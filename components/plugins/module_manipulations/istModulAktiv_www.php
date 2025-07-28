<?php 
    if (!function_exists('istModulAktiv')) {
        function istModulAktiv(string $projekt, string $modul): bool {
                $url = "https://webdesign.digitaleseele.at/projects/lizenzserver/components/api/modul_status.php?projekt=" . urlencode($projekt) . "&modul=" . urlencode($modul);
                
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

                error_log("✅ Modulstatus aus API: " . var_export($data, true));

                return $data['aktiv'] ?? false;
            }
    }

    

    ?>