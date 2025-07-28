<?php 
    /*
    Plugin Name: Module Manipulations
    Description: erzeugt verschiedene Ausgaben des angebenen Moduls
    Version: 1.0
    Author: Stefan Rüdenauer
    */
    // require ('module_manipulations/module_manips.php');

    foreach (glob(__DIR__ . "/module_manipulations/*.php") as $file) {
    include $file;
    }
?>