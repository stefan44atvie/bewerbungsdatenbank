<?php 
    if (!function_exists('istModulAktiv')) {
        function istModulAktiv(string $projekt, string $modul): bool {
                $url = "http://192.168.3.44/lizenzserver/components/api/modul_status.php?projekt=" . urlencode($projekt) . "&modul=" . urlencode($modul);
                
                $json = @file_get_contents($url);
                if ($json === false) {
                    error_log("⚠️ Fehler beim Abrufen von $url");
                    return false;
                }

                $data = json_decode($json, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    error_log("⚠️ JSON-Fehler: " . json_last_error_msg());
                    return false;
                }

                return $data['aktiv'] ?? false;
        }
    }
    

?>