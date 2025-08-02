<?php 
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);

    require "../components/database/db_connect.php";
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // require_once ('components/inc/check_remote_licence.php');

    $config = require __DIR__ . '/../components/config/timezone.php';

    if (!empty($config['timezone'])) {
        date_default_timezone_set($config['timezone']);
    }

    include "../components/loadplugins.php";
    include "inc/berechnungen.php";

    $current_page = getCurrentPage();

    $action = $_GET['action'];
    $id = $_GET['id'];
    $company = $_GET['company'] ?? '';

    $styleCRAPP = ($action !== 'addapplication') ? 'style="display:none;"' : '';
    $styleCRANW = ($action !== 'addresponse') ? 'style="display:none;"' : '';

    /* ---- Werte aus der enum bewerbungsmethode aus der Tabelle bewerbungen holen ---- */
        $sql_enum = "SHOW COLUMNS FROM `bewerbungen` LIKE 'bewerbungsmethode'";
        $resEnum = mysqli_query($connect, $sql_enum);
        $rowEnum = mysqli_fetch_assoc($resEnum);

        // Feld "Type" enthält den ENUM-String z. B. "enum('E-Mail','Online','Post')"
        $type = $rowEnum['Type'];

        preg_match("/^enum\((.*)\)$/", $type, $matches);
        $enumStr = $matches[1]; // Inhalt zwischen den Klammern

        $enumValues = array_map(function($val) {
            return trim($val, " '");
        }, explode(",", $enumStr));

        $options_selmethod = "";
        foreach($enumValues as $val){
            $options_selmethod .= "<option value='$val'>$val</option>";
        }
    /* ---- Werte aus der enum bewerbungsmethode aus der Tabelle bewerbungen holen ---- */

    /* ---- Werte aus der enum status der Bewerbung aus der Tabelle bewerbungen holen ---- */
        $sql_enumSTAT = "SHOW COLUMNS FROM `bewerbungen` LIKE 'status'";
        $resEnumSTAT = mysqli_query($connect, $sql_enumSTAT);
        $rowEnumSTAT = mysqli_fetch_assoc($resEnumSTAT);

        // Feld "Type" enthält den ENUM-String z. B. "enum('E-Mail','Online','Post')"
        $type = $rowEnumSTAT['Type'];

        preg_match("/^enum\((.*)\)$/", $type, $matchesSTAT);
        $enumStrSTAT = $matchesSTAT[1]; // Inhalt zwischen den Klammern

        $enumValuesSTAT = array_map(function($valSTAT) {
            return trim($valSTAT, " '");
        }, explode(",", $enumStrSTAT));

        $options_selSTAT = "";
        foreach($enumValuesSTAT as $valSTAT){
            $options_selSTAT .= "<option value='$valSTAT'>$valSTAT</option>";
        }
    /* ---- Werte aus der enum bewerbungsmethode aus der Tabelle bewerbungen holen ---- */

   /* ---- Werte aus der enum Priorität der Bewerbung aus der Tabelle bewerbungen holen ---- */
        $sql_enumPRIO = "SHOW COLUMNS FROM `bewerbungen` LIKE 'priority'";
        $resEnumPRIO = mysqli_query($connect, $sql_enumPRIO);
        $rowEnumPRIO = mysqli_fetch_assoc($resEnumPRIO);

        // Feld "Type" enthält den ENUM-String z. B. "enum('E-Mail','Online','Post')"
        $type = $rowEnumPRIO['Type'];

        preg_match("/^enum\((.*)\)$/", $type, $matchesPRIO);
        $enumStrPRIO = $matchesPRIO[1]; // Inhalt zwischen den Klammern

        $enumValuesPRIO = array_map(function($valPRIO) {
            return trim($valPRIO, " '");
        }, explode(",", $enumStrPRIO));

        $options_selPRIO = "";
        foreach($enumValuesPRIO as $valPRIO){
            $options_selPRIO .= "<option value='$valPRIO'>$valPRIO</option>";
        }
    /* ---- Werte aus der enum bewerbungsmethode aus der Tabelle bewerbungen holen ---- */

    /* ---- Create New Bewerbung ---- */
    /* ============================== */

    // Funktion wurde in unten genannte Datei ausgelagert //
    include('inc/func_createnew_bewerbung.php');
   
    /* ---- Create New Bewerbung ---- */


    /* ---- select Bewerbung/Firma ---- */
    $options_selappfirma = "";

    if (!empty($id) && !empty($company)) {
        // Nur die spezifische Bewerbung mit $id anzeigen
        $stmt = $connect->prepare("SELECT * FROM `bewerbungen` WHERE id = ?");
        $stmt->bind_param("i", $id);
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

    /* ---- Werte aus der enum Antworttyp der Antworten aus der Tabelle bewerbungen holen ---- */
        $sql_enumAT = "SHOW COLUMNS FROM `firmen_antworten` LIKE 'antwort_typ'";
        $resEnumAT = mysqli_query($connect, $sql_enumAT);
        $rowEnumAT = mysqli_fetch_assoc($resEnumAT);

        // Feld "Type" enthält den ENUM-String z. B. "enum('E-Mail','Online','Post')"
        $type = $rowEnumAT['Type'];

        preg_match("/^enum\((.*)\)$/", $type, $matchesAT);
        $enumStrAT = $matchesAT[1]; // Inhalt zwischen den Klammern

        $enumValuesAT = array_map(function($valAT) {
            return trim($valAT, " '");
        }, explode(",", $enumStrAT));

        $options_selAT = "";
        foreach($enumValuesAT as $valAT){
            $options_selAT .= "<option value='$valAT'>$valAT</option>";
        }
    /* ---- Werte aus der enum Antworttyp der Antworten aus der Tabelle bewerbungen holen ---- */

    /* ---- Werte aus der enum Outcome der Antworten aus der Tabelle firmen_antworten holen ---- */
        $sql_enumOUT = "SHOW COLUMNS FROM `firmen_antworten` LIKE 'antwort_ergebnis'";
        $resEnumOUT = mysqli_query($connect, $sql_enumOUT);
        $rowEnumOUT = mysqli_fetch_assoc($resEnumOUT);

        // Feld "Type" enthält den ENUM-String z. B. "enum('E-Mail','Online','Post')"
        $type = $rowEnumOUT['Type'];

        preg_match("/^enum\((.*)\)$/", $type, $matchesOUT);
        $enumStrOUT = $matchesOUT[1]; // Inhalt zwischen den Klammern

        $enumValuesOUT = array_map(function($valOUT) {
            return trim($valOUT, " '");
        }, explode(",", $enumStrOUT));

        $options_selOUT = "";
        foreach($enumValuesOUT as $valOUT){
            $options_selOUT .= "<option value='$valOUT'>$valOUT</option>";
        }
    /* ---- Werte aus der enum Outcome der Antworten aus der Tabelle firmen_antworten holen ---- */

    /* ---- Create NEW Antwort ---- */
    /* ============================ */

    // Funktion wurde in unten genannte Datei ausgelagert...
    include('inc/func_createnew_answer.php');


    /* ---- Create NEW Antwort ---- */

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
    <link rel="stylesheet" href="../components/css/bwd_admin_create.css">
    
    <title>Meine Bewerbungsdatenbank</title>
</head>
<body class="screen">
    <?php include ('inc/mainmenu.php'); ?>
    <div id="create_area">
        <div id="createapplication_area" <?= $styleCRAPP ?>>
            <div id="createapp_box">
                <div class="createapp_titel">
                    <h2 class="section_titel">neue Bewerbung anlegen</h2>
                </div>
                <div id="createbox_content" >
                    <div id="creationfomular_box" class="box_shadow">
                        <form class="w-100 pb-2" method="POST" action="<?= htmlspecialchars($_SERVER['SCRIPT_NAME']) . '?'.'action=addapplication' ?>" enctype="multipart/form-data">    
                            <div id="createapp_inputformular">
                                <div id="field_appl_company">
                                    <label for="floatingInputGrid" class="bg-success input_label">Firma *</label>
                                    <input type="text" class="form-control button_shadow w-100" name="newapplication_company"  id="newapplication_company" placeholder="Firma" required>
                                </div>
                                <div id="field_appl_position">
                                    <label for="floatingInputGrid" class="bg-success input_label">Position *</label>
                                    <input type="text" class="form-control button_shadow w-100" name="newapplication_position"  id="newapplication_position" placeholder="Position" required>
                                </div>
                                <div id="field_appl_appdate">
                                    <label for="floatingInputGrid" class="bg-success input_label">Bewerbungsdatum *</label>
                                    <input type="date" class="form-control button_shadow w-100" name="newapplication_date"  id="newapplication_date" placeholder="Datum" required>
                                </div>
                                <div id="field_appl_apptime">
                                    <label for="floatingInputGrid" class="bg-success input_label">Uhrzeit der Bewerbung</label>
                                    <input type="time" class="form-control button_shadow w-100" name="newapplication_time"  id="newapplication_time" placeholder="Uhrzeit">
                                </div>
                                <div id="field_appl_selmethod">
                                    <label for="newapplication_method" class="bg-success input_label">Bewerbungsmethode</label>
                                    <select id="newapplication_method" class="form-control w-100" name="newapplication_method" required>
                                        <option value="" disabled <?= empty($current_value) ? 'selected' : '' ?>>Wähle passende Methode</option>
                                        <?= $options_selmethod ?>
                                    </select>  
                                </div>
                                <div id="field_appl_selstatus">
                                    <label for="floatingInputGrid" class="bg-success input_label">Status</label>
                                    <select class="form-control dropdown-toggle w-100" name="newapplication_status">
                                        <option value="" disabled selected>Wähle passenden Status</option>
                                        <?= $options_selSTAT ?>
                                    </select>  
                                </div>
                                <div id="field_appl_selpriority">
                                    <label for="floatingInputGrid" class="bg-success input_label">Priorität</label>
                                    <select class="form-control dropdown-toggle w-100" name="newapplication_priority">
                                        <option value="" disabled selected>Wähle passende Priorität</option>
                                        <?= $options_selPRIO ?>
                                    </select>  
                                </div>
                                <div id="field_appl_expsalary">
                                    <label for="floatingInputGrid" class="bg-success input_label">zu erwartendes Gehalt</label>
                                    <input type="text" class="form-control button_shadow w-100" name="newapplication_expsalary"  id="newapplication_expsalary" placeholder="Gehalt laut Jobausschreibung">
                                </div>
                                <div id="field_appl_joblink">
                                    <label for="floatingInputGrid" class="bg-success input_label">URL der Jobausschreibung</label>
                                    <input type="text" class="form-control button_shadow w-100" name="newapplication_joburl"  id="newapplication_joburl" placeholder="URL der Ausschreibung">
                                </div>
                                <div id="field_appl_description">
                                    <label for="floatingInputGrid" class="bg-success input_label">Jobbeschreibung</label>
                                    <textarea id="textfield_description"  class="w-100" name="textfield_description" rows="7" cols="130">
                                    </textarea>   
                                </div>
                                <div id="field_appl_requirements">
                                    <label for="floatingInputGrid" class="bg-success input_label">Voraussetzungen</label>
                                    <textarea id="textfield_requirements"  class="w-100" name="textfield_requirements" rows="7" cols="130">
                                    </textarea>   
                                </div>
                                <div id="field_appl_notizen">
                                    <label for="floatingInputGrid" class="bg-success input_label">Notizen</label>
                                    <textarea id="textfield_notizen"  class="w-100" name="textfield_notizen" rows="7" cols="130">
                                    </textarea>   
                                </div>
                                <div class="submitbutton">
                                    <input type="submit" class="form-control btn btn-primary mt-2 box_shadow w-100 text-white" name="btn_createapplication" id="btn_createapplication" value="Bewerbung erstellen">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="createresponse_area" <?= $styleCRANW ?>>
            <div id="createresponse_box">
                <div class="createresponse_titel">
                    <h2 class="section_titel">neue Antwort anlegen</h2>
                </div>
                <div id="createresponsebox_content" >
                    <div id="creationrespfomular_box" class="box_shadow">
                        <form class="w-100 pb-2" method="POST" action="<?= htmlspecialchars($_SERVER['SCRIPT_NAME']) . '?'.'action=addresponse' ?>" enctype="multipart/form-data"> 
                            <div id="createresponse_inputformular">
                                <div id="field_response_selapp">
                                    <label for="newresponse_application" class="bg-success input_label">Bewerbung *</label>
                                    <select id="newresponse_application" class="form-control w-100" name="newresponse_app" required>
                                        <option value="" disabled <?= empty($current_value) ? 'selected' : '' ?>>Wähle passende Bewerbung</option>
                                        <?= $options_selappfirma ?>
                                    </select>  
                                </div>
                                <div id="field_response_selresponsetype">
                                    <label for="newresponse_resptype" class="bg-success input_label">Antwort *</label>
                                    <select id="newresponse_resptype" class="form-control w-100" name="newresponse_type" required>
                                        <option value="" disabled <?= empty($current_value) ? 'selected' : '' ?>>Wähle passenden Antwort-Typ</option>
                                        <?= $options_selAT ?>
                                    </select>  
                                </div>
                                <div id="field_response_seloutcome">
                                    <label for="newresponse_respoutcome" class="bg-success input_label">Ergebnis *</label>
                                    <select id="newresponse_respoutcome" class="form-control w-100" name="newresponse_respoutcome" required>
                                        <option value="" disabled <?= empty($current_value) ? 'selected' : '' ?>>Wähle passendes Antwort-Ergebnis</option>
                                        <?= $options_selOUT ?>
                                    </select>  
                                </div>
                                <div id="field_response_appdate">
                                    <label for="floatingInputGrid" class="bg-success input_label">Datum der Antwort *</label>
                                    <input type="date" class="form-control button_shadow w-100" name="newresponse_date"  id="newresponse_date" placeholder="Datum" required>
                                </div>
                                <div id="field_response_apptime">
                                    <label for="floatingInputGrid" class="bg-success input_label">Uhrzeit der Antwort</label>
                                    <input type="time" class="form-control button_shadow w-100" name="newresponse_time"  id="newresponse_time" placeholder="Uhrzeit">
                                </div>
                                <div id="field_response_content">
                                    <label for="floatingInputGrid" class="bg-success input_label">Inhalt der Antwort</label>
                                    <textarea id="textfield_responsecontent"  class="w-100" name="textfield_responsecontent" rows="7" cols="130">
                                    </textarea>   
                                </div>
                                <div id="field_response_nextsteps">
                                    <label for="floatingInputGrid" class="bg-success input_label">Nächste Schritte</label>
                                    <textarea id="textfield_responsenextsteps"  class="w-100" name="textfield_responsenextsteps" rows="7" cols="130">
                                    </textarea>   
                                </div>
                                <div id="field_response_contactperson">
                                    <label for="floatingInputGrid" class="bg-success input_label">Kontaktperson</label>
                                    <input type="text" class="form-control button_shadow w-100" name="newresponse_contactperson"  id="newresponse_contactperson" placeholder="Name des Kontakts">
                                </div>
                                <div id="field_response_contactemail">
                                    <label for="floatingInputGrid" class="bg-success input_label">Kontakt-email</label>
                                    <input type="email" class="form-control button_shadow w-100" name="newresponse_contactemail"  id="newresponse_contactemail" placeholder="email des Kontakts">
                                </div>
                                <div id="field_response_contactphone">
                                    <label for="floatingInputGrid" class="bg-success input_label">Kontakt-Telefon</label>
                                    <input type="text" class="form-control button_shadow w-100" name="newresponse_contactphone"  id="newresponse_contactphone" placeholder="Telefonnummer des Kontakts">
                                </div>
                                <div class="field_response_followupcheck">
                                    <input class="form-check-input" type="checkbox" id="followup_check" name="followup_check" value="1">
                                    <label class="form-check-label" for="followup_check">
                                        Follow-Up benötigt
                                    </label>
                                </div>
                                <div id="field_response_followupdate">
                                    <label for="floatingInputGrid" class="bg-success input_label">Datum FollowUp </label>
                                    <input type="date" class="form-control button_shadow w-100" name="newresponse_followupdate"  id="newresponse_followupdate" placeholder="Datum" >
                                </div>
                                <div class="submitbutton">
                                    <input type="submit" class="form-control btn btn-primary mt-2 box_shadow w-100 text-white" name="btn_createresponse" id="btn_createresponse" value="Antwort erstellen">
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