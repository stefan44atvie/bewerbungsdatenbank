<?php
// Antwort als JSON ausgeben
header('Content-Type: application/json');

// Eingabeparameter prüfen
$projekt = $_GET['projekt'] ?? null;
$current_version = $_GET['current_version'] ?? null;

if (!$projekt || !$current_version) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => '❌ Projektname oder aktuelle Version fehlen.'
    ]);
    exit;
}

// URL des Update-Servers aufbauen
$updateServer = 'http://192.168.3.44/updateserver/components/api/api_updates.php';
$url = $updateServer . '?' . http_build_query([
    'projekt' => $projekt,
    'current_version' => $current_version
]);

// Anfrage senden
$response = @file_get_contents($url);

if ($response === false) {
    http_response_code(502);
    echo json_encode([
        'success' => false,
        'error' => '❌ Verbindung zum Update-Server fehlgeschlagen.'
    ]);
    exit;
}

// Original-Antwort des Update-Servers weiterleiten
echo $response;