<?php
// api_climate.php - VERSION CORRIGÉE
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Chemin relatif - adapter selon votre structure
$db_path = __DIR__ . '/climate_europe_complete.db';

// Alternative: chemin absolu pour votre serveur
// $db_path = '/htdocs/datascout.iaanalytix.com/ianalytix/apps/climat/climate_europe_complete.db';

if (!file_exists($db_path)) {
    echo json_encode(['error' => 'Base non trouvee: ' . $db_path]);
    exit;
}

$db = new SQLite3($db_path);

$lat  = isset($_GET['lat'])  ? floatval($_GET['lat'])  : 47.73;
$lon  = isset($_GET['lon'])  ? floatval($_GET['lon'])  : 7.33;
$year = isset($_GET['year']) ? intval($_GET['year'])   : 2024;

// 1. Point le plus proche
$stmt = $db->prepare('SELECT point_id, latitude, longitude FROM grid_points ORDER BY ABS(latitude - :lat) + ABS(longitude - :lon) LIMIT 1');
$stmt->bindValue(':lat', $lat, SQLITE3_FLOAT);
$stmt->bindValue(':lon', $lon, SQLITE3_FLOAT);
$result = $stmt->execute();
$point  = $result->fetchArray(SQLITE3_ASSOC);

if (!$point) {
    echo json_encode(['error' => 'Point non trouve']);
    $db->close();
    exit;
}

$point_id = intval($point['point_id']);

// 2. Serie temporelle
$stmt = $db->prepare('SELECT year, koppen_code, temp_moy, precip_tot FROM koppen_annuel WHERE point_id = :pid ORDER BY year');
$stmt->bindValue(':pid', $point_id, SQLITE3_INTEGER);
$result = $stmt->execute();

$series = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $series[] = [
        'year'        => intval($row['year']),
        'koppen_code' => strval($row['koppen_code']),
        'temp_moy'    => floatval($row['temp_moy']),
        'precip_tot'  => floatval($row['precip_tot'])
    ];
}

// 3. Annee demandee
$current = null;
foreach ($series as $s) {
    if ($s['year'] === $year) { $current = $s; break; }
}
if (!$current && count($series) > 0) {
    $current = $series[count($series) - 1];
}

// 4. Transitions
$transitions = [];
$prev = null;
foreach ($series as $s) {
    if ($prev !== null && $prev !== $s['koppen_code']) {
        $transitions[] = ['year' => $s['year'], 'from' => $prev, 'to' => $s['koppen_code']];
    }
    $prev = $s['koppen_code'];
}

// 5. Decennies
$decades = [];
foreach ($series as $s) {
    $dec = intval(floor($s['year'] / 10) * 10);
    if (!isset($decades[$dec])) {
        $decades[$dec] = ['temp_sum' => 0.0, 'precip_sum' => 0.0, 'count' => 0, 'codes' => []];
    }
    $decades[$dec]['temp_sum']   += floatval($s['temp_moy']);
    $decades[$dec]['precip_sum'] += floatval($s['precip_tot']);
    $decades[$dec]['count']++;
    $code = strval($s['koppen_code']);
    $decades[$dec]['codes'][$code] = ($decades[$dec]['codes'][$code] ?? 0) + 1;
}

$decades_out = [];
foreach ($decades as $dec => $d) {
    $dominant = '';
    $max = 0;
    foreach ($d['codes'] as $code => $cnt) {
        if ($cnt > $max) { $max = $cnt; $dominant = $code; }
    }
    $count = intval($d['count']);
    $temp_avg = 0.0;
    $precip_avg = 0.0;
    if ($count > 0) {
        $temp_avg = round(floatval($d['temp_sum']) / $count, 1);
        $precip_avg = round(floatval($d['precip_sum']) / $count, 0);
    }
    $decades_out[] = [
        'decade'       => intval($dec),
        'temp_avg'     => $temp_avg,
        'precip_avg'   => $precip_avg,
        'dominant_code'=> $dominant
    ];
}

