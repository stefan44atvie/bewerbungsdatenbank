<?php 
    function formatFullCompanyPosition($firma,$position){
        // Überprüfen, ob Vorname, Nachname und Firma angegeben sind
        if (!empty($firma) && !empty($position)) {
            return "$position bei $firma";
        } else {
            return "Ungültige Eingabe";
        }
    }
?>