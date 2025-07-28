<?php 
    if (!function_exists('formatCreateDateJD')) {
        function formatCreateDateJD($create_date) {
            if (empty($create_date)) {
                return "Ungültige Eingabe";
            }

            $timestamp = strtotime($create_date);
            if ($timestamp === false) {
                return "Ungültiges Datum";
            }

            return date("d.m.Y", $timestamp);
        }
    }

?>