<?php
// api_groq.php - Version multilingue corrigée (caractères UTF-8)
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', 0);

$config_path = __DIR__ . '/config.php';
if (!file_exists($config_path)) {
    echo json_encode(['success' => false, 'error' => 'Fichier config.php manquant', 'fallback' => 'Analyse non disponible (configuration manquante)'], JSON_UNESCAPED_UNICODE);
    exit;
}
require_once($config_path);

if (!defined('GROQ_API_KEY') || GROQ_API_KEY === '') {
    echo json_encode(['success' => false, 'error' => 'Clé API GROQ non configurée', 'fallback' => 'Analyse non disponible (clé API manquante)'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée - Utilisez POST'], JSON_UNESCAPED_UNICODE);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$data  = $input['data'] ?? null;

if (!$data) {
    echo json_encode(['error' => 'Données manquantes dans la requête'], JSON_UNESCAPED_UNICODE);
    exit;
}

// ============================================================
// LANGUE : validation et fallback (Correction Arabe)
// ============================================================
$supported = ['français', 'English', 'Deutsch', 'español', 'português', 'العربية'];
$langue = (isset($data['langue']) && in_array($data['langue'], $supported, true))
    ? $data['langue']
    : 'français';

// Libellé "aucune transition" localisé
$no_transition = [
    'français'  => 'Aucune transition',
    'English'   => 'No transition detected',
    'Deutsch'   => 'Kein Übergang erkannt',
    'español'   => 'Ninguna transición detectada',
    'português' => 'Nenhuma transição detectada',
    'العربية'   => 'لا توجد تحولات',
];

$transitions_str = (isset($data['transitions']) && count($data['transitions']) > 0)
    ? implode(', ', $data['transitions'])
    : $no_transition[$langue];

// ============================================================
// PROMPTS SYSTÈME par langue
// ============================================================
$system_prompts = [
    'français'  => 'Tu es un climatologue spécialiste de la classification de Köppen-Geiger. Tu décris toujours le climat en langage naturel avant de donner le code. Tu ne donnes jamais le code seul. Tu réponds en français, avec rigueur et pédagogie.',
    'English'   => 'You are a climatologist specializing in the Köppen-Geiger classification. You always describe the climate in natural language before giving the code. You never give the code alone. You respond in English, with rigor and clarity.',
    'Deutsch'   => 'Sie sind Klimatologe und spezialisiert auf die Köppen-Geiger-Klassifikation. Sie beschreiben das Klima immer in natürlicher Sprache, bevor Sie den Code angeben. Sie geben den Code niemals allein an. Sie antworten auf Deutsch, präzise und verständlich.',
    'español'   => 'Eres climatólogo especializado en la clasificación de Köppen-Geiger. Siempre describes el clima en lenguaje natural antes de dar el código. Nunca das el código solo. Respondes en español, con rigor y claridad.',
    'português' => 'Você é climatologista especializado na classificação de Köppen-Geiger. Você sempre descreve o clima em linguagem natural antes de dar o código. Nunca dá o código sozinho. Responde em português, com rigor e clareza.',
    'العربية'   => 'أنت عالم مناخ متخصص في تصنيف كوبن-غايغر. أنت دائماً تصف المناخ بلغة طبيعية قبل إعطاء الرمز. لا تعطي الرمز وحده أبداً. تجيب باللغة العربية بدقة ووضوح.',
];

// ============================================================
// PARAMÈTRES UTILISATEUR
// ============================================================
$nom     = $data['nom']           ?? '?';
$lat     = $data['lat']           ?? '?';
$lon     = $data['lon']           ?? '?';
$code    = $data['code_actuel']   ?? '?';
$temp    = $data['temp_moyenne']  ?? '?';
$precip  = $data['precip_totale'] ?? '?';
$trend   = $data['trend']         ?? '0';

// ============================================================
// PROMPTS UTILISATEUR par langue
// ============================================================
$prompts = [
    'français' => "Tu es climatologue. Voici des données observées pour un lieu.
=== FICHE CLIMATIQUE ===
Lieu : {$nom} ({$lat}°N, {$lon}°E)
Code Köppen : {$code}
Température moyenne annuelle : {$temp}°C
Précipitations totales annuelles : {$precip} mm
Tendance thermique : {$trend}°C par décennie
Transitions détectées : {$transitions_str}
=== CONSIGNE D'ÉCRITURE ===
Rédige une analyse en 4 paragraphes. Ne donne JAMAIS le code seul. Décris d'abord le climat en mots, puis mets le code entre parenthèses.",

    'English' => "You are a climatologist. Here is observed data for a location.
=== CLIMATE DATA ===
Location: {$nom} ({$lat}°N, {$lon}°E)
Köppen code: {$code}
Annual mean temperature: {$temp}°C
Annual total precipitation: {$precip} mm
Temperature trend: {$trend}°C per decade
Detected transitions: {$transitions_str}
=== WRITING INSTRUCTIONS ===
Write an analysis in 4 paragraphs. NEVER give the code alone. Describe the climate in words first, then put the code in parentheses.",

    'Deutsch' => "Sie sind Klimatologe. Hier sind beobachtete Daten für einen Ort.
=== KLIMADATEN ===
Ort: {$nom} ({$lat}°N, {$lon}°E)
Köppen-Code: {$code}
Jährliche Durchschnittstemperatur: {$temp}°C
Jährlicher Gesamtniederschlag: {$precip} mm
Temperaturtrend: {$trend}°C pro Jahrzehnt
Erkannte Übergänge: {$transitions_str}
=== SCHREIBANWEISUNGEN ===
Schreiben Sie eine Analyse in 4 Absätzen. Geben Sie NIEMALS den Code allein an. Beschreiben Sie das Klima zuerst in Worten, dann setzen Sie den Code in Klammern.",

    'español' => "Eres climatólogo. Aquí están los datos observados para un lugar.
=== DATOS CLIMÁTICOS ===
Lugar: {$nom} ({$lat}°N, {$lon}°E)
Código Köppen: {$code}
Temperatura media anual: {$temp}°C
Precipitación total anual: {$precip} mm
Tendencia térmica: {$trend}°C por década
Transiciones detectadas: {$transitions_str}
=== INSTRUCCIONES DE ESCRITURA ===
Escribe un análisis en 4 párrafos. NUNCA des el código solo. Describe el clima primero en palabras, luego pon el código entre paréntesis.",

    'português' => "Você é climatologista. Aqui estão os dados observados para um local.
=== DADOS CLIMÁTICOS ===
Local: {$nom} ({$lat}°N, {$lon}°E)
Código Köppen: {$code}
Temperatura média anual: {$temp}°C
Precipitação total anual: {$precip} mm
Tendência térmica: {$trend}°C por década
Transições detectadas: {$transitions_str}
=== INSTRUÇÕES DE ESCRITA ===
Escreva uma análise em 4 parágrafos. NUNCA dê o código sozinho. Descreva o clima primeiro em palavras, depois coloque o código entre parênteses.",

    'العربية' => "أنت عالم مناخ. إليك البيانات المرصودة لموقع معين.
=== البيانات المناخية ===
الموقع: {$nom} ({$lat}°شمالاً، {$lon}°شرقاً)
رمز كوبن: {$code}
متوسط درجة الحرارة السنوية: {$temp}°C
إجمالي هطول الأمطار السنوي: {$precip} مم
الاتجاه الحراري: {$trend}°C لكل عقد
التحولات المكتشفة: {$transitions_str}
=== تعليمات الكتابة ===
اكتب تحليلاً في 4 فقرات. لا تعطِ الرمز وحده أبداً. صف المناخ أولاً بكلمات طبيعية، ثم ضع الرمز بين قوسين."
];

// Sélection du prompt (fallback sur français si non défini dans le tableau partiel ci-dessus)
$user_prompt = $prompts[$langue] ?? $prompts['français'];

// ============================================================
// APPEL API GROQ
// ============================================================
$ch = curl_init(GROQ_API_URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . GROQ_API_KEY
]);

$request_body = [
    'model'    => 'llama-3.3-70b-versatile',
    'messages' => [
        ['role' => 'system', 'content' => $system_prompts[$langue]],
        ['role' => 'user',   'content' => $user_prompt]
    ],
    'temperature' => 0.4,
    'max_tokens'  => 1000
];

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body, JSON_UNESCAPED_UNICODE));
curl_setopt($ch, CURLOPT_TIMEOUT, 35);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$response   = curl_exec($ch);
$http_code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// ============================================================
// GESTION DES ERREURS & MESSAGES LOCALISÉS
// ============================================================
$error_msgs = [
    'connection' => [
        'français'  => "L'analyse IA est temporairement indisponible.",
        'العربية'   => 'تحليل الذكاء الاصطناعي غير متاح مؤقتاً.',
    ],
    'fallback' => [
        'français'  => "Le climat de {$nom} (code {$code}) présente une température moyenne de {$temp}°C.",
        'العربية'   => "مناخ {$nom} (الرمز {$code}) يتميز بمتوسط حرارة {$temp}°C.",
    ],
];

if ($curl_error) {
    echo json_encode(['success' => false, 'error' => $curl_error, 'fallback' => $error_msgs['connection'][$langue] ?? $error_msgs['connection']['français']], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($http_code === 200) {
    $groq_response = json_decode($response, true);
    $content = $groq_response['choices'][0]['message']['content'] ?? null;
    echo json_encode(['success' => true, 'analysis' => $content], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['success' => false, 'http_code' => $http_code, 'fallback' => $error_msgs['fallback'][$langue] ?? $error_msgs['fallback']['français']], JSON_UNESCAPED_UNICODE);
}
?>
