<?php 
    if (!function_exists('formatCreateDate')) {

        function formatCreateDate($create_date){
            // $updateNewDatee = date("d.m.Y H:i", strtotime($rowAAF['update_date']));
            $createNewDatee = date("d.m.Y, H:i", strtotime($create_date));

            if (!empty($create_date)) {
                return $createNewDatee;
            } else {
                return "Ungültige Eingabe";
            }
        }
    }
?>