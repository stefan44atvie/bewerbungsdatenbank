<?php
foreach (glob(__DIR__ . "/plugins/*.php") as $file) {
    include $file;
}
?>