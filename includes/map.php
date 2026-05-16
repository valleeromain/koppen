<!-- includes/map.php -->
<style>
    /* ===== CARTE ===== */
    .map-container {
        flex: 1;
        position: relative;
        background: #0a0f1a;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    #map {
        height: 100%;
        width: 100%;
    }

    /* Marqueur personnalisé */
    .custom-marker {
        background: #ef4444;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.8);
        z-index: 1000;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .map-container { order: 1; min-height: 55vh; }
    }
    @media (max-width: 480px) {
        .map-container { min-height: 60vh; }
    }
    @media (max-width: 768px) and (orientation: landscape) {
        .map-container { min-height: 50vh; }
    }
</style>

<div class="map-container">
    <div id="map"></div>
</div>

<script>
    // ===== LOGIQUE CARTE =====

    // Garde-fou : t() de secours si i18n pas encore chargé
    if (typeof t === 'undefined') {
        window.t = function(key) {
            const fallbacks = {
                'map.topo_not_loaded': "La couche climatique n'est pas encore chargée",
                'map.topo_not_found':  "Fichier TopoJSON non trouvé.",
                'map.geocoding_loading': 'Chargement...',
                'sidebar.btn_koppen_show': 'Carte Köppen',
                'sidebar.btn_koppen_hide': 'Masquer carte',
            };
            return fallbacks[key] || key;
        };
    }

    /**
     * Initialise la carte Leaflet, place le marqueur par défaut
     * et branche l'événement clic pour charger un nouveau lieu.
     */
    function initMap() {
        // 1. Définition des différents fonds
        const dark = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO'
        });

        const light = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO'
        });

        const grey = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri'
        });

        const terrain = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Physical_Map/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri'
        });
        
        // 2. Initialisation de la carte
        map = L.map('map', {
            center: [currentLat, currentLon],
            zoom: 6,
            layers: [terrain]
        });

        // 3. Ajout du sélecteur de couches
        const baseMaps = {
            "Mode Sombre": dark,
            "Mode Clair": light,
            "Gris Neutre": grey,
            "Terrain": terrain
        };
        L.control.layers(baseMaps).addTo(map);

        const customIcon = L.divIcon({
            className: 'custom-marker',
            html: '<div class="custom-marker"></div>',
            iconSize: [12, 12],
            popupAnchor: [0, -8]
        });
        marker = L.marker([currentLat, currentLon], { icon: customIcon }).addTo(map);

        // Clic sur la carte → charger ce lieu
        map.on('click', async function (e) {
            currentLat = e.latlng.lat;
            currentLon = e.latlng.lng;
            document.getElementById('latAffichage').innerText = currentLat.toFixed(4);
            document.getElementById('lonAffichage').innerText = currentLon.toFixed(4);
            await rechercherNomParCoordonnees(currentLat, currentLon);
            chargerLieu();
        });

        // Charger la couche TopoJSON dès l'init
        chargerTopoJSON();
    }

    /**
     * Géocodage inverse : retrouve le nom d'un lieu à partir de ses coordonnées.
     */
    function rechercherNomParCoordonnees(lat, lon) {
        return new Promise((resolve) => {
            const timeoutId = setTimeout(() => {
                const fallback = `${lat.toFixed(2)}°, ${lon.toFixed(2)}°`;
                currentNom = fallback;
                document.getElementById('nomAffichage').innerHTML = fallback;
                resolve();
            }, 5000);

            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&zoom=10&addressdetails=1`)
                .then(r => r.json())
                .then(data => {
                    clearTimeout(timeoutId);
                    if (data?.display_name) {
                        let shortName =
                            data.address.city    ||
                            data.address.town    ||
                            data.address.village ||
                            data.address.hamlet  ||
                            data.address.suburb  ||
                            data.address.county  ||
                            data.address.state   ||
                            data.display_name.split(',')[0];

                        shortName = shortName.replace(/[^\w\s\-éèêëàâôûçîïù]/g, '').trim();
                        currentNom = shortName;
                        document.getElementById('nomAffichage').innerHTML =
                            `<i class="fas fa-map-marker-alt"></i> ${shortName}`;
                    } else {
                        const fallback = `${lat.toFixed(2)}°, ${lon.toFixed(2)}°`;
                        currentNom = fallback;
                        document.getElementById('nomAffichage').innerHTML =
                            `<i class="fas fa-map-marker-alt"></i> ${fallback}`;
                    }
                    resolve();
                })
                .catch(() => {
                    clearTimeout(timeoutId);
                    const fallback = `${lat.toFixed(2)}°, ${lon.toFixed(2)}°`;
                    currentNom = fallback;
                    document.getElementById('nomAffichage').innerHTML =
                        `<i class="fas fa-map-marker-alt"></i> ${fallback}`;
                    resolve();
                });
        });
    }

    /**
     * Charge le fichier TopoJSON des zones climatiques européennes
     */
    function chargerTopoJSON() {
        fetch('grid_europe.topojson')
            .then(response => {
                if (!response.ok) throw new Error('Fichier TopoJSON introuvable');
                return response.json();
            })
            .then(topology => {
                const key = Object.keys(topology.objects)[0];
                const geojsonData = topojson.feature(topology, topology.objects[key]);
                
                // DEBUG : Afficher les 5 premières features
                console.log('=== PREMIÈRES FEATURES DU TOPOJSON ===');
                for (let i = 0; i < Math.min(5, geojsonData.features.length); i++) {
                    const f = geojsonData.features[i];
                    console.log(`Feature ${i}:`, {
                        c: f.properties.c,
                        t: f.properties.t,
                        p: f.properties.p,
                        geometry_type: f.geometry.type
                    });
                }
                
                // Créer une version majuscule des couleurs
                const KOPPEN_COLORS_UPPER = {};
                Object.keys(KOPPEN_COLORS).forEach(k => {
                    KOPPEN_COLORS_UPPER[k.toUpperCase()] = KOPPEN_COLORS[k];
                });
                
                console.log('ðÂÂ¨ Mapping des codes:', Object.keys(KOPPEN_COLORS_UPPER).slice(0, 10));
                
                topoLayer = L.geoJSON(geojsonData, {
                    style: function (feature) {
                        let code = feature.properties.c || 'Unknown';
                        let originalCode = code;
                        code = code.trim().toUpperCase();
                        
                        let color = KOPPEN_COLORS_UPPER[code];
                        if (!color) {
                            console.warn(`âÂ ï¸Â Code non reconnu: "${code}" (original: "${originalCode}")`);
                            color = '#666666';
                        }
                        
                        // Log pour CFB et CSB
                        if (code === 'CFB' || code === 'CSB') {
                            console.log(`ðÂÂ¨ Style: code="${code}" -> couleur="${color}"`);
                        }
                        
                        return {
                            fillColor: color,
                            fillOpacity: 0.6,
                            weight: 0.5,
                            color: '#ffffff',
                            opacity: 0.6
                        };
                    },
                    onEachFeature: function (feature, layer) {
                        let originalCode = feature.properties.c || 'N/A';
                        let code = originalCode.trim().toUpperCase();
                        
                        // Log pour CFB et CSB
                        if (code === 'CFB' || code === 'CSB') {
                            console.log(`ðÂÂÂ Popup: code original="${originalCode}", code utilisé="${code}"`);
                        }
                        
                        // Version formatée pour l'affichage
                        let displayCode = code;
                        if (code.length === 3) {
                            displayCode = code.charAt(0) + code.slice(1).toLowerCase();
                        }
                        if (displayCode === 'Et') displayCode = 'ET';
                        if (displayCode === 'Ef') displayCode = 'EF';
                        
                        const temp = feature.properties.t || 'N/A';
                        const precip = feature.properties.p || 'N/A';

                        layer.bindPopup(`
                            <div style="min-width:200px; font-family:'Inter',sans-serif;">
                                <strong style="color:#fbbf24; font-size:1.1rem;">Climat ${displayCode}</strong><br>
                                <i style="font-size:0.85rem;">${descriptions[displayCode] || descriptions[code] || 'Zone climatique'}</i><br><br>
                                <b>&#127777; Température moyenne :</b> ${temp}°C<br>
                                <b>&#127783; Précipitations totales :</b> ${precip} mm<br>
                                <hr style="margin:8px 0; border-color:rgba(255,255,255,0.1);">
                                <span style="font-size:0.8rem; color:#64b5f6;">&#10024; Cliquez pour sélectionner cette zone</span>
                            </div>
                        `);

                        // Clic sur une zone Köppen
                        layer.on('click', async function (e) {
                            layer.closePopup();
                            let lat, lon;
                            if (feature.geometry.type === 'Polygon' && feature.geometry.coordinates[0].length > 0) {
                                const coords = feature.geometry.coordinates[0];
                                let sumLat = 0, sumLon = 0;
                                coords.forEach(c => { sumLat += c[1]; sumLon += c[0]; });
                                lat = sumLat / coords.length;
                                lon = sumLon / coords.length;
                            } else if (e.latlng) {
                                lat = e.latlng.lat;
                                lon = e.latlng.lng;
                            } else {
                                return;
                            }

                            currentLat = lat;
                            currentLon = lon;
                            document.getElementById('latAffichage').innerText = lat.toFixed(4);
                            document.getElementById('lonAffichage').innerText = lon.toFixed(4);
                            document.getElementById('nomAffichage').innerHTML =
                                '<i class="fas fa-spinner fa-pulse"></i> Chargement...';

                            await rechercherNomParCoordonnees(lat, lon);
                            await chargerLieu();

                            map.setView([lat, lon], 8);
                            marker.setLatLng([lat, lon]);
                            setTimeout(() => marker.openPopup(), 500);

                            this.setStyle({ fillOpacity: 0.7, weight: 2, color: '#fbbf24' });
                            setTimeout(() => {
                                if (topoVisible) {
                                    topoLayer.resetStyle(this);
                                }
                            }, 1000);
                        });

                        // Survol
                        layer.on('mouseover', function () {
                            if (topoVisible) {
                                this.setStyle({ fillOpacity: 0.7, weight: 1.5, color: '#fbbf24' });
                            }
                        });
                        layer.on('mouseout', function () {
                            if (topoVisible) {
                                topoLayer.resetStyle(this);
                            }
                        });
                    }
                });
                
                console.log('âÂÂ TopoJSON chargé – zones cliquables.');
            })
            .catch(err => {
                console.error('âÂÂ Erreur TopoJSON :', err);
                const errorText = document.getElementById('errorText');
                const errorMessage = document.getElementById('errorMessage');
                if (errorText) {
                    errorText.innerHTML = 'Fichier TopoJSON non trouvé. La superposition climatique n\'est pas disponible.';
                }
                if (errorMessage) {
                    errorMessage.style.display = 'block';
                    setTimeout(() => errorMessage.style.display = 'none', 5000);
                }
            });
    }

    /**
     * Affiche ou masque la couche Köppen sur la carte.
     */
    function toggleTopoLayer() {
        const btn = document.getElementById('toggleTopoBtn');
        const legend = document.getElementById('koppenLegend');

        if (!topoLayer) {
            if (typeof afficherErreur === 'function') {
                afficherErreur("La couche climatique n'est pas encore chargée");
            }
            return;
        }

        if (topoVisible) {
            map.removeLayer(topoLayer);
            topoVisible = false;
            if (btn) {
                btn.classList.remove('active');
                btn.innerHTML = '<i class="fas fa-layer-group"></i> Carte Köppen';
            }
            if (legend && window.innerWidth > 480) {
                legend.style.display = 'none';
            }
        } else {
            topoLayer.addTo(map);
            topoVisible = true;
            if (btn) {
                btn.classList.add('active');
                btn.innerHTML = '<i class="fas fa-eye-slash"></i> Masquer carte';
            }
            if (legend && window.innerWidth > 480) {
                legend.style.display = 'block';
            }
        }
    }
</script>
