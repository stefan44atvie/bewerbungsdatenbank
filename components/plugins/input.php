<?php 
    /*
    Plugin Name: Input
    Description: bereinigt den der Funktion übergebenen Wert
    Version: 1.0
    Author: Stefan Rüdenauer
    */
    // require ('input/input_functions.php');

    foreach (glob(__DIR__ . "/input/*.php") as $file) {
        include $file;
    }
?>