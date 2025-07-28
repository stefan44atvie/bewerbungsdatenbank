<?php 
    /*
    Plugin Name: ilesize_formats
    Description: rechnet den in der DB angebenen Wert in MB,B, KBV oder GB um
    Version: 1.0
    Author: Stefan Rüdenauer
    */
    // require ('filesizes/filesizes_functions.php');

    foreach (glob(__DIR__ . "/filesizes/*.php") as $file) {
        include $file;
    }

?>