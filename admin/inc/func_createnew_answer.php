<?php 

/* ---- Create NEW Antwort ---- */
if (isset($_POST["btn_createresponse"])) {
    $newresponse_application = cleanInput($_POST["newresponse_app"]);
    $newresponse_resptype = cleanInput($_POST["newresponse_type"]);
    $newresponse_respoutcome = cleanInput($_POST["newresponse_respoutcome"]);
    $newresponse_date = cleanInput($_POST["newresponse_date"]);
    $newresponse_time = cleanInput($_POST["newresponse_time"]);
    $textfield_responsecontent = cleanInput($_POST["textfield_responsecontent"]);
    $textfield_responsenextsteps = cleanInput($_POST["textfield_responsenextsteps"]);
    $newresponse_contactperson = cleanInput($_POST["newresponse_contactperson"]);
    $newresponse_contactemail = cleanInput($_POST["newresponse_contactemail"]);
    $newresponse_contactphone = cleanInput($_POST["newresponse_contactphone"]);
    $followup_check = isset($_POST["followup_check"]) ? 1 : 0;        
    $newresponse_followupdate = cleanInput($_POST["newresponse_followupdate"]);
    $application_firma = $newresponse_application; 
   
    $created_at = date("Y-m-d H:i");
    $created_at_update = formatCreateDate($created_at);

    $newresponse_date = formatCreateDateJD($newresponse_date);
    $newresponse_time = TimeAusgabe($newresponse_time);

    if($followup_check==1 && !$newresponse_followupdate){
        $followup_date = date('Y-m-d', strtotime('+7 days'));
    }else{
        $followup_date = $newresponse_followupdate;
    }

    // var_dump($newresponse_application);
    // die();
    /* ---- ID zur Bewerbung herausfiltern ---- */
    $sql_bewerbungsid = "SELECT * FROM bewerbungen WHERE id = $newresponse_application";
    $resBID = mysqli_query($connect, $sql_bewerbungsid);
    $rowBID = mysqli_fetch_assoc($resBID);
    $corresp_bewerbungsid = $rowBID['id'];
    $response_firma = $rowBID['firma'];
    // $corresp_bewerbungsid = $_GET['id'];
    $textfield_responsecontent = deleteLeadingAndTrailingWhitespace($textfield_responsecontent);
    $textfield_responsenextsteps = deleteLeadingAndTrailingWhitespace($textfield_responsenextsteps);

    // var_dump($newresponse_application);
    // die();
    if (empty($newresponse_time)) {
        $newresponse_time = "19:30";
    }
    $response_datetime = DateTime::createFromFormat('d.m.Y H:i', $newresponse_date . ' ' . $newresponse_time);
    $response_datetime = $response_datetime ? $response_datetime->format('Y-m-d H:i:s') : null;

    $response_datetimeNotizen = formatCreateDate($response_datetime);
    try {
        if ($connect->connect_error) {
            throw new Exception("Datenbankverbindung fehlgeschlagen: " . $connect->connect_error);
        }

        function nullable($val) {
            return trim($val) === "" ? null : $val;
        }

        $textfield_responsecontent = nullable($textfield_responsecontent);
        $textfield_responsenextsteps = nullable($textfield_responsenextsteps);
        $newresponse_contactphone = nullable($newresponse_contactphone); 
        $newresponse_contactperson = nullable($newresponse_contactperson);
        $newresponse_followupdate = nullable($newresponse_followupdate);
        $newresponse_contactemail = nullable($newresponse_contactemail);

        $oldNotizen = $rowBID['notizen'] ?? ''; // aus vorherigem Query
        $newresponse_AppNotizen = $oldNotizen . "\n" . $response_datetimeNotizen . ": " . $response_firma . " hat auf deine Bewerbung geantwortet";

        // --- Statement für Notizen vorbereiten ---
        $stmtNotizen = $connect->prepare("UPDATE `bewerbungen` SET notizen = ? WHERE id = ?");
        if (!$stmtNotizen) {        
            throw new Exception("Statement konnte nicht vorbereitet werden: " . $connect->error);
        }
        $stmtNotizen->bind_param("si", $newresponse_AppNotizen, $newresponse_application);

        // --- Statement für Antwort vorbereiten ---
        if ($followup_check == 0) {
            $stmt = $connect->prepare(
                "INSERT INTO `firmen_antworten` 
                (`fk_bewerbungs_id`, `antwort_datum`, `antwort_typ`, `antwort_ergebnis`, `antwort_inhalt`, `next_steps`, `kontaktperson`, `kontakt_email`, `kontakt_telefon`, `followup_required`, `created_at`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "issssssssis",
                $newresponse_application,
                $response_datetime,
                $newresponse_resptype,
                $newresponse_respoutcome,
                $textfield_responsecontent,
                $textfield_responsenextsteps,
                $newresponse_contactperson,
                $newresponse_contactemail,
                $newresponse_contactphone,
                $followup_check,
                $created_at
            );
        } else {
            $stmt = $connect->prepare(
                "INSERT INTO `firmen_antworten` 
                (`fk_bewerbungs_id`, `antwort_datum`, `antwort_typ`, `antwort_ergebnis`, `antwort_inhalt`, `next_steps`, `kontaktperson`, `kontakt_email`, `kontakt_telefon`, `followup_required`, `followup_date`, `created_at`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "issssssssiss",
                $newresponse_application,
                $response_datetime,
                $newresponse_resptype,
                $newresponse_respoutcome,
                $textfield_responsecontent,
                $textfield_responsenextsteps,
                $newresponse_contactperson,
                $newresponse_contactemail,
                $newresponse_contactphone,
                $followup_check,
                $followup_date,
                $created_at
            );
        }

        // --- Ausführen und prüfen ---
        if ($stmt->execute()) {
            if ($stmtNotizen->execute()) {
                
                setFlashMessage(type: 'success', message: 'Antwort + Notizen erfolgreich gespeichert.');
                header("Location: dashboard.php");
                exit();
            } else {
    echo "<pre>Statement wurde nicht ausgeführt: " . $stmtNotizen->error . "</pre>";
}
        } else {
    echo "<pre>Statement wurde nicht ausgeführt: " . $stmtNotizen->error . "</pre>";
}

    } catch (Exception $e) {
        echo "<pre>Fehler: " . $e->getMessage() . "</pre>";
        error_log("Datenbankfehler: " . $e->getMessage());
    }
}
/* ---- Create NEW Antwort ---- */

?>