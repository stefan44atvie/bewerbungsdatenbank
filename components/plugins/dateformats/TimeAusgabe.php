<?php 
    // function TimeAusgabe($getTime){
    //     $newTime = date("H:i", strtotime($getTime));
    //     return $newTime;
    // }
        if (!function_exists('TimeAusgabe')) {

            function TimeAusgabe(string $input, string $format = 'H:i'): string {
                try {
                    $dt = new DateTime($input);
                    return $dt->format($format);
                } catch (Exception $e) {
                    return '';
                }
            }
        }
?>