// 6. Tendance
$trend = 0.0;
$n = count($series);
if ($n >= 2) {
    $first_temp = floatval($series[0]['temp_moy']);
    $last_temp = floatval($series[$n-1]['temp_moy']);
    $first_year = intval($series[0]['year']);
    $last_year = intval($series[$n-1]['year']);
    $years_diff = $last_year - $first_year;
    if ($years_diff > 0) {
        $trend = round(($last_temp - $first_temp) / $years_diff * 10, 2);
    }
}

// 7. Descriptions - Version avec Entités HTML (Codes Hexadécimaux)
$descriptions = [
    'Af'  => '&#x1F334; Forêt tropicale humide - Température moyenne >18°C chaque mois, précipitations >60mm chaque mois',
    'Am'  => '&#x1F327;&#xFE0F; Mousson tropicale - Comme Af mais avec saison sèche courte',
    'Aw'  => '&#x1F992; Savane - Tropical avec saison sèche hivernale',
    'As'  => '&#x1F992; Savane à été sec - Tropical avec saison sèche estivale',
    'BWh' => '&#x1F3DC;&#xFE0F; Désert chaud - Précipitations annuelles < 50% du seuil d\'aridité, température annuelle >18°C',
    'BWk' => '&#x1F3DC;&#xFE0F; Désert froid - Précipitations annuelles < 50% du seuil d\'aridité, température annuelle <18°C',
    'BSh' => '&#x1F33E; Steppe chaude - Précipitations entre 50% et 100% du seuil d\'aridité, température >18°C',
    'BSk' => '&#x1F33E; Steppe froide - Précipitations entre 50% et 100% du seuil d\'aridité, température <18°C',
    'Cfa' => '&#x1F333; Subtropical humide - Été chaud (>22°C), pas de saison sèche, hiver doux',
    'Cfb' => '&#x1F33F; Océanique tempéré - Été doux (<22°C), pluies régulières toute l\'année',
    'Cfc' => '&#x1F342; Océanique frais - Été court et frais, pluies abondantes',
    'Csa' => '&#x1F34A; Méditerranéen chaud - Été chaud et sec, hiver doux et humide',
    'Csb' => '&#x1F34B; Méditerranéen doux - Été sec et doux, hiver tempéré',
    'Csc' => '&#x1F33F; Méditerranéen froid - Été frais et sec, hiver froid',
    'Cwa' => '&#x1F33E; Subtropical à hiver sec - Été chaud, saison sèche en hiver',
    'Cwb' => '&#x1F33F; Subtropical à hiver sec - Été doux, saison sèche en hiver',
    'Dfa' => '&#x2744;&#xFE0F; Continental chaud - Été chaud (>22°C), hiver froid (<-3°C)',
    'Dfb' => '&#x2744;&#xFE0F; Continental humide - Été doux, hiver froid, neige abondante',
    'Dfc' => '&#x2744;&#xFE0F; Subarctique - Été court, hiver très froid, précipitations modérées',
    'Dfd' => '&#x2744;&#xFE0F; Sibérien - Hiver extrêmement froid, été très court',
    'ET'  => '&#x1F5FB; Toundra - Été frais (<10°C maximum), sol gelé une grande partie de l\'année',
    'EF'  => '&#x1F9CA; Inlandsis - Tous les mois <0°C, glace permanente'
];

$current_code = $current ? strval($current['koppen_code']) : 'N/A';
$code_desc = $descriptions[$current_code] ?? 'Climat tempéré de transition';

echo json_encode([
    'success'          => true,
    'point'            => [
        'point_id'  => intval($point['point_id']),
        'latitude'  => floatval($point['latitude']),
        'longitude' => floatval($point['longitude'])
    ],
    'current'          => $current,
    'series'           => $series,
    'transitions'      => $transitions,
    'decades'          => $decades_out,
    'trend_per_decade' => $trend,
    'current_code'     => $current_code,
    'code_description' => $code_desc
], JSON_UNESCAPED_UNICODE);

$db->close();
?>
