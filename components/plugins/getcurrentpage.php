<?php 
    /*
    Plugin Name: getCurrentPage
    Description: filtert den Namen der aktuellen Seite
    Version: 1.0
    Author: Stefan Rüdenauer
    */
    // require ('flashmessages/flashmessages_functions.php');

    foreach (glob(__DIR__ . "/getcurrentpage/*.php") as $file) {
        include $file;
    }
?>