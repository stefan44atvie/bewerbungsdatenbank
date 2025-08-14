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

    $current_page = getCurrentPage();

   
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


/* ---- Werte aus der enum Bewerbungsstatus der Bewerbung aus der Tabelle bewerbungen holen ---- */
        $sqlEnumSTAT = "SHOW COLUMNS FROM `bewerbungen` LIKE 'status'";
        $resEnumSTAT = mysqli_query($connect, $sqlEnumSTAT);
        $rowEnumSTAT = mysqli_fetch_assoc($resEnumSTAT);

        // Feld "Type" enthält den ENUM-String z. B. "enum('E-Mail','Online','Post')"
        $type = $rowEnumSTAT['Type'];

        preg_match("/^enum\((.*)\)$/", $type, $matchesSTAT);
        $enumStrSTAT = $matchesSTAT[1]; // Inhalt zwischen den Klammern

        $enumValuesSTAT = array_map(function($valSTAT) {
            return trim($valSTAT, " '");
        }, explode(",", $enumStrSTAT));

        $status_filter = $_GET['status_filter'] ?? 'AlleStatus';
        $options_selSTAT = "";
        foreach($enumValuesSTAT as $valSTAT){
        $checked = ($status_filter === $valSTAT) ? "checked" : "";
        $options_selSTAT .= "
            <div class='form-check form-check-inline'>
                <input class='form-check-input' type='radio' name='status_filter' id='radio_$valSTAT' value='$valSTAT' $checked>
                <label class='form-check-label' for='radio_$valSTAT'>$valSTAT</label>
            </div>";
    }
/* ---- Werte aus der enum Bewerbungsstatus der Bewerbung aus der Tabelle bewerbungen holen ---- */


/* ---- alle Bewerbungen ---- */
/* ========================== */

/* ---- Tabelle Aktuelle Bewerbungen ---- */
    $status_filter = $_GET['status_filter'] ?? 'AlleStatus';

    if ($status_filter === 'AlleStatus') {
        $sql_alleBewerbungen = "SELECT * FROM bewerbungen ORDER BY bewerbungsdatum DESC";
    } else {
        $status_safe = mysqli_real_escape_string($connect, $status_filter);
        $sql_alleBewerbungen = "SELECT * FROM bewerbungen WHERE status = '$status_safe' ORDER BY bewerbungsdatum DESC";
    }    $resABW = mysqli_query($connect,$sql_alleBewerbungen);

    if(mysqli_num_rows($resABW)  > 0) {
     while($rowABW = mysqli_fetch_array($resABW, MYSQLI_ASSOC)){
        $application_id = $rowABW['id'];
        $company = htmlspecialchars($rowABW['firma'], ENT_QUOTES, 'UTF-8');
        $position = htmlspecialchars($rowABW['position'], ENT_QUOTES, 'UTF-8');
        $date_applied = htmlspecialchars($rowABW['bewerbungsdatum'], ENT_QUOTES, 'UTF-8');
        // $antworten = htmlspecialchars($rowABW['bewerbungsdatum'], ENT_QUOTES, 'UTF-8');
        $application_status = htmlspecialchars($rowABW['status'], ENT_QUOTES, 'UTF-8');
      
        $date_applied = formatCreateDateJD($date_applied);

        $sql_countAntworten = "select count(id) as anzahl from firmen_antworten where fk_bewerbungs_id = $application_id";
        $resCAW = mysqli_query($connect,$sql_countAntworten);
        $rowCAW = mysqli_fetch_assoc($resCAW);
        $antworten = $rowCAW['anzahl'];


        $tbodyABW .= "
            <tr>
                <td><span class='bold_listtext'>$company</span></td>
                <td>$position</td>
                <td>$date_applied</td>
                <td><span class='applic_status'>$application_status</span></td>
                <td>$antworten</td>
                <td>
                    <div class='btn-group w-100' role='group' aria-label='Basic mixed styles example'>
                        <a type='button' class='btn btn-sm btn-primary button_shadow text-white' href='details.php?id=$application_id&details=application'>Details</a>
                        <a type='button' class='btn btn-sm btn-warning button_shadow text-white' href='create.php?id=$application_id&action=addresponse&company=$company'>Antworten</a>
                        <a type='button' class='btn btn-sm btn-success button_shadow text-white' href='update.php?id=$application_id&action=updateapplication'>Bearbeiten</a>
                        <a type='button' class='btn btn-sm btn-danger button_shadow text-white' href='inc/delete.php?id=$application_id&deleteapplication' onclick='return confirm(\"Möchten Sie diesen Auftrag wirklich löschen?\")'>Löschen</a>
                    </div>      
                </td>
            </tr>
            ";
        };
 }else {
     $tbodyABW = "<tr>
            <td colspan='6' class='text-center text-muted py-3'>
                Aktuell sind keine Bewerbungen mit diesem Status vorhanden.
            </td>
        </tr>";
 }

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
    <link rel="stylesheet" href="../components/css/bwd_admin_bewerbungen.css">
    
    <title>Bewerbungen - Meine Bewerbungsdatenbank</title>
</head>
<body class="screen">
    <?php include ('inc/mainmenu.php'); ?>
    <div id="bewerbungen_titlearea">
        <div class="pagetitel">
            <h1 class="page_titel">Bewerbungen</h1>
        </div>
        <div class="added_options">
            <div>
                <a class="btn insidemenu_link" href="create.php?action=addapplication">neue Bewerbung...</a>
            </div>
            <div>
                <button class="btn insidemenu_link" disabled>Export PDF...</button>
            </div>
            <div>
                <button class="btn insidemenu_link" data-table-id="TabelleBewerbungen" data-filename="allebewerbungen.csv">Export CSV...</button>
            </div>
        </div>
    </div>
    <div id="bewerbungen_contentarea">
        <div id="area_list_bewerbungen" class="box_shadow">
            <div id="field_appl_selstatus">
                <form method="GET" id="statusFilterForm">
                    <?php
                    $checkedAll = (!isset($_GET['status_filter']) || $_GET['status_filter'] === 'AlleStatus') ? 'checked' : '';
                    echo "
                    <div class='form-check form-check-inline'>
                        <input class='form-check-input' type='radio' name='status_filter' id='status_all' value='AlleStatus' $checkedAll>
                        <label class='form-check-label' for='status_all'>Alle Bewerbungen</label>
                    </div>";
                    ?>
                    <?= $options_selSTAT ?>
                </form>
                <script>
                    document.querySelectorAll('input[name="status_filter"]').forEach(el => {
                        el.addEventListener('change', function () {
                            document.getElementById('statusFilterForm').submit();
                        });
                    });
                </script>
                <div id="bewerbungen_liste">
                    <table class ="table table-striped" id="TabelleBewerbungen">
                        <tr>
                            <th>Firma</th>
                            <th>Position</th>
                            <th>Datum der Bewerbung</th>
                            <th>Status</th>
                            <th>Antworten</th>
                            <th class="text-center">Optionen</th>
                        </tr>
                        <tr>
                            <?php echo $tbodyABW; ?>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>


<script src="../components/scripts/update_check.js"></script>
<script src="../components/scripts/colorize_appstatus.js"></script>
<script src="components/scripts/tagging_color.js"></script>
<script src="components/scripts/category_color.js"></script>
<script src="../components/scripts/downloadtableasCSV.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>
</html>