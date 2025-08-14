<?php 
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);
    
    require "../components/database/db_connect.php";
    // require_once ('components/inc/check_remote_licence.php');

    include "../components/loadplugins.php";
    include "inc/berechnungen.php";
    include "../components/config/timezone.php";

    $config = require __DIR__ . '/../components/config/timezone.php';

    if (!empty($config['timezone'])) {
        date_default_timezone_set($config['timezone']);
    }

    $settings = getAppSettings($connect);
    $app_version = $settings['version'];
    $projektname = $settings['projektname'];

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


    /* ---- Tabelle Aktuelle Bewerbungen ---- */
     $sql_recentapplicationstable = "select * from bewerbungen order by bewerbungsdatum DESC limit 5";
    $resRAT = mysqli_query($connect,$sql_recentapplicationstable);

    if(mysqli_num_rows($resRAT)  > 0) {
     while($rowRAT = mysqli_fetch_array($resRAT, MYSQLI_ASSOC)){
        $application_id = $rowRAT['id'];
        $company = htmlspecialchars($rowRAT['firma'], ENT_QUOTES, 'UTF-8');
        $position = htmlspecialchars($rowRAT['position'], ENT_QUOTES, 'UTF-8');
        $date_applied = htmlspecialchars($rowRAT['bewerbungsdatum'], ENT_QUOTES, 'UTF-8');
        // $antworten = htmlspecialchars($rowRAT['bewerbungsdatum'], ENT_QUOTES, 'UTF-8');
        $application_status = htmlspecialchars($rowRAT['status'], ENT_QUOTES, 'UTF-8');
      
        $date_applied = formatCreateDate($date_applied);

        $sql_countAntworten = "select count(id) as anzahl from firmen_antworten where fk_bewerbungs_id = $application_id";
        $resCAW = mysqli_query($connect,$sql_countAntworten);
        $rowCAW = mysqli_fetch_assoc($resCAW);
        $antworten = $rowCAW['anzahl'];


        $tbodyRECB .= "
            <tr>
                <td><span class='bold_text'>$company</span></td>
                <td>$position</td>
                <td>$date_applied</td>
                <td><span class='applic_status'>$application_status</span></td>
                <td>$antworten</td>
                <td>
                    <div class='btn-group w-100' role='group' aria-label='Basic mixed styles example'>
                        <a type='button' class='btn btn-sm btn-primary button_shadow text-white' href='details.php?id=$application_id&details=application'>Details</a>
                        <a type='button' class='btn btn-sm btn-warning button_shadow text-white' href='create.php?action=addresponse&company=$company'>Antworten</a>
                        <a type='button' class='btn btn-sm btn-success button_shadow text-white' href='update.php?id=$application_id&action=updateapplication'>Bearbeiten</a>
                        <a type='button' class='btn btn-sm btn-danger button_shadow text-white' href='inc/delete.php?id=$application_id&deleteapplication' onclick='return confirm(\"Möchten Sie diesen Auftrag wirklich löschen?\")'>Löschen</a>
                    </div>      
                </td>
            </tr>
            ";
        };
 }else {
     $tbodyRECB = "<tr>
            <td colspan='6' class='text-center text-muted py-3'>
                Aktuell sind keine Alben vorhanden.
            </td>
        </tr>";
 }

 /* ---- ZIP changed Files ---- */
    if (isset($_POST['create_zip'])) {
        // require 'changed_zip_creator.php';
        $outputPath = realpath(__DIR__ . '/..') . '/bewerbungsdatenbank_changedfiles.zip';
        createChangedFilesZip('/Applications/XAMPP/xamppfiles/htdocs/PHP/bewerbungsdatenbank', $outputPath);
    }
/* ---- ZIP changed Files ---- */

/* ---- Umgebungserkennung ---- */
//Auf dem Raspi wird der Button 'ZIP changed files' nicht angezeigt, sondern nur auf localhost

$umgebung = 'web';
$hostname = gethostname();

