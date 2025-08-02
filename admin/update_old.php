<?php 
// echo '<pre>';
// print_r($_POST);
// echo '</pre>';
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require "../components/database/db_connect.php";
    // mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // require_once ('components/inc/check_remote_licence.php');

    include "../components/loadplugins.php";
    include "inc/berechnungen.php";
   
    $new_bewerbungsdatum = null;  // oder ''
    function toMysqlDatetime($date, $time) {
        $dt = DateTime::createFromFormat('d.m.Y H:i', "$date $time");
        return $dt ? $dt->format('Y-m-d H:i:s') : null;
    }

    $config = require __DIR__ . '/../components/config/timezone.php';

    if (!empty($config['timezone'])) {
        date_default_timezone_set($config['timezone']);
    }

    $current_page = getCurrentPage();

   $action = $_GET['action'] ?? '';
$app_id = $_GET['id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_updateapplication'])) {
    // Verarbeitung der Formularwerte
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
    // Hier kannst du z. B. auch updateApplication($connect, $_POST, $app_id); aufrufen
}


    // echo $action; 
    // var_dump($app_id);
    // die();

    if($action == "updateapplication"){
       
        $sql_updappdetails = "select * from bewerbungen where id = $app_id";
        $resUAD = mysqli_query($connect,$sql_updappdetails);
        $rowUAD = mysqli_fetch_assoc($resUAD);
        $old_firma = $rowUAD['firma'];
        $old_position = $rowUAD['position'];
        $old_appdate = $rowUAD['bewerbungsdatum'];
        $old_appmethod = $rowUAD['bewerbungsmethode'];
        $current_value = $old_appmethod;
        $old_apptime = formatTimeForDatabase($old_appdate);
        $old_appdate = formatDateForDatabase($old_appdate);
        $old_appstatus = $rowUAD['status'];
        $currentstatus_value = $old_appstatus;
        $old_priority = $rowUAD['priority'];
        $currentpriority_value = $old_priority;
        $oldsalary = $rowUAD['gehaltsangaben'];
        $oldsalaryINT = $rowUAD['gehaltsangaben'];
        $old_joburl = $rowUAD['job_url'];
        $old_jobbeschreibung = $rowUAD['jobbeschreibung'];
        $old_voraussetzungen = $rowUAD['voraussetzungen'];
        $old_notizen = $rowUAD['notizen'];

            var_dump($_POST['updateapp_date']);
var_dump($_POST['updateapp_time']);

        if($oldsalary==""){
            $oldsalary = "nicht angegeben";
        }
        if($old_joburl==""){
            $old_joburl = "nicht angegeben";
        }
        if($old_jobbeschreibung==""){
            $old_jobbeschreibung = "nicht angegeben";
        }
        if($old_voraussetzungen==""){
            $old_voraussetzungen = "nicht angegeben";
        }
        if($old_notizen==""){
            $old_notizen = "nicht angegeben";
        }

        $fullUpdateBewerbung = formatFullAntwortBewerbung($old_firma,$old_position);

        include "inc/func_selmethod.php";
        include "inc/func_selstatus.php";
        include "inc/func_selpriority.php";

        if (isset($_POST["btn_updateapplication"])) {

            /* ---- Eingabe-Werte sichern ---- */
            $new_firma = isset($_POST['updateapp_company']) && $_POST['updateapp_company'] !== '' ? $_POST['updateapp_company'] : $old_firma;
            $new_position = isset($_POST['updateapp_position']) && $_POST['updateapp_position'] !== '' ? $_POST['updateapp_position'] : $old_position;
            $new_appdate = isset($_POST['updateapp_date']) && $_POST['updateapp_date'] !== '' ? $_POST['updateapp_date'] : $old_appdate;
            $new_apptime = isset($_POST['updateapp_time']) && $_POST['updateapp_time'] !== '' ? $_POST['updateapp_time'] : $old_apptime;

            $new_bewerbungsdatum = "";
            if (!empty($new_appdate) && !empty($new_apptime)) {
                $new_bewerbungsdatum = toMysqlDatetime($new_appdate, $new_apptime);            
            } else {
                $new_bewerbungsdatum = toMysqlDatetime($old_appdate, $old_apptime);            
            }

            var_dump($new_bewerbungsdatum);
            die();
            $new_appmethod = isset($_POST['updateapp_method']) && $_POST['updateapp_method'] !== '' ? $_POST['updateapp_method'] : $old_appmethod;
            $new_appstatus = isset($_POST['updateapp_status']) && $_POST['updateapp_status'] !== '' ? $_POST['updateapp_status'] : $old_appstatus;
            $new_apppriority = isset($_POST['updateapp_priority']) && $_POST['updateapp_priority'] !== '' ? $_POST['updateapp_priority'] : $old_priority;
            $new_appexpsalary = isset($_POST['updateapp_expsalary']) && $_POST['updateapp_expsalary'] !== '' ? $_POST['updateapp_expsalary'] : $oldsalaryINT;
            $new_appjoburl = isset($_POST['updateapp_joburl']) && $_POST['updateapp_joburl'] !== '' ? $_POST['updateapp_joburl'] : $old_joburl;
            $new_jobbeschreibung = isset($_POST['textfield_description']) && $_POST['textfield_description'] !== '' ? $_POST['textfield_description'] : $old_jobbeschreibung;
            $new_voraussetzungen = isset($_POST['textfield_requirements']) && $_POST['textfield_requirements'] !== '' ? $_POST['textfield_requirements'] : $old_voraussetzungen;
            $new_notizen = isset($_POST['textfield_notizen']) && $_POST['textfield_notizen'] !== '' ? $_POST['textfield_notizen'] : $old_notizen;

            $date_update = date("Y-m-d H:i:s");
            $new_bewerbungsdatum = $new_appdate.' '.$new_apptime;

            // if($new_appstatus != $old_appstatus){
            //    if ($new_appstatus != $old_appstatus) {
            //         // 1. Statusfeld aktualisieren
            //         $stmtNewAppStatus = $connect->prepare("UPDATE `bewerbungen` SET status = ? WHERE id = ?");
            //         if (!$stmtNewAppStatus) {
            //             throw new Exception("Status-Update konnte nicht vorbereitet werden: " . $connect->error);
            //         }
            //         $stmtNewAppStatus->bind_param("si", $new_appstatus, $app_id);
            //         if (!$stmtNewAppStatus->execute()) {
            //             die("Fehler beim Status-Update: " . $stmtNewAppStatus->error);
            //         }

            //         // 2. Notizen erweitern
            //         $created_at_update = date("d.m.Y H:i");
            //         $statuswechsel_notiz = $created_at_update . ": Status wurde geändert von '$old_appstatus' auf '$new_appstatus'";
            //         $new_notizen = $old_notizen . "<br>" . $statuswechsel_notiz;
            //     }
                    
            //         $stmtNewAppStatus->close();
            //     }            
            }
            // var_dump($new_bewerbungsdatum);
            // die();
            $stmt = $connect->prepare("UPDATE bewerbungen SET 
                firma = ?,
                position = ?,
                bewerbungsdatum = ?,
                jobbeschreibung = ?,
                voraussetzungen = ?,
                gehaltsangaben = ?,
                bewerbungsmethode = ?,
                job_url = ?,
                status = ?,
                priority = ?,
                notizen = ?,
                updated_at = ?
                WHERE id = ?");

            $stmt->bind_param(
                "ssssssssssssi",
                $new_firma,
                $new_position,
                $new_bewerbungsdatum,
                $new_jobbeschreibung,
                $new_voraussetzungen,
                $new_appexpsalary,
                $new_appmethod,
                $new_appjoburl,
                $new_appstatus,
                $new_apppriority,
                $new_notizen,
                $date_update,
                $app_id
            );

            if (!$stmt->execute()) {
                die("Fehler bei der Aktualisierung: " . $stmt->error);
            }

            $stmt->close();

            setFlashMessage('success', 'Bewerbung wurde erfolgreich aktualisiert');
            header("Location: bewerbungen.php");
            exit;
        }

    

    
    
    $styleUPAP = ($action !== 'updateapplication') ? 'style="display:none;"' : '';
    // $styleUPRES = ($action !== 'addresponse') ? 'style="display:none;"' : '';

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Die Digitale Seele ist der Blog für Technik- und Online-Interessierte in Österreich">    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <link rel="stylesheet" href="../components/css/bwd_general.css">
    <link rel="stylesheet" href="../components/css/bwd_fonts.css">
    <link rel="stylesheet" href="../components/css/bwd_admin_dashboard.css">
    <link rel="stylesheet" href="../components/css/bwd_admin_update.css">
    
    <title>Meine Bewerbungsdatenbank</title>
</head>
<body class="screen">
    <?php include ('inc/mainmenu.php'); ?>
   <div id="update_area">
        <div id="updatearea_application" <?= $styleUPAP ?>>
            <div id="updateapplication_box">
                <div class="updatebox_title">
                    <h2 class="section_titel">Bewerbung aktualisieren <small class='text-muted'><?php echo $fullUpdateBewerbung; ?></small></h2>
                </div>
                <div id="updateapplication_content">
                    <div id="updateappformular_box">
                        Test Neu
                        <form class="w-100 pb-2" method="POST" action="<?= htmlspecialchars($_SERVER['SCRIPT_NAME']) . '?id='.$app_id.'&'.'action=updateapplication' ?>" enctype="multipart/form-data">    
                            <div id="updateapp_inputformular">
                                <div id="field_appl_company">
                                    <label for="floatingInputGrid" class="bg-success input_label">Firma</label>
                                    <input type="text" class="form-control button_shadow w-100" name="updateapp_company"  id="updateapp_company" placeholder="<?php echo $old_firma; ?>">
                                </div>
                                <div id="field_appl_position">
                                    <label for="floatingInputGrid" class="bg-success input_label">Position</label>
                                    <input type="text" class="form-control button_shadow w-100" name="updateapp_position"  id="updateapp_position" placeholder="<?php echo $old_position; ?>">
                                </div>
                                <div id="field_appl_appdate">
                                    <label for="floatingInputGrid" class="bg-success input_label">Bewerbungsdatum *</label>
                                    <input type="date" class="form-control button_shadow w-100" name="updateapp_date"  id="updateapp_date" value="<?php echo $old_appdate; ?>">
                                </div>
                                <div id="field_appl_apptime">
                                    <label for="floatingInputGrid" class="bg-success input_label">Uhrzeit der Bewerbung</label>
                                    <input type="time" class="form-control button_shadow w-100" name="updateapp_time"  id="updateapp_time" value="<?php echo $old_apptime; ?>">
                                </div>
                                <div id="field_appl_selmethod">
                                    <label for="updateapp_method" class="bg-success input_label">Bewerbungsmethode</label>
                                    <select id="updateapp_method" class="form-control w-100" name="updateapp_method">
                                        <option value="" disabled <?= empty($current_value) ? 'selected' : '' ?>>Wähle passende Methode</option>
                                        <?= $options_selmethod ?>
                                    </select>  
                                </div>
                                <div id="field_appl_selstatus">
                                    <label for="floatingInputGrid" class="bg-success input_label">Status</label>
                                    <select class="form-control dropdown-toggle w-100" name="updateapp_status">
                                        <option value="" disabled selected>Wähle passenden Status</option>
                                        <?= $options_selSTAT ?>
                                    </select>  
                                </div>
                                <div id="field_appl_selpriority">
                                    <label for="floatingInputGrid" class="bg-success input_label">Priorität</label>
                                    <select class="form-control dropdown-toggle w-100" name="updateapp_priority">
                                        <option value="" disabled selected>Wähle passende Priorität</option>
                                        <?= $options_selPRIO ?>
                                    </select>  
                                </div>
                                <div id="field_appl_expsalary">
                                    <label for="floatingInputGrid" class="bg-success input_label">zu erwartendes Gehalt</label>
                                    <input type="text" class="form-control button_shadow w-100" name="updateapp_expsalary"  id="updateapp_expsalary" placeholder="<?php echo $oldsalary; ?>">
                                </div>
                                <div id="field_appl_joblink">
                                    <label for="floatingInputGrid" class="bg-success input_label">URL der Jobausschreibung</label>
                                    <input type="text" class="form-control button_shadow w-100" name="updateapp_joburl"  id="updateapp_joburl" value="<?php echo $old_joburl; ?>">
                                </div>
                                <div id="field_appl_description">
                                    <label for="floatingInputGrid" class="bg-success input_label">Jobbeschreibung</label>
                                    <textarea id="textfield_description"  class="w-100" name="textfield_description" rows="7" cols="130"><?php echo $old_jobbeschreibung; ?>
                                    </textarea>   
                                </div>
                                <div id="field_appl_requirements">
                                    <label for="floatingInputGrid" class="bg-success input_label">Voraussetzungen</label>
                                    <textarea id="textfield_requirements"  class="w-100" name="textfield_requirements" rows="7" cols="130"><?php echo $old_voraussetzungen; ?>
                                    </textarea>   
                                </div>
                                <div id="field_appl_notizen">
                                    <label for="floatingInputGrid" class="bg-success input_label">Notizen</label>
                                    <textarea id="textfield_notizen"  class="w-100" name="textfield_notizen" rows="7" cols="130"><?php echo $old_notizen; ?>
                                    </textarea>   
                                </div>
                                <div class="submitbutton">
                                    <input type="submit" class="form-control btn btn-primary mt-2 box_shadow w-100 text-white" name="btn_updateapplication" id="btn_updateapplication" value="Bewerbung aktualisieren">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
        </div>
   </div>

<script src="../components/scripts/update_check.js"></script>
<script src="components/scripts/tagging_color.js"></script>
<script src="components/scripts/category_color.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>
</html>