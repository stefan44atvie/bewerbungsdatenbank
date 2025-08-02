<?php
    $sql_enum = "SHOW COLUMNS FROM `bewerbungen` LIKE 'bewerbungsmethode'";
    $resEnum = mysqli_query($connect, $sql_enum);
    $rowEnum = mysqli_fetch_assoc($resEnum);
    $type = $rowEnum['Type']; // z. B. enum('E-Mail','Online','Post')
    preg_match("/^enum\((.*)\)$/", $type, $matches);
    $enumStr = $matches[1];

    $enumValues = array_map(function($val) {
        return trim($val, " '");
    }, explode(",", $enumStr));

    $options_selmethod = "";
    foreach ($enumValues as $methode) {
        $selected = ($methode === $current_value) ? 'selected' : '';
        $options_selmethod .= "<option value=\"$methode\" $selected>$methode</option>";
    }
?>