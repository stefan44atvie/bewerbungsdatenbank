<?php 
    if (!function_exists('getModuleData')) {
        function getModuleData($modulename, $projektname = "Agency 2025") {
            // API-URL aufbauen
            $api_url = "https://webdesign.digitaleseele.at/projects/lizenzserver/components/api/modul_status.php?projekt=" . urlencode($projektname);

            // API-Daten abrufen
            $response = @file_get_contents($api_url);

            if ($response === false) {
                return [
                    'status' => 'error',
                    'message' => "❌ Fehler beim Abrufen der API."
                ];
            }

            // JSON dekodieren
            $data = json_decode($response, true);

            // Prüfen auf Erfolg
            if (!isset($data['status']) || $data['status'] !== 'success') {
                return [
                    'status' => 'error',
                    'message' => "❌ API-Fehler oder ungültige Antwort."
                ];
            }

            // Modul-Liste durchsuchen
            foreach ($data['module'] as $modul) {
                if ($modul['modul_name'] === $modulename) {
                    // Formatieren, wenn Funktion vorhanden
                    if (function_exists('formatCreateDateJD')) {
                        $modul['ablaufdatum_formatiert'] = formatCreateDateJD($modul['ablaufdatum']);
                    }
                    return [
                        'status' => 'success',
                        'projekt' => $data['projekt'] ?? $projektname,
                        'modul' => $modul
                    ];
                }
            }

            // Falls Modul nicht gefunden
            return [
                'status' => 'error',
                'message' => "❌ Modul '$modulename' wurde im Projekt '$projektname' nicht gefunden."
            ];
        }
    }
    

?>