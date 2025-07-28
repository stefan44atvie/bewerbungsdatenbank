<?php 
    /*
    Plugin Name: FlashMessages
    Description: erzeugt ein auf Sessions basiertes FMessage-System
    Version: 1.0
    Author: Stefan Rüdenauer
    */
    // require ('flashmessages/flashmessages_functions.php');

    foreach (glob(__DIR__ . "/flashmessages/*.php") as $file) {
        include $file;
    }
?>