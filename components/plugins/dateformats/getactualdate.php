<?php 
    if (!function_exists('getactualdate')) {

        function getactualdate(): string {
            $now = date("Y-m-d, H:i");
            return DatumAusgabe($now);
        }
    }
?>