<?php
    function formatBetragCurrency($betrag){
        if (is_numeric($betrag)) {
            // Formatieren des Betrags mit zwei Dezimalstellen und deutschem Format (Komma als Dezimaltrennzeichen)
            return number_format((float)$betrag, 2, ',', '.').' '.'€';
        } else {
            return "0€";  // Falls der Betrag keine gültige Zahl ist
        }
    }

?>