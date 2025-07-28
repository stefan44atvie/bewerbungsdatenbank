<?php 
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    require "../components/database/db_connect.php";
    // require_once ('components/inc/check_remote_licence.php');

    include "../components/loadplugins.php";
    include "inc/berechnungen.php";

    $current_page = getCurrentPage();

    // Alle Status-Einträge abfragen
    $sql = "SELECT COUNT(id) AS Anzahl, status FROM bewerbungen GROUP BY status ORDER BY status";
    $result = $connect->query($sql);

    if ($result && $result->num_rows > 0) {
        $tbodyASTAT = "";

        while ($row = $result->fetch_assoc()) {
            $tbodyASTAT .= "
                <div class='appsbystate_box box_shadow'>
                    <span class='appstate_box'>
                        <a class='appsbystatebox_text'>" . htmlspecialchars($row['status']) . "</a>
                    </span>
                    <a class='appsbystatebox_number'>" . htmlspecialchars($row['Anzahl']) . "</a>
                </div>
            ";
        }
    } else {
        $tbodyASTAT = "Keine Bewerbungen gefunden.";
    }

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name=“description“ content="Die Digitale Seele ist der Blog für Technik- und Online-Interessierte in Österreich">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <link rel="stylesheet" href="../components/css/bwd_general.css">
    <link rel="stylesheet" href="../components/css/bwd_fonts.css">
    <link rel="stylesheet" href="../components/css/bwd_admin_dashboard.css">
    
    <title>Meine Bewerbungsdatenbank</title>
</head>
<body class="screen">
   <div class="bwd_headermenu_area">
        <div id="projektname">
            <a class="projektname_text">Bewerbungsdatenbank</a>
        </div>
        <div>
            <a class="menu_titels">Dashboard</a>
        </div>
        <div>
            <a class="menu_titels">Bewerbungen</a>
        </div>
        <div>
            <a class="menu_titels">Analysen</a>
        </div>
        <div>
            <a class="menu_titels">Antworten</a>
        </div>
        <div>
            <a class="menu_titels">Erinnerungen</a>
        </div>
        <div>
            <a class="menu_titels">Statistiken</a>
        </div>
   </div>
   <div id="dashboard_titlearea">
        <h1 class="page_titel">Dashboard <small class='text-muted'>Übersicht deiner Bewerbungsaktivitäten</small></h1>
   </div>
   <div id="stats_overview_area">
        <div class="stats_overviewbox box_shadow">
            <div class="stat_pic">
                <img src="../components/media/icons/icons8-statistiken-64.png" alt="Statistik Bewerbungen gesamt">
            </div>
            <div class="stat_content">
                <a class="stats_ov_number"><?php echo $anzahl_bewerbungen; ?></a>
                <a class="stats_ov_text">Bewerbungen gesamt</a>
            </div>
        </div>
        <div class="stats_overviewbox box_shadow">
            <div class="stat_pic">
                <img src="../components/media/icons/icons8-antworten-96.png" width="64px" alt="Statistik Antworten gesamt">
            </div>
            <div class="stat_content">
                <a class="stats_ov_number"><?php echo $anzahl_antworten; ?></a>
                <a class="stats_ov_text">Antworten gesamt</a>
            </div>
        </div>
        <div class="stats_overviewbox box_shadow">
            <div class="stat_pic">
                <img src="../components/media/icons/icons8-positiv-96.png" width="64px" alt="Statistik positive Antworten">
            </div>
            <div class="stat_content">
                <a class="stats_ov_number"><?php echo $anzahl_positiveAntworten; ?></a>
                <a class="stats_ov_text">positive Antworten</a>
            </div>
        </div>
        <div class="stats_overviewbox box_shadow">
            <div class="stat_pic">
                <img src="../components/media/icons/icons8-finanzieller-erfolg-100.png" width="64px" alt="Statistik Erfolgsquote">
            </div>
            <div class="stat_content">
                <a class="stats_ov_number"><?php echo $anzahl_bewerbungen; ?></a>
                <a class="stats_ov_text">Erfolgsquote</a>
            </div>
        </div>
   </div>
   <div id="answers_overviewarea">
        <div class="answers_ov_titel">
            <h2 class="section_titel">Übersicht der Antwort-Ergebnisse</h2>
        </div>
        <div id="answers_ov_area">
            <div class="answers_box erfolgsbox box_shadow">
                <img src="../components/media/icons/icons8-positiv-96.png" width="64px" alt="Anzahl positive Antworten">
                <a class="answers_ov_number"><?php echo $anzahl_positiveAntworten; ?></a>
                <a class="answers_ov_text">Positiv</a>
                <a class="answers_small_text">Interessierte Unternehmen</a>
            </div>
            <div class="answers_box negativbox box_shadow">
                <img src="../components/media/icons/icons8-kreuz-markierungs-button-emoji-96.png" width="64px" alt="Anzahl negative Antworten">
                <a class="answers_ov_number"><?php echo $anzahl_negativeAntworten; ?></a>
                <a class="answers_ov_text">Negativ</a>
                <a class="answers_small_text">Absagen erhalten</a>
            </div>
            <div class="answers_box sanduhrbox box_shadow">
                <img src="../components/media/icons/icons8-sanduhr-80.png" width="64px" alt="Anzahl ausstehende Antworten">
                <a class="answers_ov_number"><?php echo $anzahl_ausstehendeAntworten; ?></a>
                <a class="answers_ov_text">Ausstehend</a>
                <a class="answers_small_text">Warten auf Entscheidung</a>            
            </div>
            <div class="answers_box neutralbox box_shadow">
                <img src="../components/media/icons/icons8-neutral-100.png" width="64px" alt="Anzahl meutraler Antworten">
                <a class="answers_ov_number"><?php echo $anzahl_neutraleAntworten; ?></a>
                <a class="answers_ov_text">Neutral</a>
                <a class="answers_small_text">Bestätigungen</a>                        
            </div>
        </div>
   </div>
   <div id="applicationsbystate_area">
        <div class="appsbystate_ov_titel">
            <h2 class="section_titel">Bewerbungen nach Status</h2>
        </div>
        <div id="appsbystate_flexarea">
            <?php echo $tbodyASTAT; ?>
        </div>
   </div>
   <div id="recentapplications_area">
        <div class="recentapps_ov_titel">
            <h2 class="section_titel">Aktuelle Bewerbungen...</h2>
        </div>
        <div id="table_area">
            <table class="table table-striped">
                <tr>
                    <th>Firma</th>
                    <th>Position</th>
                    <th>Bewerbungsdatum</th>
                    <th>Status</th>
                    <th>Antworten</th>
                    <th>Optionen</th>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>
   </div>



<script src="components/scripts/tagging_color.js"></script>
<script src="components/scripts/category_color.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>
</html>