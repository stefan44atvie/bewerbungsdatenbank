<?php 
   /* ---- Werte aus der enum status der Bewerbung aus der Tabelle bewerbungen holen ---- */
    $sql_enumPRIO = "SHOW COLUMNS FROM `bewerbungen` LIKE 'priority'";
    $resEnumPRIO = mysqli_query($connect, $sql_enumPRIO);
    $resEnumPRIO = mysqli_fetch_assoc($resEnumPRIO);

    $type = $resEnumPRIO['Type']; // z. B. enum('Offen','Angenommen','Abgelehnt')
    preg_match("/^enum\((.*)\)$/", $type, $matchesPRIO);
    $enumStrPRIO = $matchesPRIO[1];

    $enumValuePRIO = array_map(function($valPRIO) {
        return trim($valPRIO, " '");
    }, explode(",", $enumStrPRIO));

    $options_selPRIO = "";
    foreach($enumValuePRIO as $valPRIO){
        $selected = ($valPRIO === $currentpriority_value) ? 'selected' : '';
        $options_selPRIO .= "<option value=\"$valPRIO\" $selected>$valPRIO</option>";
    }




?>