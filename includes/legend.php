<!-- includes/legend.php -->
<style>
    /* ===== LÉGENDE Köppen ===== */
    .koppen-legend {
    max-height: 80vh;
    overflow-y: auto;
    scrollbar-width: thin;
}
/* Cache la légende si le conteneur de la carte est masqué */
.map-container:not(:visible) #koppenLegend {
    display: none !important;
}

/* Version plus universelle : 
   Si vous utilisez une classe "active" ou "show" sur vos sections */
#koppenLegend {
    /* ... vos styles existants ... */
    visibility: hidden; /* Par défaut caché */
    opacity: 0;
    transition: opacity 0.3s;
}

/* On ne l'affiche que si la classe 'visible-legend' est ajoutée par le JS de la carte */
#koppenLegend.active {
    visibility: visible;
    opacity: 1;
    display: block !important;
}
.koppen-legend::-webkit-scrollbar {
    width: 4px;
}

.koppen-legend::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}

.koppen-legend::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
}

.koppen-legend {
    position: absolute;
    bottom: 20px;
    right: 20px;
    z-index: 900;
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 12px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    max-width: 200px;
    font-size: 11px;
    pointer-events: auto;  /* ← ACTIVE les clics sur la légende */
    cursor: default;       /* ← Curseur par défaut pour éviter la confusion */
}

    .legend-title {
        font-size: clamp(10px, 3vw, 12px);
        font-weight: 600;
        margin-bottom: 8px;
        color: #fbbf24;
    }

    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 4px;
        font-size: clamp(9px, 2.5vw, 11px);
    }

    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 3px;
        margin-right: 8px;
        flex-shrink: 0;
    }

    @media (max-width: 480px) {
        .koppen-legend { display: none !important; }
    }

    @media (max-width: 768px) {
        .koppen-legend { bottom: 10px; right: 10px; padding: 8px; max-width: 170px; }
    }
</style>

<!-- La légende est positionnée en absolute sur la carte -->
<div id="koppenLegend" class="koppen-legend" style="display:none;">
    <div class="legend-title"><i class="fas fa-thermometer-half"></i> Classification Köppen</div>
    
    <!-- Groupe A - Tropical -->
    <div class="legend-item">
        <div class="legend-color" style="background:#0000FF;"></div>
        <span>Tropical humide (Af)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#1515FF;"></div>
        <span>Mousson tropicale (Am)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#41A3FF;"></div>
        <span>Savane tropicale (Aw/As)</span>
    </div>

    <!-- Groupe B - Sec -->
    <div class="legend-item">
        <div class="legend-color" style="background:#FF0000;"></div>
        <span>Désert chaud (BWh)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#FF9E9E;"></div>
        <span>Désert froid (BWk)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#F5A300;"></div>
        <span>Steppe chaude (BSh)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#FFCC00;"></div>
        <span>Steppe froide (BSk)</span>
    </div>

    <!-- Groupe C - Tempéré chaud -->
    <div class="legend-item">
        <div class="legend-color" style="background:#C6FF4E;"></div>
        <span>Subtropical humide (Cfa)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#66BB33;"></div>
        <span>Océanique tempéré (Cfb)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#338822;"></div>
        <span>Océanique frais (Cfc)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#FFFF00;"></div>
        <span>Méditerranéen chaud (Csa)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#C6C600;"></div>
        <span>Méditerranéen doux (Csb)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#969600;"></div>
        <span>Méditerranéen froid (Csc)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#96FF96;"></div>
        <span>Subtropical à hiver sec (Cwa)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#63C663;"></div>
        <span>Subtropical à hiver sec doux (Cwb)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#329632;"></div>
        <span>Subtropical à hiver sec frais (Cwc)</span>
    </div>

    <!-- Groupe D - Continental -->
    <div class="legend-item">
        <div class="legend-color" style="background:#00FFFF;"></div>
        <span>Continental chaud (Dfa)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#38C2C2;"></div>
        <span>Continental humide (Dfb)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#1E7D7D;"></div>
        <span>Subarctique (Dfc)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#165050;"></div>
        <span>Sibérien (Dfd)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#FF00FF;"></div>
        <span>Continental été sec chaud (Dsa)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#C600C6;"></div>
        <span>Continental été sec doux (Dsb)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#960096;"></div>
        <span>Continental été sec frais (Dsc)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#963296;"></div>
        <span>Continental été sec très froid (Dsd)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#B191FF;"></div>
        <span>Continental hiver sec chaud (Dwa)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#7A5ACD;"></div>
        <span>Continental hiver sec doux (Dwb)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#4B2A8F;"></div>
        <span>Continental hiver sec frais (Dwc)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#32145A;"></div>
        <span>Continental hiver sec très froid (Dwd)</span>
    </div>

    <!-- Groupe E - Polaire -->
    <div class="legend-item">
        <div class="legend-color" style="background:#B2B2B2;"></div>
        <span>Toundra (ET)</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background:#666666;"></div>
        <span>Inlandsis (EF)</span>
    </div>
</div>
