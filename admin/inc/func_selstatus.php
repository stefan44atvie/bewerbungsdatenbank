<?php 
   /* ---- Werte aus der enum status der Bewerbung aus der Tabelle bewerbungen holen ---- */
    $sql_enumSTAT = "SHOW COLUMNS FROM `bewerbungen` LIKE 'status'";
    $resEnumSTAT = mysqli_query($connect, $sql_enumSTAT);
    $rowEnumSTAT = mysqli_fetch_assoc($resEnumSTAT);

    $type = $rowEnumSTAT['Type']; // z. B. enum('Offen','Angenommen','Abgelehnt')
    preg_match("/^enum\((.*)\)$/", $type, $matchesSTAT);
    $enumStrSTAT = $matchesSTAT[1];

    $enumValuesSTAT = array_map(function($valSTAT) {
        return trim($valSTAT, " '");
    }, explode(",", $enumStrSTAT));

    $options_selSTAT = "";
    foreach($enumValuesSTAT as $valSTAT){
        $selected = ($valSTAT === $currentstatus_value) ? 'selected' : '';
        $options_selSTAT .= "<option value=\"$valSTAT\" $selected>$valSTAT</option>";
    }
?>