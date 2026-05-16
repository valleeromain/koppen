<?php
// api_feedback.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['error' => 'Données invalides']);
    exit;
}

// Sauvegarder dans un fichier log
$logFile = 'feedbacks.log';
$logEntry = date('Y-m-d H:i:s') . ' | ' .
            'Note: ' . ($data['rating'] ?? 0) . '/5 | ' .
            'Commentaire: ' . ($data['comment'] ?? 'N/A') . ' | ' .
            'Page: ' . ($data['page'] ?? 'N/A') . ' | ' .
            'Contact: ' . ($data['allowContact'] ? 'Oui' : 'Non') . "\n";

file_put_contents($logFile, $logEntry, FILE_APPEND);

echo json_encode(['success' => true]);
?>
