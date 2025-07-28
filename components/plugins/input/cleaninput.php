<?php 
    function cleanInput($param)
        {
            $clean = trim($param);
            $clean = strip_tags($param);
            $clean = htmlspecialchars($param);
            return $clean;
        }

?>