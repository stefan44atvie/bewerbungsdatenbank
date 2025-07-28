<?php 
    /*
    Plugin Name: getProjectSettings
    Description: filtert aktuelle Projektdaten
    Version: 1.0
    Author: Stefan Rüdenauer
    */
    // require ('flashmessages/flashmessages_functions.php');

    foreach (glob(__DIR__ . "/projectsettings/*.php") as $file) {
        include $file;
    }
?>