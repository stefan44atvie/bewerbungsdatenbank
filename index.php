<?php 
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);
    
    // require "components/database/db_connect.php";
    // require_once ('components/inc/check_remote_licence.php');

    include "components/loadplugins.php";

    $current_page = getCurrentPage();

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name=“description“ content="Die Digitale Seele ist der Blog für Technik- und Online-Interessierte in Österreich">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <link rel="stylesheet" href="components/css/ds_general.css">
    <link rel="stylesheet" href="components/css/ds_fonts.css">
    <link rel="stylesheet" href="components/css/ds_indexpage.css">
    
    <title>Meine Bewerbungsdatenbank</title>
</head>
<body class="screen">
    <div class="digseele_headerarea">

    </div>
    <?php include("components/inc/mainmenu.php"); ?>
   <div id="ds_mainwindow">
        <div id="ds_thirdintropage">
            <h2 class="display-2 fw-bold text-uppercase">HELLO WIE GEHTS?</h2> 
        </div>
   </div>



<script src="components/scripts/tagging_color.js"></script>
<script src="components/scripts/category_color.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>
</html>