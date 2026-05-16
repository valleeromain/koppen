<!-- includes/sidebar.php -->
<style>
    /* ===== RESET & BASE ===== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* ===== SIDEBAR ===== */
    .sidebar {
        width: 420px;
        height: 100dvh;
        background: rgba(15, 25, 35, 0.95);
        backdrop-filter: blur(16px);
        border-right: 1px solid rgba(255, 255, 255, 0.08);
        display: flex;
        flex-direction: column;
        z-index: 10;
        box-shadow: 4px 0 30px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
    }

    /* Conteneur principal pour TOUT le contenu défilable */
    .sidebar-content {
        flex: 1;
        overflow-y: auto; /* Défilement géré ici */
        padding: 0;
        min-height: 0; /* Permet de rétrécir */
    }

    /* ===== HEADER ===== */
    .sidebar-header {
        padding: 24px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        background: linear-gradient(135deg, rgba(0, 100, 200, 0.2), rgba(0, 150, 255, 0.05));
    }

    .sidebar-header h1 {
        font-size: clamp(1.3rem, 5vw, 1.6rem);
        font-weight: 600;
        background: linear-gradient(135deg, #fff, #64b5f6);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        letter-spacing: -0.3px;
        margin: 0;
    }

    .sidebar-header p {
        font-size: clamp(0.7rem, 3vw, 0.8rem);
        color: #9ca3af;
        margin-top: 6px;
    }

    /* ===== SEARCH SECTION (désormais dans sidebar-content) ===== */
    .search-section {
        padding: 20px;
        background: rgba(0, 0, 0, 0.2);
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .info-field {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 14px;
        padding: 12px 16px;
        color: white;
        font-size: clamp(0.85rem, 3vw, 0.9rem);
        width: 100%;
        cursor: default;
        word-break: break-word;
    }

    .info-label {
        font-size: clamp(0.65rem, 2.5vw, 0.7rem);
        color: #9ca3af;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-group {
        margin-bottom: 12px;
    }

    .btn {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border: none;
        border-radius: 14px;
        padding: 12px 20px;
        color: white;
        font-weight: 500;
        cursor: pointer;
        transition: transform 0.1s, box-shadow 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: clamp(0.8rem, 3vw, 0.85rem);
        width: 100%;
        justify-content: center;
    }

    .btn:active {
        transform: scale(0.98);
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }

    /* ===== CLIMATE BADGE ===== */
    .climate-badge-modern {
        background: linear-gradient(135deg, #1a2a3a, #0f1a24);
        border-radius: 24px;
        padding: 20px;
        margin: 20px;
        text-align: center;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .climate-code-large {
        font-size: clamp(2.5rem, 8vw, 3.5rem);
        font-weight: 700;
        font-family: 'Monaco', monospace;
        letter-spacing: 2px;
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    .climate-desc {
        font-size: clamp(0.85rem, 3vw, 1rem);
        margin-top: 8px;
        color: #d1d5db;
    }

    /* ===== STATS GRID ===== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 12px;
        margin: 20px;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        padding: 14px;
        text-align: center;
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        cursor: pointer;
        transition: all 0.2s;
    }

    .stat-card:active {
        transform: scale(0.97);
    }

    .stat-card:hover {
        background: rgba(59, 130, 246, 0.2);
        transform: translateY(-2px);
    }

    .stat-value {
        font-size: clamp(1.3rem, 5vw, 1.6rem);
        font-weight: 700;
        color: #64b5f6;
    }

    .stat-label {
        font-size: clamp(0.65rem, 2.5vw, 0.7rem);
        color: #9ca3af;
        margin-top: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ===== ACTION BUTTONS ===== */
    .action-buttons {
        display: flex;
        gap: 12px;
        margin: 0 20px 20px 20px;
        flex-wrap: wrap;
    }

    .action-btn {
        flex: 1;
        min-width: 100px;
        background: rgba(59, 130, 246, 0.15);
        border: 1px solid rgba(59, 130, 246, 0.3);
        border-radius: 16px;
        padding: 12px 8px;
        color: white;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: clamp(0.75rem, 3vw, 0.85rem);
    }

    .action-btn:hover {
        background: rgba(59, 130, 246, 0.3);
        transform: translateY(-2px);
    }

    .action-btn.evolution {
        background: rgba(16, 185, 129, 0.15);
        border-color: rgba(16, 185, 129, 0.3);
    }

    .action-btn.evolution:hover {
        background: rgba(16, 185, 129, 0.3);
    }

    .action-btn.analysis {
        background: rgba(139, 92, 246, 0.15);
        border-color: rgba(139, 92, 246, 0.3);
    }

    .action-btn.analysis:hover {
        background: rgba(139, 92, 246, 0.3);
    }

    .action-btn.topo {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border: 2px solid rgba(245, 158, 11, 0.8);
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }

    .action-btn.topo.active {
        background: linear-gradient(135deg, #10b981, #059669);
        border-color: rgba(16, 185, 129, 0.8);
    }

    /* ===== ERROR & SECTIONS ===== */
    .error-badge {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 12px;
        padding: 12px;
        margin: 20px;
        text-align: center;
        color: #f87171;
    }

    .section-title {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #9ca3af;
        margin: 20px 20px 10px 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .coords-info {
        display: flex;
        gap: 12px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }

    .coords-info .info-group {
        flex: 1;
        margin-bottom: 0;
        min-width: 120px;
    }

    /* ===== SCROLLBAR CUSTOM ===== */
    .sidebar-content::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-content::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
    }

    .sidebar-content::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 4px;
    }

    .sidebar-content::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.35);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .sidebar {
            width: 100%;
            height: auto;
            max-height: 45vh;
            min-height: 250px;
            border-right: none;
            order: 2;
        }

        .sidebar-content {
            max-height: 45vh;
            overflow-y: auto;
        }

        .search-section {
            padding: 12px 16px;
        }

        .action-buttons {
            flex-direction: row;
            flex-wrap: wrap;
            margin: 0 12px 12px 12px;
        }

        .action-btn {
            padding: 10px 6px;
            font-size: 0.75rem;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            margin: 12px;
        }

        .stat-card {
            padding: 10px;
        }

        .climate-badge-modern {
            padding: 16px;
            margin: 12px;
        }

        .sidebar-header {
            padding: 16px;
        }

        .section-title {
            margin: 12px 12px 8px 12px;
        }
    }

    @media (max-width: 480px) {
        .sidebar {
            max-height: 40vh;
            min-height: 200px;
        }

        .sidebar-content {
            max-height: 40vh;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
        }

        .action-buttons {
            margin: 0 10px 10px 10px;
            gap: 10px;
        }

        .action-btn {
            padding: 10px 6px;
            margin-bottom: 8px;
        }

        .action-btn.topo {
            padding: 12px 6px;
            margin-bottom: 10px;
        }
    }
</style>

<!-- ===== HTML STRUCTURE ===== -->
<div class="sidebar">
    <!-- Header (fixe en haut) -->
    <div class="sidebar-header">
        <h1><i class="fas fa-globe-europe"></i> ClimateWatch</h1>
        <p>Suivi des zones climatiques • Classification de Köppen</p>
    </div>

    <!-- Conteneur défilable (tout le reste) -->
    <div class="sidebar-content">
        <!-- Section de recherche (désormais dans sidebar-content) -->
        <div class="search-section">
            <div class="info-group">
                <div class="info-label"><i class="fas fa-map-marker-alt"></i> Lieu sélectionné</div>
                <div class="info-field" id="nomAffichage">Zillisheim</div>
            </div>

            <div class="coords-info">
                <div class="info-group">
                    <div class="info-label"><i class="fas fa-arrow-up"></i> Latitude</div>
                    <div class="info-field" id="latAffichage">47.73</div>
                </div>
                <div class="info-group">
                    <div class="info-label"><i class="fas fa-arrow-right"></i> Longitude</div>
                    <div class="info-field" id="lonAffichage">7.33</div>
                </div>
            </div>

            <div class="recherche-btn-group" style="margin-top:15px;">
                <button class="btn" style="cursor: default; opacity: 0.8;">
                    <i class="fas fa-search"></i> <span>Cliquez sur la carte pour explorer</span>
                </button>
            </div>
        </div>

        <!-- Badge Köppen -->
        <div id="currentCard">
            <div class="climate-badge-modern">
                <div class="climate-code-large" id="currentCode">---</div>
                <div class="climate-desc" id="currentDesc">Chargement...</div>
                <div style="margin-top:16px; font-size:0.8rem; color:#9ca3af;" id="currentYear"></div>
            </div>

            <div class="stats-grid">
                <div class="stat-card" onclick="ouvrirModalEvolution()">
                    <div class="stat-value" id="currentTemp">-</div>
                    <div class="stat-label">Temp. Moyenne <i class="fas fa-chart-line"></i></div>
                </div>
                <div class="stat-card" onclick="ouvrirModalEvolution()">
                    <div class="stat-value" id="currentPrecip">-</div>
                    <div class="stat-label">Précip. Annuelles <i class="fas fa-chart-bar"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="trend">-</div>
                    <div class="stat-label">Tendance / décennie</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="pointInfo">-</div>
                    <div class="stat-label">Point ERA5</div>
                </div>
            </div>
        </div>

        <!-- Erreurs -->
        <div id="errorMessage" class="error-badge" style="display:none;">
            <i class="fas fa-exclamation-triangle"></i> <span id="errorText"></span>
        </div>

        <!-- Boutons d'action -->
        <div class="action-buttons">
            <button class="action-btn evolution" onclick="ouvrirModalEvolution()">
                <i class="fas fa-chart-line"></i> <span>Évolution</span>
            </button>
            <button class="action-btn analysis" onclick="genererAnalyse()">
                <i class="fas fa-brain"></i> <span>Analyse IA</span>
            </button>
            <button class="action-btn topo" id="toggleTopoBtn" onclick="toggleTopoLayer()">
                <i class="fas fa-layer-group"></i> <span>Carte Köppen</span>
            </button>
        </div>

        <!-- Transitions et décennies -->
        <div class="section-title"><i class="fas fa-exchange-alt"></i> Transitions climatiques</div>
        <div id="transitionsList" style="min-height:50px; margin:0 20px;">
            <div class="transition-item" style="color:#6b7280; font-size: 0.8rem;">Sélectionnez un point...</div>
        </div>

        <div class="section-title"><i class="fas fa-calendar-alt"></i> Moyennes par décennie</div>
        <div id="decadesList" style="margin:0 20px;"></div>
    </div>
</div>

<script>
    /**
     * Charge les données climatiques pour le lieu courant
     */
    async function chargerLieu() {
        const currentCard = document.getElementById('currentCard');
        const errorMsg = document.getElementById('errorMessage');
        const transList = document.getElementById('transitionsList');
        const decList = document.getElementById('decadesList');

        // Affiche toujours le conteneur (même vide)
        currentCard.style.display = 'block';
        errorMsg.style.display = 'none';
        transList.innerHTML = `<div class="transition-item"><i class="fas fa-spinner fa-pulse"></i> Chargement des données...</div>`;
        decList.innerHTML = '';

        try {
            const response = await fetch(`api_climate.php?lat=${currentLat}&lon=${currentLon}`);
            if (!response.ok) throw new Error(`Erreur serveur (${response.status})`);

            const data = await response.json();
            currentData = data;

            if (data.error) {
                afficherErreur(data.error);
                return;
            }

            // Mise à jour Interface Principale
            document.getElementById('currentCode').textContent = data.current.koppen_code || 'N/A';
            document.getElementById('currentDesc').textContent = descriptions[data.current.koppen_code] || "Climat non répertorié";
            document.getElementById('currentTemp').textContent = (data.current.temp_moy || 0).toFixed(1) + '°C';
            document.getElementById('currentPrecip').textContent = (data.current.precip_tot || 0).toFixed(0) + ' mm';
            document.getElementById('currentYear').textContent = "Année de référence : " + (data.current.year || '2023');
            document.getElementById('pointInfo').textContent = `${currentLat.toFixed(2)}° / ${currentLon.toFixed(2)}°`;
            document.getElementById('trend').textContent = (data.trend_per_decade > 0 ? '+' : '') + (data.trend_per_decade || 0).toFixed(2) + '°C';

            // Transitions
            if (data.transitions && data.transitions.length > 0) {
                transList.innerHTML = data.transitions.map(t => `
                    <div class="transition-item" style="background: rgba(245,158,11,0.1); padding: 10px; border-radius: 8px; margin-bottom: 8px; font-size: 0.8rem; border-left: 3px solid #f59e0b;">
                        <strong>${t.year}</strong> : ${t.from} <i class="fas fa-arrow-right" style="margin: 0 5px;"></i> ${t.to}
                    </div>
                `).join('');
            } else {
                transList.innerHTML = `<div class="transition-item" style="color:#10b981; font-size: 0.8rem;"><i class="fas fa-check-circle"></i> Climat historiquement stable.</div>`;
            }

            // Tableau des Décennies
            if (data.decades && data.decades.length > 0) {
                let tableHtml = `<div class="decade-table" style="background: rgba(0,0,0,0.2); border-radius: 12px; overflow: hidden; font-size: 0.75rem;">
                    <div class="decade-row decade-header" style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; padding: 10px; background: rgba(59,130,246,0.2); font-weight: bold; color: #93c5fd;">
                        <span>Décennie</span><span>Temp.</span><span>Précip.</span><span>Code</span>
                    </div>`;

                data.decades.forEach(d => {
                    tableHtml += `
                    <div class="decade-row" style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span>${d.decade}s</span>
                        <span>${d.temp_avg.toFixed(1)}°C</span>
                        <span>${d.precip_avg.toFixed(0)}mm</span>
                        <span style="font-weight:bold; color:#fbbf24;">${d.dominant_code}</span>
                    </div>`;
                });
                tableHtml += `</div>`;
                decList.innerHTML = tableHtml;
            }

            // Mise à jour du marqueur sur la carte
            if (marker) {
marker.setLatLng([currentLat, currentLon])
      .bindPopup(`
          <div style="min-width:200px; font-family:'Inter',sans-serif;">
              <strong style="color:#fbbf24; font-size:1.1rem;">Climat ${data.current.koppen_code}</strong><br>
              <i style="font-size:0.85rem;">Typologie : ${descriptions[data.current.koppen_code] || 'Zone climatique'}</i><br><br>
<b>Température moyenne :</b> ${(data.current.temp_moy || 0).toFixed(2)}°C<br>
<b>Précipitations totales :</b> ${(data.current.precip_tot || 0).toFixed(2)} mm<br>
              <hr style="margin:8px 0; border-color:rgba(255,255,255,0.1);">
              <span style="font-size:0.8rem; color:#64b5f6;"> ${document.getElementById('nomAffichage').innerText}</span>
          </div>
      `)
      .openPopup();
            }

        } catch (e) {
            console.error("Erreur chargerLieu:", e);
            afficherErreur("Impossible de récupérer les données climatiques.");
        }
    }

    function afficherErreur(msg) {
        const err = document.getElementById('errorMessage');
        document.getElementById('errorText').textContent = msg;
        err.style.display = 'block';
        document.getElementById('transitionsList').innerHTML = '';
    }
</script>
