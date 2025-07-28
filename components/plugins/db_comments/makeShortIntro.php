<?php 
function makeShortIntro($kommentar, $did) {
    require "../../components/database/db_connect.php";

    if (!isset($did) || !is_numeric($did)) {
        return "Ungültige ID";
    }

    // Kommentar bereinigen
    $kommentar = trim($kommentar);
    $kommentar = preg_replace('/\s+/', ' ', $kommentar);

    if (empty($kommentar)) {
        return "Ungültige Eingabe";
    }

    // In Wörter aufteilen
    $words = explode(' ', $kommentar);

    if (count($words) > 15) {
        $shortWords = array_slice($words, 0, 15);
        return implode(' ', $shortWords) . ' (...)';
    } else {
        return $kommentar;
    }
}
?>