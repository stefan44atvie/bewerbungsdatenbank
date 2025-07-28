<?php 
    if (!function_exists('formatUpdateDate')) {

        function formatUpdateDate($update_date){
            // $updateNewDatee = date("d.m.Y H:i", strtotime($rowAAF['update_date']));
            $updateNewDatee = date("d.m.Y, H:i", strtotime($update_date));

            if (!empty($update_date)) {
                return $updateNewDatee;
            } else {
                return "Ungültige Eingabe";
            }
        }
    }
?>