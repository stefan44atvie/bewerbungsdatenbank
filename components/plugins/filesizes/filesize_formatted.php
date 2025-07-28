<?php 
 /* ---- Dateigröße aus dem Dateisystem herausfiltern ---- */
    function filesize_formatted($datei)
    {
        if (!file_exists($datei)) {
            return 'Unbekannt';
        }
            
        $size = filesize($datei);
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
            
        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }
    /* ---- Dateigröße aus dem Dateisystem herausfiltern ---- */

?>