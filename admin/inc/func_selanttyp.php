<?php 
   /* ---- Werte aus der enum status der Bewerbung aus der Tabelle bewerbungen holen ---- */
    $sqlEnumAntTyp = "SHOW COLUMNS FROM `firmen_antworten` LIKE 'antwort_typ'";
    $resEnumAntTyp = mysqli_query($connect, $sqlEnumAntTyp);
    $rowEnumAntTyp = mysqli_fetch_assoc($resEnumAntTyp);

    $type = $rowEnumAntTyp['Type']; // z. B. enum('Offen','Angenommen','Abgelehnt')
    preg_match("/^enum\((.*)\)$/", $type, $matchesAntTyp);
    $enumStrAntTyp = $matchesAntTyp[1];

    $enumValueAntTyp = array_map(function($valAntTyp) {
        return trim($valAntTyp, " '");
    }, explode(",", $enumStrAntTyp));

    $options_selAntTyp = "";
    foreach($enumValueAntTyp as $valAntTyp){
        $selected = ($valAntTyp === $currenttyp_value) ? 'selected' : '';
        $options_selAntTyp .= "<option value=\"$valAntTyp\" $selected>$valAntTyp</option>";
    }
?>