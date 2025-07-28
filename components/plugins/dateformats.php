<?php 
/*
 Plugin Name: Date3Formats
 Description: erzeugt verschiedene Ausgaben angegebener Datumsformate
 Version: 1.2
 Author: Stefan Rüdenauer
 Changelog: verbesserte TimeAusgabe-Funktion
*/
    // require ('dateformats/format_date_functions.php');
    foreach (glob(__DIR__ . "/dateformats/*.php") as $file) {
        include $file;
    }

?>