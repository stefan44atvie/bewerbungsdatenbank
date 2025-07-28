<?php 
    /*
    Plugin Name: ZipChangedFiles
    Description: fügt Dateien einem zip-Archiv hinzu, die seit dem letzten Aufruf verändert wurden
    Version: 1.0
    Author: Stefan Rüdenauer
    */
    foreach (glob(__DIR__ . "/zipChangedFiles/*.php") as $file) {
        include $file;
    }
?>