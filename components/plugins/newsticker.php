<?php 
/*
 Plugin Name: Newsticker
 Description: stellt einen Newsticker bereit
 Version: 1.0
 Author: Stefan Rüdenauer
*/
    // require ('dateformats/format_date_functions.php');
    foreach (glob(__DIR__ . "/nachrichten/*.php") as $file) {
        include $file;
    }

?>