<?php 
    function isMenuActive(string $target): string {
        $current_path = $_SERVER['SCRIPT_NAME']; // z. B. /admin/create.php
        $current_file = basename($current_path); // z. B. create.php
        $current_query = $_SERVER['QUERY_STRING'] ?? ''; // z. B. object=createmeldung

        // Wenn kein Query-String im Target vorhanden ist: einfacher Vergleich
        if (strpos($target, '?') === false) {
            return ($current_file === $target) ? 'active" aria-current="page' : '';
        }

        // Zerlege Ziel in Datei und Query-Teil
        [$target_file, $target_query] = explode('?', $target, 2);

        return ($current_file === $target_file && $current_query === $target_query)
            ? 'active" aria-current="page'
            : '';
    }
?>