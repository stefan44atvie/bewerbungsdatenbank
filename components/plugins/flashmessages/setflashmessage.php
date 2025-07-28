<?php 
/* ---- FlashMessage-System für diverse Meldungen ---- */
    function setFlashMessage($type, $message) {
         // Sicherstellen, dass die Session gestartet wird
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Überprüfen, ob die 'flash_messages' in der Session existiert
        if (!isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'] = [];
        }

        // Flash-Nachricht setzen
        $_SESSION['flash_messages'][$type] = $message;
    }
?>