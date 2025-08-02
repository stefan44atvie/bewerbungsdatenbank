<?php 
    if (!function_exists('formatTimeForDatabase')) {

       function formatTimeForDatabase($create_date) {
            if (empty($create_date)) {
                return null; // oder ein leerer String
            }

            $timestamp = strtotime($create_date);
            if ($timestamp === false) {
                return null;
            }

            return date("H:i", $timestamp);
        }
    }
?>