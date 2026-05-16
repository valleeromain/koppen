<!-- includes/lang_selector.php -->
<style>
    /* ===== SÉLECTEUR DE LANGUE ===== */
    .lang-selector {
        position: fixed;
        top: 16px;
        right: 16px;
        z-index: 2000;
        display: flex;
        align-items: center;
        gap: 6px;
        background: rgba(15, 25, 35, 0.92);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 40px;
        padding: 6px 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        transition: all 0.2s ease;
    }

    /* En mode RTL, le sélecteur passe à gauche */
    body.rtl .lang-selector {
        right: auto;
        left: 16px;
    }

    .lang-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px 6px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #9ca3af;
        transition: all 0.15s ease;
        letter-spacing: 0.3px;
        display: flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }

    .lang-btn:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.1);
    }

    .lang-btn.active {
        color: #ffffff;
        background: rgba(59, 130, 246, 0.35);
        border: 1px solid rgba(59, 130, 246, 0.5);
    }

    .lang-btn .lang-flag {
        font-size: 1rem;
        line-height: 1;
    }

    .lang-btn .lang-code {
        font-size: 0.7rem;
        text-transform: uppercase;
    }

    /* Séparateur entre langues */
    .lang-sep {
        width: 1px;
        height: 14px;
        background: rgba(255, 255, 255, 0.15);
        flex-shrink: 0;
    }

    /* Spinner de chargement de langue */
    .lang-loading {
        width: 12px;
        height: 12px;
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        border-top-color: #64b5f6;
        animation: spin 0.6s linear infinite;
        display: none;
        margin-left: 4px;
    }

    /* Responsive : plus compact sur mobile */
    @media (max-width: 768px) {
        .lang-selector {
            top: 10px;
            right: 10px;
            padding: 4px 8px;
            gap: 4px;
        }
        body.rtl .lang-selector {
            right: auto;
            left: 10px;
        }
        .lang-btn .lang-code { display: none; } /* Garder uniquement le drapeau */
        .lang-btn { padding: 3px 5px; }
        .lang-btn .lang-flag { font-size: 1.1rem; }
    }

    /* ===== STYLES RTL GLOBAUX ===== */
    body.rtl {
        direction: rtl;
        font-family: 'Cairo', 'Inter', sans-serif;
    }

    body.rtl .sidebar {
        border-right: none;
        border-left: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: -4px 0 30px rgba(0, 0, 0, 0.3);
        order: 2; /* Sidebar à droite */
    }

    body.rtl .map-container {
        order: 1; /* Carte à gauche */
    }

    body.rtl .info-label,
    body.rtl .section-title {
        text-align: right;
    }

    body.rtl .transition-item {
        border-left: none;
        border-right: 3px solid #f59e0b;
    }

    body.rtl .transition-row {
        flex-direction: row-reverse;
    }

    body.rtl .modal-header {
        flex-direction: row-reverse;
    }

    body.rtl .decade-row {
        direction: rtl;
    }

    body.rtl .btn,
    body.rtl .action-btn {
        flex-direction: row-reverse;
    }

    body.rtl .stat-card {
        direction: rtl;
    }

    body.rtl .koppen-legend {
        right: auto;
        left: 20px;
        text-align: right;
    }

    body.rtl .legend-item {
        flex-direction: row-reverse;
    }

    body.rtl .legend-color {
        margin-right: 0;
        margin-left: 8px;
    }

    @media (max-width: 768px) {
        body.rtl .sidebar {
            border-left: none;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            order: 2;
        }
        body.rtl .koppen-legend {
            left: 10px;
            right: auto;
        }
    }
</style>

