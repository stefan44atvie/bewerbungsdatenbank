<?php 
    /*
    Plugin Name: DB Comments
    Description: verkürzt das aus der DB geladene Kommentar
    Version: 1.0
    Author: Stefan Rüdenauer
    */
    // require ('db_comments/db_comment_functions.php');
    foreach (glob(__DIR__ . "/db_comments/*.php") as $file) {
        include $file;
    }
?>