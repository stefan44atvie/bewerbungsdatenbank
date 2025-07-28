<?php 

    function formatKundeVollName($vorname,$nachname){
        // Überprüfen, ob Vorname, Nachname und Firma angegeben sind
        if (!empty($firstName) && !empty($lastName) ) {
            return "$vorname $nachname";
        } elseif (!empty($vorname) && !empty($nachname)) {
            return "$vorname $nachname";
        } else {
            return "Ungültige Eingabe";
        }
    }

?>