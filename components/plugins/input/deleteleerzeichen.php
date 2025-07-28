<?php 
    function deleteLeerzeichen($mystring){
        $newstring = preg_replace('/\s+/', '', $mystring);

        return $newstring;
    }
    ?>