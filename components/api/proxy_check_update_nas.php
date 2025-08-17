<?php
// Antwort als JSON
header('Content-Type: application/json');

// Eingabeparameter prüfen
$projekt = $_GET['projekt'] ?? null;
$current_version = $_GET['current_version'] ?? null;

if (!$projekt || !$current_version) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => '❌ Projektname oder aktuelle Version fehlen.'
    ]);
    exit;
}

// Mapping laden
$projects = include __DIR__ . '/../../components/config/project_mapping.php';

// Projekt im Mapping suchen
if (!isset($projects[$projekt])) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error'   => "❌ Projekt '$projekt' nicht im Mapping gefunden."
    ]);
    exit;
}

$projectConfig = $projects[$projekt];

// Update-Server aus Mapping nehmen (Fallback, falls nicht definiert)
$updateServer = $projectConfig['update_server'] ?? null;
if (!$updateServer) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => "❌ Kein Update-Server für Projekt '$projekt' definiert."
    ]);
    exit;
}

// Update-Channel ggf. berücksichtigen
$queryData = [
    'projekt'         => $projekt,
    'current_version' => $current_version,
];
if (!empty($projectConfig['update_channel'])) {
    $queryData['channel'] = $projectConfig['update_channel'];
}

// URL bauen
$url = $updateServer . '?' . http_build_query($queryData);

// Anfrage senden
$response = @file_get_contents($url);

if ($response === false) {
    http_response_code(502);
    echo json_encode([
        'success' => false,
        'error'   => '❌ Verbindung zum Update-Server fehlgeschlagen.'
    ]);
    exit;
}

// Antwort vom Update-Server 1:1 durchreichen
echo $response;