if (strpos($hostname, 'raspi') !== false || strpos(__DIR__, '/home/pi') !== false) {
    $umgebung = 'raspi';
} elseif (
    strpos($_SERVER['HTTP_HOST'] ?? '', '192.168.') !== false ||
    ($_SERVER['REMOTE_ADDR'] ?? '') === '127.0.0.1' ||
    ($_SERVER['REMOTE_ADDR'] ?? '') === '::1' ||  // IPv6 localhost ergänzen
    ($_SERVER['REMOTE_ADDR'] ?? '') === 'localhost'
) {
    $umgebung = 'localhost';
}
// Steuere die Anzeige mit einer CSS-Klasse oder Inline-Style
$style = ($umgebung === 'raspi' || $umgebung === 'web') ? 'style="display:none;"' : '';
$styleUpdate = ($umgebung === 'localhost') ? 'style="display:none;"' : '';

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name=“description“ content="Die Digitale Seele ist der Blog für Technik- und Online-Interessierte in Österreich">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <link rel="stylesheet" href="../components/css/bwd_general.css">
    <link rel="stylesheet" href="../components/css/bwd_fonts.css">
    <link rel="stylesheet" href="../components/css/bwd_admin_dashboard.css">
    <link rel="stylesheet" href="../components/css/bwd_updateprozess_fonts.css">
    
    <title>Dashboard - Meine Bewerbungsdatenbank</title>
</head>
<body class="screen">
    <!-- Modal für UpdateMeldungen-->
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Update-Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-body">
                <!-- Update-Nachricht wird hier angezeigt -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
                    <button type="button" id="reload-btn" class="btn btn-success" onclick="location.reload()">🔁 Seite neu laden</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal für UpdateMeldungen-->
   <?php include ('inc/mainmenu.php'); ?>
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
                <a class="stats_ov_number"><?php echo $erfolgsquote.'%'; ?></a>
                <a class="stats_ov_text">Erfolgsquote</a>
            </div>
        </div>
   </div>
    <div id="updatecheck_ov_titel">
        <h2 class="section_titel">Website - Aktionen</h2>
    </div>
   <div id="updateov_area">
        <button id="update-btn" <?= $styleUpdate ?>
            data-projektname="<?php echo $projektname; ?>"
            data-version="<?php echo $app_version; ?>"
            class="btn btn-primary" <?= $styleUpdate ?>>
            🔄 Update prüfen (v<?php echo $app_version; ?>)
        </button>
        <form method="post" <?= $style ?>>
            <button type="submit" name="create_zip" class="btn btn-primary">
                🔄 ZIP mit geänderten Dateien erstellen
            </button>
        </form>
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
        <div id="appstate_box" class="box_shadow">
            <div class="appsbystate_ov_titel">
                <h2 class="section_titel">Bewerbungen nach Status</h2>
            </div>
            <div id="appsbystate_flexarea">
                <?php echo $tbodyASTAT; ?>
            </div>
        </div>
   </div>
   <div id="recentapplications_area">
        <div id="recentapps_box" class="box_shadow">
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
                    <?php echo $tbodyRECB; ?>
                </table>
            </div>
        </div> 
   </div>
   <div id="quickactions_area">
        <div id="qhickactions_box" class="box_shadow">
            <div class="quickactions_ov_titel">
                <h2 class="section_titel">Aktionen...</h2>
            </div>
            <div id="quickactions_listarea">
                <div class="box_shadow">
                    <a href="create.php?action=addapplication" class="actionbox_titel">Bewerbung anlegen...</a> 
                </div>
                <div class="box_shadow">
                    <a href="create.php?action=addresponse" class="actionbox_titel">Antwort hinzufügen...</a> 
                </div>
                <div class="box_shadow">
                    <a href="listings.php?action=jobinterviews" class="actionbox_titel">Künftige Jobinterviews</a> 
                </div>
                <div class="box_shadow">
                    <a href="statistics.php" class="actionbox_titel">Statistiken kontrollieren...</a> 
                </div>
            </div>
        </div>
   </div>


<script src="../components/scripts/update_check.js"></script>
<script src="../components/scripts/colorize_appstatus.js"></script>
<script src="components/scripts/tagging_color.js"></script>
<script src="components/scripts/category_color.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>
</html>