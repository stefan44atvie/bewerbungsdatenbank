<?php 
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
//     echo "<pre>";
// print_r($_POST);
// echo "</pre>";
    require "../components/database/db_connect.php";
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // require_once ('components/inc/check_remote_licence.php');

    include "../components/loadplugins.php";
    include "inc/berechnungen.php";
    include "../components/config/timezone.php";

    $config = require __DIR__ . '/../components/config/timezone.php';

    if (!empty($config['timezone'])) {
        date_default_timezone_set($config['timezone']);
    }

    $current_page = getCurrentPage();

    // $id = $_GET['id'];

    $details = $_GET['details'];

    if($details === "application"){
        $object_id = $_GET['id'];

        $sql_detailsapplication = "select * from bewerbungen where id = $object_id";
        $resDAP = mysqli_query($connect,$sql_detailsapplication);
        $rowDAP = mysqli_fetch_assoc($resDAP);
        $firma = $rowDAP['firma'];
        $position = $rowDAP['position'];
        $fullCompPosition = formatFullCompanyPosition($firma, $position);
        $bewerbungsdatum = $rowDAP['bewerbungsdatum'];
        $methode = $rowDAP['bewerbungsmethode'];
        $status = $rowDAP['status'];
        $priority = $rowDAP['priority'];
        $salaryrange = $rowDAP['gehaltsangaben'];
        $updated_at = $rowDAP['updated_at'];
        $job_beschreibung = $rowDAP['jobbeschreibung'];
        $voraussetzungen = $rowDAP['voraussetzungen'];
        $job_url = $rowDAP['job_url'];
        $notizen = $rowDAP['notizen'];

        $salaryrange = formatBetragCurrency($salaryrange);

        if($job_beschreibung == ""){
            $job_beschreibung = "<i>Keine näheren Details zu diesen Jobangebot verfügbar</i>";
        }
        if($voraussetzungen == ""){
            $voraussetzungen = "<i>Keine näheren Details zu diesen Jobangebot verfügbar</i>";
        }
        if($notizen == ""){
            $notizen = "<i>Keine näheren Details zu diesen Jobangebot verfügbar</i>";
        }

        $bewerbungsdatum = formatCreateDate($bewerbungsdatum);
        $updated_at = formatCreateDate($updated_at);

    }else if($details === "response"){
        $object_id = $_GET['id'];
        // echo $details; 

        $sql_detailsresponse = "select * from firmen_antworten where id = $object_id";
        $resDRES = mysqli_query($connect,$sql_detailsresponse);
        $rowDRES = mysqli_fetch_assoc($resDRES);
        $fk_bewerbungs_id = $rowDRES['fk_bewerbungs_id'];
        $antwort_datum = $rowDRES['antwort_datum'];
        $antwort_ergebnis = $rowDRES['antwort_ergebnis'];
        $antwort_typ = $rowDRES['antwort_typ'];
        $antwort_inhalt = $rowDRES['antwort_inhalt'];
        $next_steps = $rowDRES['next_steps'];
        $kontaktperson = $rowDRES['kontaktperson'];
        $kontakt_email = $rowDRES['kontakt_email'];
        $kontakt_telefon = $rowDRES['kontakt_telefon'];
        $followup_required = $rowDRES['followup_required'];
        $followup_date = $rowDRES['followup_date'];
        $created_at = $rowDRES['created_at'];

        $sql_responseBewDetails = "select * from bewerbungen where id = $fk_bewerbungs_id";
        $resRBD = mysqli_query($connect,$sql_responseBewDetails);
        $rowRBD = mysqli_fetch_assoc($resRBD);
        $resp_firma = $rowRBD['firma'];
        $resp_bewerbung = $rowRBD['position'];

        $response_BewPosition = formatFullAntwortBewerbung($resp_firma, $resp_bewerbung);
        $antwort_datum = formatCreateDate($antwort_datum);
        $followup_date = formatCreateDateJD($followup_date);
        if(!$antwort_inhalt){
            $antwort_inhalt = "<a class='non_available_text'>Keine Details einer allfälligen Antwort verfügbar</a>";
        } 
        if (!$next_steps){
            $next_steps = "<a class='non_available_text'>Keine Details zu weiteren Schritten verfügbar</a>";
        }
        if(!$kontaktperson){
            $kontaktperson = "<span class='non_available_text'>In der email wurde keine Kontaktperson angegeben</span>";
        } 
        if (!$kontakt_email){
            $kontakt_email = "<span class='non_available_text'>In der email wurde keine Kontakt-Email angegeben</span>";
        }
    }
    // echo $details;
    $styleDTAPP = ($details !== 'application') ? 'style="display:none;"' : '';
    $styleDTRE = ($details !== 'response') ? 'style="display:none;"' : '';
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
    <link rel="stylesheet" href="../components/css/bwd_admin_details.css">
    
    <title>Details - Meine Bewerbungsdatenbank</title>
