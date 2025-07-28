<?php 
    /*
    Plugin Name: Name Manipulations
    Description: gibt formatierte Texte aus
    Version: 1.0
    Author: Stefan Rüdenauer
    */
    foreach (glob(__DIR__ . "/name_manipulations/*.php") as $file) {
        include $file;
    }
?>