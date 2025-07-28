
<?php 
    function formatKundeVollNameCompany($vorname,$nachname, $firma){
        // Überprüfen, ob Vorname, Nachname und Firma angegeben sind
        if (!empty($firstName) && !empty($lastName) && !empty($firma) ) {
            return "$vorname $nachname ($firma)";
        } elseif (!empty($vorname) && !empty($nachname) && !empty($firma)) {
            return "$vorname $nachname ($firma)";
        } else {
            return "Ungültige Eingabe";
        }
    }
?>