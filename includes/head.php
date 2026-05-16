<!-- includes/head.php -->
<!-- Librairies externes -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder@2.4.0/dist/Control.Geocoder.css" />
<script src="https://unpkg.com/leaflet-control-geocoder@2.4.0/dist/Control.Geocoder.js"></script>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
<script src="https://unpkg.com/topojson-client@3"></script>

<!-- CSS global de l'application -->
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'Inter', sans-serif;
        background: #0a0f1a;
        color: #eef2ff;
        overflow: hidden;
        height: 100vh;
    }

    .app {
        display: flex;
        height: 100vh;
        overflow: hidden;
        position: relative;
    }

    /* Scrollbars globales */
    .sidebar::-webkit-scrollbar,
    .scrollable-section::-webkit-scrollbar,
    .transitions-list::-webkit-scrollbar {
        width: 6px;
    }
    .sidebar::-webkit-scrollbar-track,
    .scrollable-section::-webkit-scrollbar-track,
    .transitions-list::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
    }
    .sidebar::-webkit-scrollbar-thumb,
    .scrollable-section::-webkit-scrollbar-thumb,
    .transitions-list::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 4px;
    }

    /* Modales - base commune */
    .modal {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.95);
        backdrop-filter: blur(8px);
        z-index: 1000;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .modal-content {
        background: #1a2633;
        border-radius: 32px;
        width: 100%;
        max-width: 90vw;
        max-height: 85vh;
        overflow-y: auto;
        padding: 24px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .modal-large { max-width: 1000px; width: 95%; }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        flex-wrap: wrap;
        gap: 10px;
    }

    .modal-header h3 {
        font-size: clamp(1rem, 5vw, 1.3rem);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .close-modal {
        background: none;
        border: none;
        color: white;
        font-size: 1.8rem;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.2s;
        padding: 0 10px;
    }
    .close-modal:active  { transform: scale(0.9); }
    .close-modal:hover   { opacity: 1; }

    /* Spinner de chargement */
    .loading-spinner {
        display: inline-block;
        width: 20px; height: 20px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Décennies - tableau partagé */
    .decade-table {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 16px;
        overflow-x: auto;
        margin-top: 15px;
    }
    .decade-row {
        display: grid;
        grid-template-columns: 70px 1fr 1fr 1fr;
        padding: 10px 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        font-size: clamp(0.7rem, 2.5vw, 0.75rem);
        min-width: 400px;
    }
    .decade-header {
        background: rgba(59, 130, 246, 0.2);
        font-weight: 600;
        color: #93c5fd;
    }

    /* Transitions - style partagé */
    .transition-item {
        background: rgba(255, 255, 255, 0.04);
        margin: 8px 0;
        padding: 10px 12px;
        border-radius: 10px;
        border-left: 3px solid #f59e0b;
        font-size: clamp(0.75rem, 2.5vw, 0.8rem);
        word-break: break-word;
    }

    /* Section title partagé */
    .section-title {
        font-size: clamp(0.7rem, 3vw, 0.8rem);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #9ca3af;
        margin: 20px 20px 12px 20px;
    }

    /* Responsive mobile */
    @media (max-width: 768px) {
        .app { flex-direction: column; }
        .modal-content { padding: 16px; max-height: 90vh; }
        .modal-header h3 { font-size: 1rem; }
    }

    @media (prefers-color-scheme: dark) {
        body { background: #0a0f1a; }
    }

    @media (hover: none) and (pointer: coarse) {
        .close-modal { -webkit-tap-highlight-color: transparent; }
    }
</style>

<!-- Variables JS globales partagées entre tous les modules -->
<script>
    // État de l'application
    let map, marker, currentData = null, evolutionChart = null;
    let currentEvoChartType = 'temp';
    let currentLat = 47.73, currentLon = 7.33, currentNom = "Zillisheim";

    // Couche TopoJSON
    let topoLayer = null;
    let topoVisible = false;

    // Couleurs Köppen
    const KOPPEN_COLORS = {
        'Af': '#0000FF', 'Am': '#1515FF', 'Aw': '#41A3FF',
        'BWh': '#FF0000', 'BWk': '#FF9E9E', 'BSh': '#F5A300', 'BSk': '#FFCC00',
        'Csa': '#FFFF00', 'Csb': '#C6C600', 'Csc': '#969600',
        'Cwa': '#96FF96', 'Cwb': '#63C663', 'Cwc': '#329632',
        'Cfa': '#C6FF4E', 'Cfb': '#66BB33', 'Cfc': '#338822',
        'Dsa': '#FF00FF', 'Dsb': '#C600C6', 'Dsc': '#960096', 'Dsd': '#963296',
        'Dwa': '#B191FF', 'Dwb': '#7A5ACD', 'Dwc': '#4B2A8F', 'Dwd': '#32145A',
        'Dfa': '#00FFFF', 'Dfb': '#38C2C2', 'Dfc': '#1E7D7D', 'Dfd': '#165050',
        'ET': '#B2B2B2', 'EF': '#666666',
    };

    // Descriptions Köppen complètes

const descriptions = {
    // Climat équatorial (A)
    'Af':  '\u{1F334} Forêt tropicale humide – Température moyenne > 18°C chaque mois, précipitations > 60 mm chaque mois',
    'Am':  '\u{1F327}\u{FE0F} Mousson tropicale – Comme Af, mais avec une saison sèche courte',
    'Aw':  '\u{1F992} Savane tropicale – Saison sèche hivernale',
    'As':  '\u{1F3DD}\u{FE0F} Savane à été sec – Saison sèche estivale',

    // Climat sec (B)
    'BWh': '\u{1F3DC}\u{FE0F} Désert chaud – Précipitations annuelles < 50% du seuil d\'aridité, température annuelle > 18°C',
    'BWk': '\u{1F3DC}\u{FE0F} Désert froid – Précipitations annuelles < 50% du seuil d\'aridité, température annuelle < 18°C',
    'BSh': '\u{1F33E} Steppe chaude – Précipitations entre 50% et 100% du seuil d\'aridité, température > 18°C',
    'BSk': '\u{2744}\u{FE0F} Steppe froide – Précipitations entre 50% et 100% du seuil d\'aridité, température < 18°C',

    // Climat tempéré chaud (C)
    'Cfa': '\u{1F333} Subtropical humide – Été chaud (> 22°C), sans saison sèche',
    'Cfb': '\u{1F33F} Océanique tempéré – Été doux (< 22°C), pluies régulières toute l\'année',
    'Cfc': '\u{1F343} Océanique frais – Été court et frais, pluies abondantes',
    'Csa': '\u{1F34B} Méditerranéen chaud – Été chaud et sec, hiver doux et humide',
    'Csb': '\u{1F34A} Méditerranéen doux – Été sec et doux, hiver tempéré',
    'Csc': '\u{1F34E} Méditerranéen froid – Été frais et sec, hiver froid',
    'Cwa': '\u{1F33E} Subtropical à hiver sec – Été chaud, saison sèche en hiver',
    'Cwb': '\u{1F33F} Subtropical à hiver sec – Été doux, saison sèche en hiver',
    'Cwc': '\u{2744}\u{FE0F} Subtropical à hiver sec et frais – Été frais, saison sèche en hiver',

    // Climat continental (D)
    'Dfa': '\u{2744}\u{FE0F} Continental chaud – Été chaud (> 22°C), hiver froid (< -3°C)',
    'Dfb': '\u{2744}\u{FE0F} Continental humide – Été doux, hiver froid, neige abondante',
    'Dfc': '\u{2744}\u{FE0F} Subarctique – Été court, hiver très froid, précipitations modérées',
    'Dfd': '\u{2744}\u{FE0F} Sibérien – Hiver extrêmement froid, été très court',
    'Dsa': '\u{2744}\u{FE0F} Continental à été sec – Été chaud et sec, hiver froid',
    'Dsb': '\u{2744}\u{FE0F} Continental à été sec – Été sec et doux, hiver froid',
    'Dsc': '\u{2744}\u{FE0F} Continental à été sec et frais – Été frais et sec, hiver froid',
    'Dsd': '\u{2744}\u{FE0F} Continental à été sec et très froid – Été sec, hiver extrêmement froid',
    'Dwa': '\u{2744}\u{FE0F} Continental à hiver sec – Été chaud, hiver sec et froid',
    'Dwb': '\u{2744}\u{FE0F} Continental à hiver sec – Été doux, hiver sec et froid',
    'Dwc': '\u{2744}\u{FE0F} Continental à hiver sec et frais – Été frais, hiver sec et froid',
    'Dwd': '\u{2744}\u{FE0F} Continental à hiver sec et très froid – Été très court, hiver sec et extrêmement froid',

    // Climat polaire (E)
    'ET':  '\u{1F3D4}\u{FE0F} Toundra – Été frais (< 10°C max), pergélisol',
    'EF':  '\u{1F9CA} Inlandsis – Tous les mois < 0°C, glace permanente'

};

    // Utilitaire : afficher une erreur dans la sidebar
    function afficherErreur(message) {
        const errorDiv = document.getElementById('errorMessage');
        document.getElementById('errorText').innerText = message;
        errorDiv.style.display = 'block';
        document.getElementById('currentCard').style.display = 'none';
        setTimeout(() => errorDiv.style.display = 'none', 5000);
    }

    // Utilitaire : fermer une modale
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
        if (evolutionChart) { evolutionChart.destroy(); evolutionChart = null; }
    }

    // Redimensionnement
    window.addEventListener('resize', function () {
        if (evolutionChart) evolutionChart.render();
    });
</script>
