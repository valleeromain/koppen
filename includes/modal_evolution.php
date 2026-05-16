<!-- includes/modal_evolution.php -->
<style>
    /* ===== MODALE ÉVOLUTION ===== */
    .chart-container-large {
        height: clamp(250px, 40vh, 350px);
        margin: 20px 0;
    }

    .button-group {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .toggle-chart-btn {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 8px 16px;
        color: white;
        cursor: pointer;
        font-size: clamp(0.7rem, 3vw, 0.8rem);
        transition: all 0.2s;
    }
    .toggle-chart-btn:active { transform: scale(0.95); }
    .toggle-chart-btn.active { background: #3b82f6; border-color: #3b82f6; }

    .stats-panel {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 15px;
        margin-top: 20px;
        padding: 15px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 16px;
    }
    .stat-item { text-align: center; }
    .stat-item-label { font-size: clamp(0.65rem, 2.5vw, 0.7rem); color: #9ca3af; }
    .stat-item-value { font-size: clamp(0.9rem, 4vw, 1.1rem); font-weight: 600; color: #64b5f6; }

    .transitions-list { max-height: 300px; overflow-y: auto; }

    .transition-item-expanded {
        padding: 12px 14px;
        margin: 10px 0;
    }
    .transition-header {
        font-size: 0.75rem;
        color: #9ca3af;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .transition-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .transition-code {
        font-family: 'Monaco', monospace;
        font-size: 0.85rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 8px;
        background: rgba(245, 158, 11, 0.15);
        border: 1px solid rgba(245, 158, 11, 0.35);
        color: #fbbf24;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .koppen-to {
        background: rgba(16, 185, 129, 0.15);
        border-color: rgba(16, 185, 129, 0.35);
        color: #34d399;
    }
    .transition-desc {
        font-size: 0.78rem;
        color: #d1d5db;
        line-height: 1.4;
    }
    .transition-arrow-line {
        text-align: center;
        margin: 4px 0;
        padding-left: 2px;
    }

    @media (max-width: 768px) {
        .button-group { gap: 6px; }
        .toggle-chart-btn { padding: 6px 12px; font-size: 0.7rem; }
    }
</style>

<!-- Modale -->
<div id="evolutionModal" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3><i class="fas fa-chart-line"></i> Évolution historique du climat</h3>
            <button class="close-modal" onclick="closeModal('evolutionModal')">&times;</button>
        </div>

        <div class="button-group">
            <button id="evoBtnTemp"   class="toggle-chart-btn active" onclick="toggleEvoChartType('temp')">
                <i class="fas fa-temperature-high"></i> Températures
            </button>
            <button id="evoBtnPrecip" class="toggle-chart-btn" onclick="toggleEvoChartType('precip')">
                <i class="fas fa-tint"></i> Précipitations
            </button>
            <button id="evoBtnBoth"   class="toggle-chart-btn" onclick="toggleEvoChartType('both')">
                <i class="fas fa-chart-line"></i> Comparaison
            </button>
        </div>

        <div id="evolutionChartContainer" class="chart-container-large"></div>

        <div class="stats-panel">
            <div class="stat-item">
                <div class="stat-item-label">Période</div>
                <div class="stat-item-value" id="evoPeriod">-</div>
            </div>
            <div class="stat-item">
                <div class="stat-item-label">Température moyenne</div>
                <div class="stat-item-value" id="evoAvgTemp">-</div>
            </div>
            <div class="stat-item">
                <div class="stat-item-label">Précipitations totales</div>
                <div class="stat-item-value" id="evoTotalPrecip">-</div>
            </div>
            <div class="stat-item">
                <div class="stat-item-label">Tendance</div>
                <div class="stat-item-value" id="evoTrend">-</div>
            </div>
        </div>

        <div class="section-title" style="margin-top:20px;">
            <i class="fas fa-list"></i> Détail des transitions
        </div>
        <div id="modalTransitionsList" class="transitions-list"></div>

        <div class="section-title" style="margin-top:20px;">
            <i class="fas fa-calendar-alt"></i> Décennies
        </div>
        <div id="modalDecadesList"></div>
    </div>
</div>

<script>
    // ===== LOGIQUE MODALE ÉVOLUTION =====

    /**
     * Ouvre la modale d'évolution historique et initialise le graphique.
     */
    function ouvrirModalEvolution() {
        if (!currentData?.series?.length) {
            afficherErreur('Aucune donnée historique disponible');
            return;
        }

        document.getElementById('evolutionModal').style.display = 'flex';

        // Transitions
        document.getElementById('modalTransitionsList').innerHTML =
            currentData.transitions?.length
                ? currentData.transitions.map(t =>
                    `<div class="transition-item transition-item-expanded">
                        <div class="transition-header">
                            <i class="fas fa-calendar-alt" style="color:#f59e0b;"></i>
                            <strong style="color:#fbbf24;">${t.year}</strong>
                        </div>
                        <div class="transition-row">
                            <span class="transition-code koppen-from">${t.from}</span>
                            <span class="transition-desc">${descriptions[t.from] || t.from}</span>
                        </div>
                        <div class="transition-arrow-line">
                            <i class="fas fa-arrow-down" style="color:#f59e0b; font-size:0.8rem;"></i>
                        </div>
                        <div class="transition-row">
                            <span class="transition-code koppen-to">${t.to}</span>
                            <span class="transition-desc">${descriptions[t.to] || t.to}</span>
                        </div>
                    </div>`
                  ).join('')
                : '<div class="transition-item">â Aucune transition détectée</div>';

        // Décennies
        if (currentData.decades?.length) {
            let html = `<div class="decade-table">
                <div class="decade-row decade-header">
                    <span>Décennie</span><span>Temp.</span><span>Precip.</span><span>Climat</span>
                </div>`;
            for (const d of currentData.decades) {
                html += `<div class="decade-row">
                    <span><strong>${d.decade}s</strong></span>
                    <span>${d.temp_avg}°C</span>
                    <span>${d.precip_avg} mm</span>
                    <span>${d.dominant_code}</span>
                </div>`;
            }
            html += `</div>`;
            document.getElementById('modalDecadesList').innerHTML = html;
        }

        // Graphique (léger délai pour laisser le DOM se rendre)
        setTimeout(() => creerGraphiqueEvolution('temp'), 100);
    }

    /**
     * Bascule entre les types de graphique et met à jour le bouton actif.
     */
    function toggleEvoChartType(type) {
        currentEvoChartType = type;
        ['evoBtnTemp', 'evoBtnPrecip', 'evoBtnBoth'].forEach(id =>
            document.getElementById(id).classList.remove('active')
        );
        document.getElementById(
            `evoBtn${type.charAt(0).toUpperCase() + type.slice(1)}`
        ).classList.add('active');
        creerGraphiqueEvolution(type);
    }

    /**
     * Formate un nombre : 0 décimale si entier, 1 décimale sinon.
     * Ex : 12.0 → "12"  |  12.3 → "12.3"
     */
    function fmt(val) {
        const n = Math.round(val * 10) / 10;
        return Number.isInteger(n) ? n.toString() : n.toFixed(1);
    }

    /**
     * Crée (ou recrée) le graphique ApexCharts selon le type demandé :
     * 'temp', 'precip' ou 'both'.
     */
    function creerGraphiqueEvolution(type) {
        if (!currentData?.series) return;

        const years   = currentData.series.map(s => s.year);
        const temps   = currentData.series.map(s => s.temp_moy);
        const precips = currentData.series.map(s => s.precip_tot);

        const avgTemp     = fmt(temps.reduce((a, b) => a + b, 0) / temps.length);
        const totalPrecip = Math.round(precips.reduce((a, b) => a + b, 0));
        const trend       = currentData.trend_per_decade || 0;

        document.getElementById('evoPeriod').innerHTML      = `${years[0]} – ${years[years.length - 1]}`;
        document.getElementById('evoAvgTemp').innerHTML     = `${avgTemp}°C`;
        document.getElementById('evoTotalPrecip').innerHTML = `${totalPrecip} mm`;
        document.getElementById('evoTrend').innerHTML       =
            `${trend > 0 ? '+' : ''}${fmt(trend)}°C/décennie`;

        if (evolutionChart) evolutionChart.destroy();

        // Options communes
        const baseOptions = {
            chart: { height: 350, toolbar: { show: true }, background: 'transparent' },
            xaxis: { categories: years, labels: { rotate: -45, style: { colors: '#9ca3af' } } },
            theme: { mode: 'dark' },
            grid: { borderColor: 'rgba(255,255,255,0.1)' },
            tooltip: { theme: 'dark' }
        };

        // Formatters axes Y avec fmt()
        const fmtTemp   = { labels: { formatter: v => fmt(v) + '\u00b0C' } };
        const fmtPrecip = { labels: { formatter: v => Math.round(v) + ' mm' } };

        let options;
        if (type === 'temp') {
            options = {
                ...baseOptions,
                series: [{ name: 'Température (°C)', data: temps, type: 'line' }],
                chart:   { ...baseOptions.chart, type: 'line' },
                stroke:  { curve: 'smooth', width: 3, colors: ['#f97316'] },
                markers: { size: 4 },
                fill:    { type: 'gradient' },
                yaxis:   { ...fmtTemp, title: { text: '\u00b0C', style: { color: '#9ca3af' } } },
                title:   { text: 'Évolution des températures', style: { color: '#eef2ff' } }
            };
        } else if (type === 'precip') {
            options = {
                ...baseOptions,
                series:      [{ name: 'Précipitations (mm)', data: precips, type: 'bar' }],
                chart:       { ...baseOptions.chart, type: 'bar' },
                plotOptions: { bar: { borderRadius: 6, columnWidth: '70%' } },
                dataLabels:  { enabled: false },
                colors:      ['#3b82f6'],
                yaxis:       { ...fmtPrecip, title: { text: 'mm', style: { color: '#9ca3af' } } },
                title:       { text: 'Évolution des précipitations', style: { color: '#eef2ff' } }
            };
        } else {
            options = {
                ...baseOptions,
                series: [
                    { name: 'Température (°C)',    data: temps,   type: 'line' },
                    { name: 'Précipitations (mm)', data: precips, type: 'bar'  }
                ],
                stroke: { curve: 'smooth', width: 3 },
                colors: ['#f97316', '#3b82f6'],
                yaxis: [
                    { ...fmtTemp,   title: { text: '\u00b0C', style: { color: '#f97316' } } },
                    { ...fmtPrecip, title: { text: 'mm',      style: { color: '#3b82f6' } }, opposite: true }
                ],
                title: { text: 'Comparaison températures / précipitations', style: { color: '#eef2ff' } }
            };
        }

        evolutionChart = new ApexCharts(
            document.getElementById('evolutionChartContainer'),
            options
        );
        evolutionChart.render();
    }
</script>
