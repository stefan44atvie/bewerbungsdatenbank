<?php 
   /* ---- Werte aus der enum status der Bewerbung aus der Tabelle bewerbungen holen ---- */
    $sqlEnumAntErg = "SHOW COLUMNS FROM `firmen_antworten` LIKE 'antwort_ergebnis'";
    $resEnumAntErg = mysqli_query($connect, $sqlEnumAntErg);
    $rowEnumAntErg = mysqli_fetch_assoc($resEnumAntErg);

    $type = $rowEnumAntErg['Type']; // z. B. enum('Offen','Angenommen','Abgelehnt')
    preg_match("/^enum\((.*)\)$/", $type, $matchesAntErg);
    $enumStrAntErg = $matchesAntErg[1];

    $enumValueAntErg = array_map(function($valAntErg) {
        return trim($valAntErg, " '");
    }, explode(",", $enumStrAntErg));

    $options_selAntErg = "";
    foreach($enumValueAntErg as $valAntErg){
        $selected = ($valAntErg === $currentergebnis_value) ? 'selected' : '';
        $options_selAntErg .= "<option value=\"$valAntErg\" $selected>$valAntErg</option>";
    }
?>