<!-- Widget sélecteur -->
<div class="lang-selector" id="langSelector">
    <button class="lang-btn" data-lang="fr" onclick="setLang('fr')" title="Français">
        <span class="lang-flag">ð«ð·</span>
        <span class="lang-code">FR</span>
    </button>
    <div class="lang-sep"></div>
    <button class="lang-btn" data-lang="en" onclick="setLang('en')" title="English">
        <span class="lang-flag">ð¬ð§</span>
        <span class="lang-code">EN</span>
    </button>
    <div class="lang-sep"></div>
    <button class="lang-btn" data-lang="de" onclick="setLang('de')" title="Deutsch">
        <span class="lang-flag">ð©ðª</span>
        <span class="lang-code">DE</span>
    </button>
    <div class="lang-sep"></div>
    <button class="lang-btn" data-lang="es" onclick="setLang('es')" title="Español">
        <span class="lang-flag">ðªð¸</span>
        <span class="lang-code">ES</span>
    </button>
    <div class="lang-sep"></div>
    <button class="lang-btn" data-lang="pt" onclick="setLang('pt')" title="Português">
        <span class="lang-flag">ðµð¹</span>
        <span class="lang-code">PT</span>
    </button>
    <div class="lang-sep"></div>
    <button class="lang-btn" data-lang="ar" onclick="setLang('ar')" title="Ø§ÙØ¹Ø±Ø¨ÙØ©">
        <span class="lang-flag">ð¸ð¦</span>
        <span class="lang-code">AR</span>
    </button>
    <div class="lang-loading" id="langLoading"></div>
</div>

