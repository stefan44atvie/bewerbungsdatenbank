<?php 
/*
 Plugin Name: Active Menu
 Description: fügt dem gerade aktiven Menüeintrag die Klasse active
 Version: 1.0
 Author: Stefan Rüdenauer
*/
    // require ('currency/currency_functions.php');

    foreach (glob(__DIR__ . "/activemenu/*.php") as $file) {
        include $file;
    }
?>