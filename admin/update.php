<?php 
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

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
// var_dump($_GET);

    $current_page = getCurrentPage();

    $action = $_GET['action'];
    $app_id = $_GET['id'];

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

            $new_bewerbungsdatum = $new_appdate.' '.$new_apptime;

            $new_appmethod = isset($_POST['updateapp_method']) && $_POST['updateapp_method'] !== '' ? $_POST['updateapp_method'] : $old_appmethod;
            $new_appstatus = isset($_POST['updateapp_status']) && $_POST['updateapp_status'] !== '' ? $_POST['updateapp_status'] : $old_appstatus;
            $new_apppriority = isset($_POST['updateapp_priority']) && $_POST['updateapp_priority'] !== '' ? $_POST['updateapp_priority'] : $old_priority;
            $new_appexpsalary = isset($_POST['updateapp_expsalary']) && $_POST['updateapp_expsalary'] !== '' ? $_POST['updateapp_expsalary'] : $oldsalaryINT;
            $new_appjoburl = isset($_POST['updateapp_joburl']) && $_POST['updateapp_joburl'] !== '' ? $_POST['updateapp_joburl'] : $old_joburl;
            $new_jobbeschreibung = isset($_POST['textfield_description']) && $_POST['textfield_description'] !== '' ? $_POST['textfield_description'] : $old_jobbeschreibung;
            $new_voraussetzungen = isset($_POST['textfield_requirements']) && $_POST['textfield_requirements'] !== '' ? $_POST['textfield_requirements'] : $old_voraussetzungen;
            $new_notizen = isset($_POST['textfield_notizen']) && $_POST['textfield_notizen'] !== '' ? $_POST['textfield_notizen'] : $old_notizen;

            $date_update = date("Y-m-d H:i:s");

            $date_newupdate = formatCreateDate($date_update);
            
            /* ---- Statusänderung der Bewerbung ---- */
            // ist der Status der Bewerbung anders als der alte, wird dies auch in den Notizen erfasst

            if ($new_appstatus != $old_appstatus) {
                $newAppStatus_text = $date_newupdate . ": Der Status der Bewerbung wurde von $old_appstatus auf $new_appstatus aktualisiert";

                // Wenn bisher kein Text vorhanden ist oder Platzhalter "nicht angegeben"
                if (trim($new_notizen) === "nicht angegeben" || trim($new_notizen) === "") {
                    $new_notizen = $newAppStatus_text;
                } else {
                    // Bestehenden Text beibehalten, neue Zeile anhängen
                    $new_notizen .= "\n" . $newAppStatus_text;
                }
            }

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

    }

    if($action == "updateresponse"){
        // $resp_id = $_GET['id'];
        $sql_details_response = "select * from firmen_antworten where id = $app_id";

        // var_dump($app_id);
        // die();
        $resDR = mysqli_query($connect,$sql_details_response);
        $rowDR = mysqli_fetch_assoc($resDR);
        // $antwort_titel = $rowDR['']
        $fk_bewerbungs_id = $rowDR['fk_bewerbungs_id'];
        $id = $_GET['id'];
        $old_antwort_datum = $rowDR['antwort_datum'];
        $old_antwort_typ = $rowDR['antwort_typ'];
        $currenttyp_value = $old_antwort_typ;
        $old_antwort_ergebnis = $rowDR['antwort_ergebnis'];
        $currentergebnis_value = $old_antwort_ergebnis;
        $currentergebnis_value = "Positiv (Ja/Interessiert)"; // Testweise
        $old_antwort_inhalt = $rowDR['antwort_inhalt'];
        $old_next_steps = $rowDR['next_steps'];
        $old_kontaktperson = $rowDR['kontaktperson'];
        $old_kontakt_email = $rowDR['kontakt_email'];
        $old_kontakt_telefon = $rowDR['kontakt_telefon'];
        $old_followup_required = $rowDR['followup_required'];
        $old_followup_date = $rowDR['followup_date'];
        $old_antwort_date = formatDateForDatabase($old_antwort_datum);
        $old_antwort_time = formatTimeForDatabase($old_antwort_datum);
        $old_followup_date = formatDateForDatabase($old_antwort_datum);
        $newUpdate_date = date("Y-m-d H:i:s");
                // $current_value = $old_appmethod;

        include ('inc/func_selanttyp.php');
        include ('inc/func_selantergebnis.php');
        include ('inc/func_selantwbewerbung.php');
        $sql_detBewerbung = "select * from bewerbungen where id = $fk_bewerbungs_id";
        $resDBE = mysqli_query($connect,$sql_detBewerbung);
        $rowDBE = mysqli_fetch_assoc($resDBE);
        $applic_id = $rowDBE['id'];
        $antwort_firma = $rowDBE['firma'];
        $antwort_position = $rowDBE['position'];
        // var_dump($firma);
        // die();
        $antwortFullPosition = formatFullAntwortFirma($antwort_firma,$antwort_position);

         /* ---- select Bewerbung/Firma ---- */
            $options_selappfirma = "";

            if (!empty($id)) {
                // Nur die spezifische Bewerbung mit $id anzeigen
                $stmt = $connect->prepare("SELECT * FROM `bewerbungen` WHERE id = ?");
                $stmt->bind_param("i", $fk_bewerbungs_id);
            } elseif (!empty($company)) {
                // Alle Bewerbungen dieser Firma laden
                $stmt = $connect->prepare("SELECT * FROM `bewerbungen` WHERE firma = ? ORDER BY created_at DESC");
                $stmt->bind_param("s", $company);
            } else {
                // Alle Bewerbungen, außer abgelehnte
                $stmt = $connect->prepare("SELECT * FROM `bewerbungen` WHERE status NOT LIKE 'Abgelehnt' ORDER BY created_at DESC");
            }

            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $selected = ($row["id"] == $id) ? "selected" : "";
                $options_selappfirma .= "<option value='{$row["id"]}' $selected>{$row["firma"]} – {$row["position"]}</option>\n";
            }

            $stmt->close();
            /* ---- select Bewerbung/Firma ---- */

            if (isset($_POST["btn_updateresponse"])) {

                /* ---- Eingabewerte sichern ---- */
                $update_bewerbung = isset($_POST['updateresponse_app']) && $_POST['updateresponse_app'] !== '' ? $_POST['updateresponse_app'] : $fk_bewerbungs_id;
                $update_resptype = isset($_POST['updateresponse_type']) && $_POST['updateresponse_type'] !== '' ? $_POST['updateresponse_type'] : $old_antwort_typ;
                $update_respergebnis = isset($_POST['updateresponse_respoutcome']) && $_POST['updateresponse_respoutcome'] !== '' ? $_POST['updateresponse_respoutcome'] : $old_antwort_ergebnis;
                $update_respdate = isset($_POST['updateresponse_date']) && $_POST['updateresponse_date'] !== '' ? $_POST['updateresponse_date'] : $old_antwort_date;
                $update_resptime = isset($_POST['updateresponse_time']) && $_POST['updateresponse_time'] !== '' ? $_POST['updateresponse_time'] : $old_antwort_time;
                $update_respcontent = isset($_POST['textfield_upd_responsecontent']) && $_POST['textfield_upd_responsecontent'] !== '' ? $_POST['textfield_upd_responsecontent'] : $old_antwort_inhalt;
                $update_respnextsteps = isset($_POST['textfield_upd_responsenextsteps']) && $_POST['textfield_upd_responsenextsteps'] !== '' ? $_POST['textfield_upd_responsenextsteps'] : $old_next_steps;
                $update_respcontactperson = isset($_POST['upd_response_contactperson']) && $_POST['upd_response_contactperson'] !== '' ? $_POST['upd_response_contactperson'] : $old_kontaktperson;
                $update_respcontactemail = isset($_POST['upd_response_contactemail']) && $_POST['upd_response_contactemail'] !== '' ? $_POST['upd_response_contactemail'] : $old_kontakt_email;
                $update_respcontactphone = isset($_POST['upd_response_contactphone']) && $_POST['upd_response_contactphone'] !== '' ? $_POST['upd_response_contactphone'] : $old_kontakt_telefon;
                $update_respfollowup_date = isset($_POST['upd_response_followupdate']) && $_POST['upd_response_followupdate'] !== '' ? $_POST['upd_response_followupdate'] : $old_followup_date;


                $newupdate_datetime = $update_respdate. ' '. $update_resptime;

                $stmtResponse = $connect->prepare("UPDATE firmen_antworten SET 
                fk_bewerbungs_id = ?,
                antwort_datum = ?,
                antwort_typ = ?,
                antwort_ergebnis = ?,
                antwort_inhalt = ?,
                next_steps = ?,
                kontaktperson = ?,
                kontakt_email = ?,
                kontakt_telefon = ?,
                followup_date = ?,
                updated_at = ?
                WHERE id = ?");

                    $stmtResponse->bind_param(
                        "issssssssssi",
                        $update_bewerbung,
                        $newupdate_datetime,
                        $update_resptype,
                        $update_respergebnis,
                        $update_respcontent,
                        $update_respnextsteps,
                        $update_respcontactperson,
                        $update_respcontactemail,
                        $update_respcontactphone,
                        $update_respfollowup_date,
                        $newUpdate_date,
                        $app_id
                    );

                    if (!$stmtResponse->execute()) {
                        die("Fehler bei der Aktualisierung: " . $stmtResponse->error);
                    }

                    $stmtResponse->close();

                    setFlashMessage('success', 'Bewerbung wurde erfolgreich aktualisiert');
                    header("Location: bewerbungen.php");
                    exit;
                }

                // var_dump($newupdate_datetime);
                // die();
            }
        


    
    
    $styleUPAP = ($action !== 'updateapplication') ? 'style="display:none;"' : '';
    $styleUPRE = ($action !== 'updateresponse') ? 'style="display:none;"' : '';
    // $styleUPRES = ($action !== 'addresponse') ? 'style="display:none;"' : '';

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
        <div id="updatearea_response" <?= $styleUPRE ?>>
            <div id="updateresponse_box">
                <div class="updatebox_title">
                    <h2 class="section_titel">Antwort aktualisieren <small class='text-muted'><?php echo $antwortFullPosition; ?></small></h2>
                </div>
                <div id="updateresponse_content">
                    <div id="updaterespformular_box">
                        <form class="w-100 pb-2" method="POST" action="<?= htmlspecialchars($_SERVER['SCRIPT_NAME']) . '?id='.$app_id.'&'.'action=updateresponse' ?>" enctype="multipart/form-data"> 
                            <div id="updateresp_inputformular">
                               <div id="field_response_selapp">
                                    <label for="updateresponse_app" class="bg-success input_label">Bewerbung *</label>
                                    <select id="updateresponse_app" class="form-control w-100" name="updateresponse_app" required>
                                        <option value="" disabled <?= empty($current_value) ? 'selected' : '' ?>>Wähle passende Bewerbung</option>
                                        <?= $options_selappfirma ?>
                                    </select>  
                                </div>
                                <div id="field_response_selresponsetype">
                                    <label for="updateresponse_type" class="bg-success input_label">Antwort *</label>
                                    <select id="updateresponse_type" class="form-control w-100" name="updateresponse_type" required>
                                        <option value="" disabled <?= empty($currenttyp_value) ? 'selected' : '' ?>>Wähle passenden Antwort-Typ</option>
                                        <?= $options_selAntTyp ?>
                                    </select>  
                                </div>
                                <div id="field_response_seloutcome">
                                    <label for="updateresponse_respoutcome" class="bg-success input_label">Ergebnis *</label>
                                    <select id="updateresponse_respoutcome" class="form-control w-100" name="updateresponse_respoutcome" required>
                                        <option value="" disabled <?= empty($currentergebnis_value) ? 'selected' : '' ?>>Wähle passendes Antwort-Ergebnis</option>
                                        <?= $options_selAntErg ?>
                                    </select>  
                                </div>
                                <div id="field_response_appdate">
                                    <label for="floatingInputGrid" class="bg-success input_label">Datum der Antwort *</label>
                                    <input type="date" class="form-control button_shadow w-100" name="updateresponse_date"  id="updateresponse_date" value="<?php echo $old_antwort_date; ?>">
                                </div>
                                <div id="field_response_apptime">
                                    <label for="floatingInputGrid" class="bg-success input_label">Uhrzeit der Antwort</label>
                                    <input type="time" class="form-control button_shadow w-100" name="updateresponse_time"  id="updateresponse_time" value="<?php echo $old_antwort_time; ?>">
                                </div>
                                <div id="field_response_content">
                                    <label for="floatingInputGrid" class="bg-success input_label">Inhalt der Antwort</label>
                                    <textarea id="textfield_upd_responsecontent"  class="w-100" name="textfield_upd_responsecontent" rows="7" cols="130"><?php echo $old_antwort_inhalt; ?>
                                    </textarea>   
                                </div>
                                <div id="field_response_nextsteps">
                                    <label for="floatingInputGrid" class="bg-success input_label">Nächste Schritte</label>
                                    <textarea id="textfield_upd_responsenextsteps"  class="w-100" name="textfield_upd_responsenextsteps" rows="7" cols="130"><?php echo $old_next_steps; ?>
                                    </textarea>   
                                </div>
                                <div id="field_response_contactperson">
                                    <label for="floatingInputGrid" class="bg-success input_label">Kontaktperson</label>
                                    <input type="text" class="form-control button_shadow w-100" name="upd_response_contactperson"  id="upd_response_contactperson" value="<?php echo $old_kontaktperson; ?>">
                                </div>
                                <div id="field_response_contactemail">
                                    <label for="floatingInputGrid" class="bg-success input_label">Kontakt-email</label>
                                    <input type="email" class="form-control button_shadow w-100" name="upd_response_contactemail"  id="upd_response_contactemail" value="<?php echo $old_kontakt_email; ?>">
                                </div>
                                <div id="field_response_contactphone">
                                    <label for="floatingInputGrid" class="bg-success input_label">Kontakt-Telefon</label>
                                    <input type="text" class="form-control button_shadow w-100" name="upd_response_contactphone"  id="upd_response_contactphone" value="<?php echo $old_kontakt_telefon; ?>">
                                </div>
                                <div class="field_response_followupcheck">
                                    <input class="form-check-input" type="checkbox" id="followup_check" name="followup_check" value="1">
                                    <label class="form-check-label" for="followup_check">
                                        Follow-Up benötigt
                                    </label>
                                </div>
                                <div id="field_response_followupdate">
                                    <label for="floatingInputGrid" class="bg-success input_label">Datum FollowUp </label>
                                    <input type="date" class="form-control button_shadow w-100" name="upd_response_followupdate"  id="upd_response_followupdate" value="<?php echo $old_followup_date; ?>" >
                                </div>
                                <div class="submitbutton">
                                    <input type="submit" class="form-control btn btn-primary mt-2 box_shadow w-100 text-white" name="btn_updateresponse" id="btn_updateresponse" value="Antwort aktualisieren">
                                </div>
                            </div>
                        </form>
                    </div>
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