<script>
    // ===== SYSTÈME I18N =====

    // Traductions chargées en mémoire
    let i18n = {};

    /**
     * t() est définie IMMÉDIATEMENT avec i18n vide.
     * Avant le chargement des traductions, elle retourne la clé.
     * Après loadLang(), i18n est rempli et t() retourne la bonne valeur.
     * Cela évite "t is not a function" si des modules s'exécutent avant i18n.
     */
    window.t = function(key) {
        const parts = key.split('.');
        let val = i18n;
        for (const p of parts) {
            if (val && typeof val === 'object' && p in val) {
                val = val[p];
            } else {
                return key;
            }
        }
        return typeof val === 'string' ? val : key;
    };

    // Langues supportées et leur code Nominatim
    const LANG_NOMINATIM = {
        fr: 'fr', en: 'en', de: 'de',
        es: 'es', pt: 'pt', ar: 'ar'
    };

    // Langue de l'IA Groq selon la langue choisie
    const LANG_GROQ = {
        fr: 'français', en: 'English', de: 'Deutsch',
        es: 'español',  pt: 'português', ar: 'Ø§ÙØ¹Ø±Ø¨ÙØ©'
    };

    // t() est définie globalement ci-dessus via window.t

    /**
     * Détecte la langue préférée du navigateur.
     * Ordre : localStorage → navigator.language → 'fr'
     */
    function detectLang() {
        const stored = localStorage.getItem('climatewatch_lang');
        if (stored) return stored;

        const browser = (navigator.language || navigator.userLanguage || 'fr').toLowerCase();
        const supported = ['fr', 'en', 'de', 'es', 'pt', 'ar'];

        // Correspondance exacte (ex: 'fr')
        if (supported.includes(browser)) return browser;

        // Correspondance préfixe (ex: 'fr-FR' → 'fr')
        const prefix = browser.substring(0, 2);
        if (supported.includes(prefix)) return prefix;

        return 'fr';
    }

    /**
     * Charge le fichier JSON de traduction via lang.php,
     * applique les traductions et gère RTL si nécessaire.
     */
    async function loadLang(lang) {
        const spinner = document.getElementById('langLoading');
        spinner.style.display = 'block';

        try {
            const res = await fetch(`includes/lang.php?lang=${lang}`);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            i18n = await res.json();

            // Stocker le choix
            localStorage.setItem('climatewatch_lang', lang);

            // Appliquer RTL / LTR
            const dir = i18n.meta?.dir || 'ltr';
            document.documentElement.setAttribute('dir', dir);
            document.documentElement.setAttribute('lang', lang);
            if (dir === 'rtl') {
                document.body.classList.add('rtl');
                // Charger la police arabe si besoin
                if (!document.getElementById('font-arabic')) {
                    const link = document.createElement('link');
                    link.id   = 'font-arabic';
                    link.rel  = 'stylesheet';
                    link.href = 'https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap';
                    document.head.appendChild(link);
                }
            } else {
                document.body.classList.remove('rtl');
            }

            // Titre de la page
            document.title = i18n.app?.title || document.title;

            // Mettre à jour le dictionnaire descriptions utilisé dans les modules
            if (i18n.koppen) {
                for (const code in i18n.koppen) {
                    descriptions[code] = i18n.koppen[code];
                }
            }

            // Appliquer les traductions dans le DOM
            applyTranslations();

            // Mettre à jour le bouton actif
            document.querySelectorAll('.lang-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.lang === lang);
            });

            // Dispatcher un événement global une fois la langue chargée.
            // index.php écoute 'langReady' et appelle chargerLieu() — garantit que
            // tous les modules sont définis avant l'appel.
            window.dispatchEvent(new CustomEvent('langReady', { detail: { lang } }));

        } catch (err) {
            console.error('Erreur chargement langue :', err);
        } finally {
            spinner.style.display = 'none';
        }
    }

    /**
     * Change la langue et recharge les traductions.
     */
    function setLang(lang) {
        loadLang(lang);
    }

    /**
     * Applique les attributs data-i18n automatiquement.
     * Tout élément avec data-i18n="clé" voit son textContent remplacé par t(clé).
     * Les icônes enfants (<i>) sont préservées.
     */
    function applyDataI18n() {
        document.querySelectorAll('[data-i18n]').forEach(el => {
            const key = el.getAttribute('data-i18n');
            const translated = t(key);
            if (translated === key) return; // Clé non trouvée, on ne touche pas
            // Préserver les icônes <i> enfants
            const icons = Array.from(el.querySelectorAll('i')).map(i => i.outerHTML);
            el.textContent = translated;
            icons.forEach(icon => {
                const tmp = document.createElement('span');
                tmp.innerHTML = icon;
                el.insertBefore(tmp.firstChild, el.firstChild);
            });
        });
    }

    /**
     * Applique toutes les traductions dans le DOM
     * pour les éléments statiques (labels, boutons, titres).
     */
    function applyTranslations() {
        // Attributs data-i18n automatiques
        applyDataI18n();

        // En-tête sidebar
        const subtitle = document.querySelector('.sidebar-header p');
        if (subtitle) subtitle.textContent = t('app.subtitle');

        // Champ lieu
        const placeLabel = document.querySelector('#nomAffichage')?.previousElementSibling;
        safeText('.info-label:nth-of-type(1)', t('sidebar.place'));

        // Labels coordonnées
        setLabelText('latAffichage', t('sidebar.latitude'));
        setLabelText('lonAffichage', t('sidebar.longitude'));

        // Bouton recherche
        const searchBtn = document.querySelector('.recherche-btn-group .btn');
        if (searchBtn) searchBtn.innerHTML = `<i class="fas fa-search"></i> ${t('sidebar.search_hint')}`;

        // Stat cards labels
        safeAllText('.stat-label', [
            t('sidebar.temp_label'),
            t('sidebar.precip_label'),
            t('sidebar.trend_label'),
            t('sidebar.point_label')
        ]);

        // Boutons d'action
        const btnEvol = document.querySelector('.action-btn.evolution');
        if (btnEvol) btnEvol.innerHTML = `<i class="fas fa-chart-line"></i> ${t('sidebar.btn_evolution')}`;

        const btnAI = document.querySelector('.action-btn.analysis');
        if (btnAI) btnAI.innerHTML = `<i class="fas fa-brain"></i> ${t('sidebar.btn_analysis')}`;

        const btnTopo = document.getElementById('toggleTopoBtn');
        if (btnTopo && !topoVisible) {
            btnTopo.innerHTML = `<i class="fas fa-layer-group"></i> ${t('sidebar.btn_koppen_show')}`;
        } else if (btnTopo && topoVisible) {
            btnTopo.innerHTML = `<i class="fas fa-eye-slash"></i> ${t('sidebar.btn_koppen_hide')}`;
        }

        // Titres sections
        const sectionTitles = document.querySelectorAll('.section-title');
        if (sectionTitles[0]) sectionTitles[0].innerHTML = `<i class="fas fa-exchange-alt"></i> ${t('sidebar.transitions_title')}`;
        if (sectionTitles[1]) sectionTitles[1].innerHTML = `<i class="fas fa-calendar-alt"></i> ${t('sidebar.decades_title')}`;

        // Modale évolution — titre
        const evoTitle = document.querySelector('#evolutionModal .modal-header h3');
        if (evoTitle) evoTitle.innerHTML = `<i class="fas fa-chart-line"></i> ${t('modal_evolution.title')}`;

        // Boutons graphique
        const btnTemp   = document.getElementById('evoBtnTemp');
        const btnPrecip = document.getElementById('evoBtnPrecip');
        const btnBoth   = document.getElementById('evoBtnBoth');
        if (btnTemp)   btnTemp.innerHTML   = `<i class="fas fa-temperature-high"></i> ${t('modal_evolution.btn_temp')}`;
        if (btnPrecip) btnPrecip.innerHTML = `<i class="fas fa-tint"></i> ${t('modal_evolution.btn_precip')}`;
        if (btnBoth)   btnBoth.innerHTML   = `<i class="fas fa-chart-line"></i> ${t('modal_evolution.btn_both')}`;

        // Stat panel modale évolution
        safeText('#evoPeriod + .stat-item-label, .stat-item:nth-child(1) .stat-item-label', t('modal_evolution.stat_period'));
        const statLabels = document.querySelectorAll('#evolutionModal .stat-item-label');
        const statKeys   = ['stat_period', 'stat_avg_temp', 'stat_total_precip', 'stat_trend'];
        statLabels.forEach((el, i) => {
            if (statKeys[i]) el.textContent = t(`modal_evolution.${statKeys[i]}`);
        });

        // Modale analyse IA
        const analysisTitle = document.querySelector('#analysisModal .modal-header h3');
        if (analysisTitle) analysisTitle.innerHTML = `<i class="fas fa-robot"></i> ${t('modal_analysis.title')}`;

        const metaModel = document.querySelector('#analysisMeta span:last-child');
        if (metaModel) metaModel.innerHTML = `<i class="fas fa-robot" style="color:#a78bfa;"></i> ${t('modal_analysis.model_label')}`;

        // Légende Köppen
        const legendTitle = document.querySelector('.legend-title');
        if (legendTitle) legendTitle.innerHTML = `<i class="fas fa-thermometer-half"></i> ${t('legend.title')}`;

        const legendItems = document.querySelectorAll('.legend-item span');
        const legendKeys  = ['oceanic','continental','mediterranean','tundra','desert','steppe','tropical','subarctic'];
        legendItems.forEach((el, i) => {
            if (legendKeys[i]) el.textContent = t(`legend.${legendKeys[i]}`);
        });
    }

    /** Helpers internes */
    function safeText(selector, text) {
        const el = document.querySelector(selector);
        if (el) el.textContent = text;
    }

    function safeAllText(selector, texts) {
        const els = document.querySelectorAll(selector);
        els.forEach((el, i) => { if (texts[i] !== undefined) el.textContent = texts[i]; });
    }

    function setLabelText(inputId, text) {
        const input = document.getElementById(inputId);
        if (input) {
            const label = input.previousElementSibling;
            if (label) label.textContent = text;
        }
    }

    // ===== INIT =====
    // loadLang() est appelé depuis index.php après que tous les modules sont chargés.
</script>
