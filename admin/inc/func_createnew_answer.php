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

        $followup_date = date('Y-m-d', strtotime('+7 days'));

        $created_at = date("Y-m-d H:i");

        $textfield_responsecontent = deleteLeadingAndTrailingWhitespace($textfield_responsecontent);
        $textfield_responsenextsteps = deleteLeadingAndTrailingWhitespace($textfield_responsenextsteps);

        if(empty($newresponse_time)){
            $newresponse_time = "19:30";
        }
        $response_datetime = $newresponse_date . ' ' . $newresponse_time;
            // var_dump ($textfield_notizen);
            // die();

        try {


            $error = false;
            if (!$error) {
                // Überprüfe die Datenbankverbindung
                if ($connect->connect_error) {
                    throw new Exception("Datenbankverbindung fehlgeschlagen: " . $connect->connect_error);
                }
                $stmt = $connect->prepare(                                          //$title_pic
                    "INSERT INTO `firmen_antworten`(`fk_bewerbungs_id`, `antwort_datum`, `antwort_typ`, `antwort_ergebnis`, `antwort_inhalt`, `next_steps`, `kontaktperson`, `kontakt_email`, `kontakt_telefon`, `followup_required`, `followup_date`, `created_at`) 
                        VALUES (?,?,?,?,?,?,?,?,?,?,?,?)"
                );
        
                    if (!$stmt) {        
                        throw new Exception("Statement konnte nicht vorbereitet werden: " . $connect->error);
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

                    // Parameter binden
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
                    // Statement ausführen
                    if ($stmt->execute()) {
                        setFlashMessage(type: 'success', message: 'Artikel erfolgreich gespeichert: ');
                        error_log("Neue Bewerbung gespeichert: {$newapplication_company}, Methode: {$newapplication_method}");
                        header("Location: dashboard.php");
                        exit();
                    } else {
                            throw new Exception("Fehler beim Ausführen des Statements: " . $stmt->error);
                    }
                }
            } catch (Exception $e) {
                $errType = "danger";
                setFlashMessage(type: 'error', message: 'Ein Fehler ist aufgetreten: '. $e->getMessage());
                $errMsg = "Ein Fehler ist aufgetreten: " . $e->getMessage();
                error_log("Datenbankfehler: " . $e->getMessage());
            }
    }
    /* ---- Create NEW Antwort ---- */

?>