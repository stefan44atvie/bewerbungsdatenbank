<?php 
    function formatFullAntwortFirma($firma,$position){
        // Überprüfen, ob Vorname, Nachname und Firma angegeben sind
        if (!empty($position) && !empty($firma)) {
            return "von $firma zu deiner Bewerbung als $position";
        } else {
            return "Ungültige Eingabe";
        }
    }
?>