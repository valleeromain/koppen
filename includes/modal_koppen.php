<!-- Modale détaillée de la classification de K&ouml;ppen -->
<div id="customKoppenModal" class="custom-modal">
    <div class="custom-modal-content">
        <span class="custom-close-btn">&times;</span>
        <h2>&#128218; Classification climatique de K&ouml;ppen</h2>
        <p>La classification de K&ouml;ppen est un système de référence mondial qui catégorise les climats selon les températures, les précipitations et les saisons.</p>

        <!-- NIVEAU 1 : Groupes principaux -->
        <h3>&#127959;&#65039; 1<sup>er</sup> niveau – Groupes principaux</h3>
        <table class="custom-koppen-table">
            <thead>
                <tr><th>Code</th><th>Type</th><th>Description</th></tr>
            </thead>
            <tbody>
                <tr><td>A</td><td>Tropical</td><td>Température moyenne &gt; 18&nbsp;°C chaque mois, précipitations abondantes.</td></tr>
                <tr><td>B</td><td>Sec (aride / semi-aride)</td><td>Précipitations annuelles inférieures à un seuil d'aridité.</td></tr>
                <tr><td>C</td><td>Tempéré</td><td>Température du mois le plus froid entre 0&nbsp;°C et 18&nbsp;°C, mois le plus chaud &gt; 10&nbsp;°C.</td></tr>
                <tr><td>D</td><td>Continental</td><td>Mois le plus froid &lt; -3&nbsp;°C, mois le plus chaud &gt; 10&nbsp;°C.</td></tr>
                <tr><td>E</td><td>Polaire</td><td>Température maximale du mois le plus chaud &lt; 10&nbsp;°C.</td></tr>
            </tbody>
        </table>

        <!-- NIVEAU 2 : R&eacute;gime des pr&eacute;cipitations -->
        <h3>&#128167; 2<sup>e</sup> niveau – R&eacute;gime des pr&eacute;cipitations</h3>
        <table class="custom-koppen-table">
            <thead>
                <tr><th>Code</th><th>Signification</th><th>Description</th></tr>
            </thead>
            <tbody>
                <tr><td>f</td><td>Humide toute l'ann&eacute;e</td><td>Pas de saison s&egrave;che (pr&eacute;cipitations r&eacute;parties).</td></tr>
                <tr><td>s</td><td>&Eacute;t&eacute; sec</td><td>La saison s&egrave;che se produit en &eacute;t&eacute; (ex. climat m&eacute;diterran&eacute;en).</td></tr>
                <tr><td>w</td><td>Hiver sec</td><td>La saison s&egrave;che se produit en hiver.</td></tr>
                <tr><td>m</td><td>Mousson</td><td>Climat tropical avec une saison des pluies tr&egrave;s marqu&eacute;e.</td></tr>
                <tr><td>S / W</td><td>Steppe / D&eacute;sert</td><td>Utilis&eacute; uniquement pour le groupe B (BS = steppe, BW = d&eacute;sert).</td></tr>
            </tbody>
        </table>

        <!-- NIVEAU 3 : R&eacute;gime thermique -->
        <h3>&#127777;&#65039; 3<sup>e</sup> niveau – R&eacute;gime thermique</h3>
        <table class="custom-koppen-table">
            <thead>
                <tr><th>Code</th><th>Signification</th><th>Crit&egrave;re (exemples)</th></tr>
            </thead>
            <tbody>
                <tr><td>a</td><td>&Eacute;t&eacute; chaud</td><td>Temp&eacute;rature du mois le plus chaud &gt; 22&nbsp;°C.</td></tr>
                <tr><td>b</td><td>&Eacute;t&eacute; doux / temp&eacute;r&eacute;</td><td>Mois le plus chaud &lt; 22&nbsp;°C, au moins 4 mois &gt; 10&nbsp;°C.</td></tr>
                <tr><td>c</td><td>&Eacute;t&eacute; frais</td><td>Moins de 4 mois &gt; 10&nbsp;°C.</td></tr>
                <tr><td>d</td><td>Hiver tr&egrave;s froid</td><td>Mois le plus froid &lt; -38&nbsp;°C (climats continentaux extr&ecirc;mes).</td></tr>
                <tr><td>h</td><td>Chaud (d&eacute;sert / steppe)</td><td>Temp&eacute;rature annuelle &gt; 18&nbsp;°C (pour groupe B).</td></tr>
                <tr><td>k</td><td>Froid (d&eacute;sert / steppe)</td><td>Temp&eacute;rature annuelle &lt; 18&nbsp;°C (pour groupe B).</td></tr>
            </tbody>
        </table>

        <h3>&#128269; Exemples de codes complets</h3>
        <ul>
            <li><strong>Cfa</strong> : Subtropical humide (C + f + a) – &eacute;t&eacute;s chauds, pas de saison s&egrave;che.</li>
            <li><strong>Csa</strong> : M&eacute;diterran&eacute;en (C + s + a) – &eacute;t&eacute;s chauds et secs.</li>
            <li><strong>Dfb</strong> : Continental humide (D + f + b) – &eacute;t&eacute; doux, hiver froid, pr&eacute;cipitations r&eacute;guli&egrave;res.</li>
            <li><strong>BWh</strong> : D&eacute;sert chaud (B + W + h).</li>
            <li><strong>ET</strong> : Toundra (E + T) – cas particulier du groupe polaire.</li>
        </ul>

        <div class="custom-modal-buttons">
            <button id="closeKoppenBtn" class="custom-btn-primary">Fermer</button>
        </div>
    </div>
</div>

<style>
    /* Styles spécifiques à la modale Köppen */
    .custom-koppen-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }

    .custom-koppen-table th,
    .custom-koppen-table td {
        border: 1px solid #334155;
        padding: 10px;
        text-align: center;
    }

    .custom-koppen-table th {
        background-color: #2a3a4a;
        color: #e2e8f0;
    }

    .custom-koppen-table td {
        color: #cbd5e1;
    }

    .custom-koppen-table tr:nth-child(even) {
        background-color: #1e2a3a;
    }
</style>

<script>
    // JavaScript spécifique à la modale Köppen
    (function() {
        console.log('Modale K&ouml;ppen charg&eacute;e');
        // Initialisations sp&eacute;cifiques si n&eacute;cessaire
    })();
</script>
