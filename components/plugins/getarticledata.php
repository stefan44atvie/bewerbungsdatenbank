<?php 
    /*
    Plugin Name: getArticleData
    Description: filtert Daten des aktuellsten Artikels
    Version: 1.0
    Author: Stefan Rüdenauer
    */
    // require ('flashmessages/flashmessages_functions.php');

    foreach (glob(__DIR__ . "/articledata/*.php") as $file) {
        include $file;
    }
?>