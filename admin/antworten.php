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


/* ---- Werte aus der enum Antworten der Bewerbung aus der Tabelle bewerbungen holen ---- */
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

/* ---- Tabelle Antworten ---- */
    $sql_alleAntworten = "SELECT * FROM firmen_antworten ORDER BY antwort_datum DESC";
    $resAAN = mysqli_query($connect,$sql_alleAntworten);

    if(mysqli_num_rows($resAAN)  > 0) {
     while($rowAAN = mysqli_fetch_array($resAAN, MYSQLI_ASSOC)){
        $antwort_id = $rowAAN['id'];
        $fk_bewerbungs_id = htmlspecialchars($rowAAN['fk_bewerbungs_id'], ENT_QUOTES, 'UTF-8');
        $antwort_datum = htmlspecialchars($rowAAN['antwort_datum'], ENT_QUOTES, 'UTF-8');
        $antwort_typ = htmlspecialchars($rowAAN['antwort_typ'], ENT_QUOTES, 'UTF-8');
        $antwort_ergebnis = htmlspecialchars($rowAAN['antwort_ergebnis'], ENT_QUOTES, 'UTF-8');
        $kontaktperson = htmlspecialchars($rowAAN['kontaktperson'], ENT_QUOTES, 'UTF-8');
        $followup_required = htmlspecialchars($rowAAN['followup_required'], ENT_QUOTES, 'UTF-8');
      
        if($followup_required==1){
            $followup_required = "ja";
        }else if ($followup_required==0){
            $followup_required = "nein";
        }
        $antwort_datum = formatCreateDate($antwort_datum);

       if (!empty($fk_bewerbungs_id) && is_numeric($fk_bewerbungs_id)) {
        $sql_BewDetails = "SELECT * FROM bewerbungen WHERE id = $fk_bewerbungs_id";
        $resBWD = mysqli_query($connect, $sql_BewDetails);

        if ($resBWD && mysqli_num_rows($resBWD) > 0) {
            $rowBWD = mysqli_fetch_assoc($resBWD);
            $firma = $rowBWD['firma'];
            $position = $rowBWD['position'];
        } else {
            $firma = "<i>Unbekannt</i>";
            $position = "";
        }
        } else {
            $firma = "<i>Ungültige ID</i>";
            $position = "";
        }


        $tbodyABW .= "
            <tr>
                <td>$antwort_datum</td>
                <td>
                    <span class='bold_listtext'>$firma</span>
                    <br>
                    $position
                </td>
                <td>$antwort_typ</td>
                <td><span class='response_status'>$antwort_ergebnis</span></td>
                <td>$kontaktperson</td>
                <td>$followup_required</td>
                <td>
                    <div class='btn-group w-100' role='group' aria-label='Basic mixed styles example'>
                        <a type='button' class='btn btn-sm btn-primary button_shadow text-white' href='details.php?id=$antwort_id&details=response'>Details</a>
                        <a type='button' class='btn btn-sm btn-success button_shadow text-white' href='update.php?id=$antwort_id&action=updateresponse'>Bearbeiten</a>
                        <a type='button' class='btn btn-sm btn-danger button_shadow text-white' href='inc/delete.php?id=$antwort_id&deleteresponse' onclick='return confirm(\"Möchten Sie diesen Auftrag wirklich löschen?\")'>Löschen</a>
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
    <link rel="stylesheet" href="../components/css/bwd_admin_antworten.css">
    
    <title>Bewerbungen - Meine Bewerbungsdatenbank</title>
</head>
<body class="screen">
    <?php include ('inc/mainmenu.php'); ?>
    <div id="antworten_titlearea">
        <div class="pagetitel">
            <h1 class="page_titel">Antworten</h1>
        </div>
        <div class="added_options">
            <div>
                <a class="btn insidemenu_link" href="create.php?action=addresponse">neue Antwort...</a>
            </div>
        </div>
    </div>
    <div id="antworten_contentarea">
        <div id="area_list_antworten" class="box_shadow">
            <div id="antworten_liste">
                <table class ="table table-striped" id="TabelleAntworten">
                    <tr>
                        <th>Datum der Antwort</th>
                        <th>Firma und Position</th>
                        <th>Art</th>
                        <th>Antwort</th>
                        <th>Kontaktperson</th>
                        <th>FollowUp?</th>
                        <th class="text-center">Optionen</th>
                    </tr>
                    <tr>
                        <?php echo $tbodyABW; ?>
                    </tr>
                </table>
            </div>
        </div>
    </div>


<script src="../components/scripts/update_check.js"></script>
<script src="../components/scripts/colorize_responsestatus.js"></script>
<script src="components/scripts/tagging_color.js"></script>
<script src="components/scripts/category_color.js"></script>
<script src="../components/scripts/downloadtableasCSV.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>
</html>