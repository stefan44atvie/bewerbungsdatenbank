<?php 
    if (!function_exists('formatDateForDatabase')) {

       function formatDateForDatabase($create_date) {
            if (empty($create_date)) {
                return null; // oder ein leerer String
            }

            $timestamp = strtotime($create_date);
            if ($timestamp === false) {
                return null;
            }

            return date("Y-m-d", $timestamp);
        }
    }
?>