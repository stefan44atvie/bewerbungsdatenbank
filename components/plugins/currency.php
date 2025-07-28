<?php 
/*
 Plugin Name: Currency Formatierungen
 Description: erzeugt verschiedene Textausgaben angegebener Währungsformate
 Version: 1.0
 Author: Stefan Rüdenauer
*/
    // require ('currency/currency_functions.php');

    foreach (glob(__DIR__ . "/currency/*.php") as $file) {
    include $file;
    }
?>