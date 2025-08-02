<?php 
    function formatFullAntwortBewerbung($firma,$position){
        // Überprüfen, ob Vorname, Nachname und Firma angegeben sind
        if (!empty($position) && !empty($firma)) {
            return "$position bei $firma";
        } else {
            return "Ungültige Eingabe";
        }
    }
?>