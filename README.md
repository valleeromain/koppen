# koppen
# 🌍 ClimateWatch — Suivi Köppen des climats européens

Application web interactive de visualisation et d'analyse des zones climatiques européennes selon la **classification de Köppen-Geiger**. Disponible sur [iaanalytix.com/koppen](https://iaanalytix.com/koppen/).

---

## ✨ Fonctionnalités

- **Carte interactive** — Cliquez n'importe où en Europe pour obtenir la zone climatique Köppen du lieu
- **Données historiques** — Séries temporelles ERA5 par décennie avec visualisation des températures et précipitations
- **Superposition Köppen** — Couche TopoJSON colorée affichant les zones climatiques sur la carte
- **Transitions climatiques** — Détection automatique des changements de zone Köppen dans le temps
- **Analyse IA** — Description climatique générée par Llama 3.3 (70B) via l'API Groq, dans la langue choisie
- **6 langues** — Français, English, Deutsch, Español, Português, العربية (avec support RTL)
- **Design responsive** — Optimisé mobile, tablette et desktop

---

## 🗂️ Structure du projet

```
koppen/
├── index.php                  # Point d'entrée (HTML, CSS, JS principal)
├── api_climate.php            # API : données climatiques depuis SQLite (ERA5)
├── api_groq.php               # API : analyse IA via Groq (Llama 3.3)
├── api_feedback.php           # API : collecte des avis utilisateurs
├── grid_europe.topojson       # Données géographiques Europe (zones Köppen)
├── config.php                 # ⚠️ À créer — clés API (non versionné)
├── climate_europe_complete.db # ⚠️ À fournir — base SQLite ERA5 (non versionnée)
├── includes/
│   ├── head.php               # Dépendances CSS/JS (Leaflet, Chart.js...)
│   ├── sidebar.php            # Panneau latéral (stats, boutons, transitions)
│   ├── map.php                # Initialisation et logique de la carte Leaflet
│   ├── legend.php             # Légende Köppen
│   ├── modal_intro.php        # Modale d'introduction
│   ├── modal_koppen.php       # Modale de référence Köppen
│   ├── modal_evolution.php    # Modale graphiques historiques
│   ├── modal_analysis.php     # Modale analyse IA
│   ├── modal_feedback.php     # Modale feedback utilisateur
│   ├── lang.php               # Chargement des fichiers de langue
│   ├── lang_selector.php      # Sélecteur de langue
│   └── lang/
│       ├── fr.json            # Français
│       ├── en.json            # English
│       ├── de.json            # Deutsch
│       ├── es.json            # Español
│       ├── pt.json            # Português
│       └── ar.json            # العربية
├── sitemap.xml
└── robot.txt
```

---

## ⚙️ Installation

### Prérequis

- PHP 8.0+ avec l'extension **SQLite3** activée
- Serveur web (Apache, Nginx ou équivalent)
- Accès à internet pour les CDN (Leaflet, Chart.js, Font Awesome)

### Étapes

**1. Cloner le dépôt**
```bash
git clone https://github.com/valleeromain/koppen.git
cd koppen
```

**2. Créer le fichier de configuration**

Créez un fichier `config.php` à la racine (non versionné) :
```php
<?php
define('GROQ_API_KEY', 'votre_clé_groq_ici');
```

Obtenez une clé API gratuite sur [console.groq.com](https://console.groq.com/).

**3. Fournir la base de données climatiques**

Placez le fichier `climate_europe_complete.db` (base SQLite ERA5) à la racine du projet. Ce fichier contient les tables :
- `grid_points` — Points de grille européens (latitude, longitude)
- `koppen_annuel` — Données annuelles par point (code Köppen, température, précipitations)

**4. Configurer le serveur web**

Le projet ne nécessite pas de configuration particulière. Assurez-vous que PHP peut écrire dans le répertoire racine pour le fichier `feedbacks.log`.

**5. Vérifier le chemin de la base de données**

Dans `api_climate.php`, la ligne suivante doit pointer vers votre fichier SQLite :
```php
$db_path = __DIR__ . '/climate_europe_complete.db';
```

---

## 🔌 APIs

### `GET /api_climate.php`

Retourne les données climatiques ERA5 pour un point géographique.

| Paramètre | Type | Défaut | Description |
|-----------|------|--------|-------------|
| `lat` | float | 47.73 | Latitude |
| `lon` | float | 7.33 | Longitude |
| `year` | int | 2024 | Année souhaitée |

**Réponse :**
```json
{
  "success": true,
  "point": { "point_id": 1, "latitude": 47.75, "longitude": 7.25 },
  "current": { "year": 2024, "koppen_code": "Cfb", "temp_moy": 11.2, "precip_tot": 820 },
  "series": [...],
  "transitions": [...],
  "decades": [...],
  "trend_per_decade": 0.38,
  "current_code": "Cfb",
  "code_description": "🌿 Océanique tempéré - Été doux..."
}
```

### `POST /api_groq.php`

Génère une analyse climatique par IA (Llama 3.3 via Groq).

**Corps de la requête :**
```json
{
  "data": {
    "nom": "Strasbourg",
    "lat": 48.57,
    "lon": 7.75,
    "code_actuel": "Cfb",
    "temp_moyenne": 11.2,
    "precip_totale": 820,
    "trend": 0.38,
    "transitions": ["2003: Cfb → Cfa"],
    "langue": "français"
  }
}
```

### `POST /api_feedback.php`

Enregistre un avis utilisateur dans `feedbacks.log`.

---

## 🗺️ Classification Köppen supportée

| Code | Description |
|------|-------------|
| **A** — Tropical | Af, Am, Aw, As |
| **B** — Aride | BWh, BWk, BSh, BSk |
| **C** — Tempéré | Cfa, Cfb, Cfc, Csa, Csb, Csc, Cwa, Cwb |
| **D** — Continental | Dfa, Dfb, Dfc, Dfd, Dsa, Dsb, Dwa, Dwb, Dwc |
| **E** — Polaire | ET (Toundra), EF (Inlandsis) |

---

## 🛠️ Technologies

| Composant | Technologie |
|-----------|-------------|
| Backend | PHP 8+ |
| Base de données | SQLite3 (données ERA5) |
| Carte | [Leaflet.js](https://leafletjs.com/) + TopoJSON |
| Graphiques | [Chart.js](https://www.chartjs.org/) |
| Géocodage | [Nominatim](https://nominatim.openstreetmap.org/) (OpenStreetMap) |
| Fond de carte | Stadia Maps / CartoDB |
| Analyse IA | [Groq](https://groq.com/) — Llama 3.3 70B |
| Icônes | Font Awesome 6 |

---

## 🌐 Internationalisation

L'application est disponible en **6 langues**, avec support complet RTL. Les traductions sont stockées dans `includes/lang/*.json` et chargées dynamiquement côté client.

Pour ajouter une langue :
1. Créer `includes/lang/xx.json` en suivant la structure des fichiers existants
2. Ajouter la langue dans `includes/lang_selector.php`
3. Ajouter le prompt système correspondant dans `api_groq.php`

---

## ⚠️ Points d'attention

- **`config.php` ne doit jamais être versionné** (contient la clé API Groq)
- **`climate_europe_complete.db` n'est pas inclus** dans le dépôt en raison de sa taille
- Le fichier `feedbacks.log` est écrit à la racine — vérifiez que ce fichier n'est pas accessible publiquement via HTTP (règle `.htaccess` recommandée)
- Le CORS est actuellement ouvert (`*`) — à restreindre en production si nécessaire

---

## 📄 Licence

Projet personnel — © ClimateWatch / [iaanalytix.com](https://iaanalytix.com)
