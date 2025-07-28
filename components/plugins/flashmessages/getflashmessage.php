<?php 
/* ---- FlashMessage-System für diverse Meldungen ---- */
    function getFlashMessage($type) {
        session_start();
        if (isset($_SESSION['flash_messages'][$type])) {
            $msg = $_SESSION['flash_messages'][$type];
            unset($_SESSION['flash_messages'][$type]);
            return '<div class="' . $type . '">' . $msg . '</div>';
        }
            return '';
    }
?>