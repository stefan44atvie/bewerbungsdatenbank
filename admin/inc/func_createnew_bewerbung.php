<?php 

/* ---- Create NEW Bewerbung ---- */
    if (isset($_POST["btn_createapplication"])) {
        $newapplication_company = cleanInput($_POST["newapplication_company"]);
        $newapplication_position = cleanInput($_POST["newapplication_position"]);
        $newapplication_date = cleanInput($_POST["newapplication_date"]);
        $newapplication_time = cleanInput($_POST["newapplication_time"]);
        $newapplication_method = cleanInput($_POST["newapplication_method"]);
        $newapplication_status = cleanInput($_POST["newapplication_status"]);
        $newapplication_priority = cleanInput($_POST["newapplication_priority"]);
        $newapplication_expsalary = cleanInput($_POST["newapplication_expsalary"]);
        $newapplication_joburl = cleanInput($_POST["newapplication_joburl"]);
        $textfield_description = cleanInput($_POST["textfield_description"]);
        $textfield_requirements = cleanInput($_POST["textfield_requirements"]);
        $textfield_notizen = cleanInput($_POST["textfield_notizen"]);

        $created_at = date("Y-m-d H:i");

        $textfield_description = deleteLeadingAndTrailingWhitespace($textfield_description);
        $textfield_requirements = deleteLeadingAndTrailingWhitespace($textfield_requirements);
        $textfield_notizen = deleteLeadingAndTrailingWhitespace($textfield_notizen);

        if(empty($newapplication_time)){
            $newapplication_time = "19:30";
        }
        $applied_date = $newapplication_date . ' ' . $newapplication_time;
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
                    "INSERT INTO `bewerbungen`(`firma`, `position`, `bewerbungsdatum`, `jobbeschreibung`, `voraussetzungen`, `gehaltsangaben`, `bewerbungsmethode`, `job_url`, `status`, `priority`, `notizen`, `created_at`) 
                        VALUES (?,?,?,?,?,?,?,?,?,?,?,?)"
                );
        
                    if (!$stmt) {        
                        throw new Exception("Statement konnte nicht vorbereitet werden: " . $connect->error);
                    }

        function nullable($val) {
            return trim($val) === "" ? null : $val;
        }

        $newapplication_method = nullable($newapplication_method);
        $newapplication_status = nullable($newapplication_status);
        $newapplication_priority = nullable($newapplication_priority);
        $newapplication_expsalary = nullable($newapplication_expsalary);
        $newapplication_joburl = nullable($newapplication_joburl);

                    // Parameter binden
                    $stmt->bind_param(
                    "ssssssssssss",
                $newapplication_company,
                $newapplication_position,
                    $applied_date,
                    $textfield_description,
                    $textfield_requirements,
                    $newapplication_expsalary,
                    $newapplication_method,
                    $newapplication_joburl,
                    $newapplication_status,
                    $newapplication_priority,
                    $textfield_notizen,
                    $created_at
                                    // "ssisssssssiss",
                );

                    // Statement ausführen
                    if ($stmt->execute()) {
                        setFlashMessage(type: 'success', message: 'Artikel erfolgreich gespeichert: ');
                        error_log("Neue Bewerbung gespeichert: {$newapplication_company}, Methode: {$newapplication_method}");
                        header("Location: bewerbungen.php");
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
    /* ---- Create NEW Bewerbung ---- */

    ?>