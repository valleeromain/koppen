<!-- includes/modal_analysis.php -->
<style>
    /* ===== MODALE ANALYSE IA ===== */
    .analysis-text {
        line-height: 1.7;
        font-size: clamp(0.85rem, 3vw, 0.95rem);
        color: #d1d5db;
    }

    .analysis-text strong { color: #fbbf24; }

    .analysis-loading {
        text-align: center;
        padding: 40px 20px;
        color: #9ca3af;
    }

    .analysis-loading .loading-spinner {
        width: 32px;
        height: 32px;
        border-width: 3px;
        margin-bottom: 16px;
    }
/* Dans includes/modal_analysis.php */
.modal {
    z-index: 9999 !important; /* Supérieur au 1000 de la légende */
    background-color: rgba(0, 0, 0, 0.8); /* Assure une opacité totale pour cacher le fond */
}

/* Si la légende persiste par transparence */
#analysisModal {
    backdrop-filter: blur(15px); /* Floute la légende derrière si elle dépasse */
}
    .analysis-error {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 12px;
        padding: 16px;
        color: #f87171;
        text-align: center;
    }

    .analysis-meta {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 20px;
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        font-size: 0.8rem;
        color: #9ca3af;
    }

    .analysis-meta span { display: flex; align-items: center; gap: 6px; }

    @media (max-width: 480px) {
        .analysis-meta { flex-direction: column; }
    }
</style>

<!-- Modale -->
<div id="analysisModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-robot"></i> Analyse climatique par IA</h3>
            <button class="close-modal" onclick="closeModal('analysisModal')">&times;</button>
        </div>

        <!-- Métadonnées du lieu analysé -->
        <div id="analysisMeta" class="analysis-meta" style="display:none;">
            <span><i class="fas fa-map-marker-alt"></i> <span id="metaNom">-</span></span>
            <span><i class="fas fa-thermometer-half"></i> <span id="metaCode">-</span></span>
            <span><i class="fas fa-robot" style="color:#a78bfa;"></i> Llama 3.3 via Groq</span>
        </div>

        <!-- Contenu généré -->
        <div id="analysisContent">
            <div class="analysis-loading">
                <div class="loading-spinner"></div>
                <div>Génération de l'analyse en cours…</div>
            </div>
        </div>
    </div>
</div>

<script>
    // ===== LOGIQUE MODALE ANALYSE IA =====

    /**
     * Ouvre la modale d'analyse IA et déclenche la génération
     * via l'API Groq (api_groq.php).
     */
    async function genererAnalyse() {
        if (!currentData?.current) {
            afficherErreur('Chargez d\'abord un lieu');
            return;
        }

        // Ouvrir la modale et afficher le spinner
        document.getElementById('analysisModal').style.display = 'flex';
        document.getElementById('analysisMeta').style.display = 'none';
        document.getElementById('analysisContent').innerHTML = `
            <div class="analysis-loading">
                <div class="loading-spinner"></div>
                <div>Génération de l'analyse en cours…</div>
            </div>`;

        try {
            const payload = {
                data: {
                    nom:           currentNom,
                    lat:           currentLat.toFixed(2),
                    lon:           currentLon.toFixed(2),
                    code_actuel:   currentData.current.koppen_code,
                    temp_moyenne:  currentData.current.temp_moy,
                    precip_totale: currentData.current.precip_tot,
                    trend:         currentData.trend_per_decade,
                    transitions:   currentData.transitions?.map(t => `${t.year}: ${t.from}→${t.to}`) || []
                }
            };

            const response = await fetch('api_groq.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            // Afficher les métadonnées du lieu
            document.getElementById('metaNom').textContent  = currentNom;
            document.getElementById('metaCode').textContent = currentData.current.koppen_code;
            document.getElementById('analysisMeta').style.display = 'flex';

            if (result.success) {
                // Formater le Markdown léger retourné par le LLM
                const formatted = result.analysis
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\n\n/g, '</p><p>')
                    .replace(/\n/g, '<br>');

                document.getElementById('analysisContent').innerHTML =
                    `<div class="analysis-text"><p>${formatted}</p></div>`;
            } else {
                document.getElementById('analysisContent').innerHTML = `
                    <div class="analysis-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        ${result.error || 'Une erreur est survenue lors de la génération.'}
                        ${result.fallback
                            ? `<hr style="margin:12px 0; border-color:rgba(239,68,68,0.3);">
                               <div style="color:#d1d5db; font-size:0.85rem;">${result.fallback}</div>`
                            : ''}
                    </div>`;
            }
        } catch (e) {
            document.getElementById('analysisContent').innerHTML = `
                <div class="analysis-error">
                    <i class="fas fa-wifi"></i> Erreur de connexion : ${e.message}
                </div>`;
        }
    }
</script>