</head>
<body class="screen">
    <?php include ('inc/mainmenu.php'); ?>
        <div id="detailsarea_application" <?= $styleDTAPP ?>>
            <div id="details_titlearea">
                <h1 class="page_titel">Details der Bewerbung <small class='text-muted'><?php echo $fullCompPosition; ?></small></h1>
            </div>
            <div class="content_area">
                <div id="box_details" class="box_shadow">
                    <a class="standardtext"><span class="bold_text">Firma</span>: <?php echo $firma; ?></a>
                    <a class="standardtext text-end"><span class="bold_text">Position</span>: <?php echo $position; ?></a>
                    <a class="standardtext"><span class="bold_text">Bewerbungsdatum</span>: <?php echo $bewerbungsdatum; ?></a>
                    <a class="standardtext text-end"><span class="bold_text">Methode</span>: <?php echo $methode; ?></a>
                    <a class="standardtext"><span class="bold_text">Status</span>: <span class="applic_status"><?php echo $status; ?></span></a>
                    <a class="standardtext text-end"><span class="bold_text">Priorität</span>: <?php echo $priority; ?></a>
                    <a class="standardtext"><span class="bold_text">Gehaltsangaben</span>: <?php echo $salaryrange; ?></a>
                    <a class="standardtext text-end"><span class="bold_text">Bewerbung zuletzt aktualisiert</span>: <?php echo $updated_at; ?></a>
                    <a class="standardtext text_description_box" href="<?php echo $job_url; ?>" target="_blank"><span class="bold_text">URL der Jobanzeige</span>: <?php echo $job_url; ?></a>
                    <a class="standardtext pt-2 "><span class="bold_text details_title">Job-Details</span>:</a>
                    <a class="standardtext text_description_box"><?php echo $job_beschreibung; ?> </a>
                    <a class="standardtext pt-2"><span class="bold_text details_title">Voraussetzungen</span>:</a>
                    <a class="standardtext text_description_box"><?php echo $voraussetzungen; ?> </a>
                    <a class="standardtext pt-2 "><span class="bold_text details_title">Notizen</span>:</a>
                    <a class="standardtext text_description_box"><?php echo nl2br(htmlspecialchars($notizen));; ?> </a>
                </div>
            </div>
            
        </div>
        <div id="detailsarea_response" <?= $styleDTRE ?>>
            <div id="details_titlearea">
                <h1 class="page_titel">Details der Antwort zur Bewerbung <small class='text-muted'><?php echo $response_BewPosition; ?></small></h1>
            </div>
            <div class="content_area">
                <div id="box_details" class="box_shadow">
                    <a class="standardtext"><span class="bold_text">Firma</span>: <?php echo $resp_firma; ?></a>
                    <a class="standardtext text-end"><span class="bold_text">Antwort zur Bewerbung für Position</span>: <?php echo $resp_bewerbung; ?></a>
                    <a class="standardtext"><span class="bold_text">Zeitpunkt der Antwort</span>: <?php echo $antwort_datum; ?></a>
                    <a class="standardtext text-end"><span class="bold_text">Ergebnis</span>: <span class="response_status"><?php echo $antwort_ergebnis; ?></span></a>
                    <a class="standardtext"><span class="bold_text">Antwort-Typ</span>: <?php echo $antwort_typ; ?></a>
                    <a class="standardtext pt-2 fulltext_titel"><span class="bold_text details_title">Antwort der Firma</span>:</a>
                    <a class="standardtext text_description_box"><?php echo $antwort_inhalt; ?> </a>
                    <a class="standardtext pt-2 fulltext_titel"><span class="bold_text details_title">Folge-Punkte</span>:</a>
                    <a class="standardtext text_description_box"><?php echo $next_steps; ?> </a>
                    <a class="standardtext firstline"><span class="bold_text">Kontaktperson</span>: <?php echo $kontaktperson; ?></a>
                    <a class="standardtext text-end"><span class="bold_text">Kontakt-Email</span>: <?php echo $kontakt_email; ?></a>  
                    <a class="standardtext firstline"><span class="bold_text">Follow-Up?</span>: <?php echo $followup_required; ?></a>
                    <a class="standardtext text-end"><span class="bold_text">FollowUp-Datum</span>: <?php echo $followup_date; ?></a>  
                </div>


            </div>
        </div>

<script src="../components/scripts/update_check.js"></script>
<script src="../components/scripts/colorize_appstatus.js"></script>
<script src="../components/scripts/colorize_responsestatus.js"></script>
<script src="components/scripts/tagging_color.js"></script>
<script src="components/scripts/category_color.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>
</html>