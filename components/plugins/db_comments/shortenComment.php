<?php 
    function shortenComment($kommentar, $did) {
        require "../../components/database/db_connect.php";

        if (!isset($did) || !is_numeric($did)) {
            return "Ungültige ID";
        }

        // Kommentar bereinigen: Leerzeichen am Anfang/Ende + mehrfaches Leerzeichen
        $kommentar = trim($kommentar);
        $kommentar = preg_replace('/\s+/', ' ', $kommentar);

        // UTF-8 sicher zählen
        if (mb_strlen($kommentar, 'UTF-8') > 25) {
            // UTF-8 sicher kürzen und " (...)" anhängen
            $comment_short = mb_substr($kommentar, 0, 22, 'UTF-8') . ' (...)';
        } else {
            $comment_short = $kommentar;
        }

        return !empty($kommentar) ? $comment_short : "Ungültige Eingabe";
    }

?>