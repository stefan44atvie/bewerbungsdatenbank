<?php 
    if (!function_exists('DatumAusgabe')) {
        function DatumAusgabe($getDate){
            $newDate = date("d.m.Y", strtotime($getDate));
            return $newDate;
        }
    }
?>