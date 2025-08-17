<?php
// Mapping laden
$projects = include __DIR__ . '/../../components/config/project_mapping.php';

// Aktives Projekt suchen
$activeProjectKey = null;
foreach ($projects as $key => $data) {
    if (!empty($data['active']) && $data['active'] === true) {
        $activeProjectKey = $key;
        break;
    }
}

if (!$activeProjectKey) {
    die("❌ Kein aktives Projekt gefunden.");
}

$projekt = $activeProjectKey;
$info    = $projects[$projekt];

// Domain & IP vom Server
$domain = $_SERVER['SERVER_NAME'] ?? '';
$ip     = $_SERVER['SERVER_ADDR'] ?? '';

// Lizenzdatei prüfen
$licenceFile = $info['licence_file'];
$key_hash    = file_exists($licenceFile) ? hash_file('sha256', $licenceFile) : '';

// URL zusammenbauen
$url = $info['licence_server'] . '?' . http_build_query([
    'projekt'  => $projekt,
    'domain'   => $domain,
    'ip'       => $ip,
    'key_hash' => $key_hash
]);

// Anfrage an Lizenzserver
$response = @file_get_contents($url);
if ($response === false) {
    die("❌ Lizenzserver nicht erreichbar.");
}

$data = json_decode($response, true);

if (!is_array($data) || $data['status'] !== 'ok') {
    die("❌ Lizenzfehler: " . ($data['message'] ?? 'Unbekannter Fehler'));
}

// Wenn alles ok
// echo "✅ Lizenz gültig für $projekt";