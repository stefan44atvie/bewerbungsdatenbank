<?php 
/*
 Plugin Name: Convert2Webp
 Description: erzeugt verschiedene Textausgaben angegebener Währungsformate
 Version: 1.0
 Author: Stefan Rüdenauer
*/
    // require ('currency/currency_functions.php');

    foreach (glob(__DIR__ . "/convert2webp/*.php") as $file) {
        include $file;
    }
?>