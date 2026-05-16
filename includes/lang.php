<?php
// includes/lang.php
// Sert le fichier JSON de traduction selon le paramètre ?lang=xx
// Appelé par : fetch('includes/lang.php?lang=fr')

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: public, max-age=3600'); // Cache 1h côté navigateur

// Langues supportées
const SUPPORTED_LANGS = ['fr', 'en', 'de', 'es', 'pt', 'ar'];
const DEFAULT_LANG    = 'fr';

// Récupérer et nettoyer le paramètre lang
$lang = isset($_GET['lang']) ? preg_replace('/[^a-z]/', '', strtolower($_GET['lang'])) : '';

// Fallback si langue non supportée
if (!in_array($lang, SUPPORTED_LANGS)) {
    // Tentative de correspondance partielle : 'fr-FR' → 'fr'
    $prefix = substr($lang, 0, 2);
    $lang   = in_array($prefix, SUPPORTED_LANGS) ? $prefix : DEFAULT_LANG;
}

$file_path = __DIR__ . '/lang/' . $lang . '.json';

if (!file_exists($file_path)) {
    http_response_code(404);
    echo json_encode(['error' => 'Language file not found: ' . $lang]);
    exit;
}

// Lire et valider le JSON
$content = file_get_contents($file_path);
$decoded = json_decode($content);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid JSON for language: ' . $lang]);
    exit;
}

// Tout est bon → servir le fichier
echo $content;
?>
