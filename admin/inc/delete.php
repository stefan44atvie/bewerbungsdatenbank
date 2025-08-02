<?php
     ini_set('display_errors', 1);
     ini_set('display_startup_errors', 1);
     error_reporting(E_ALL);
    require "../../components/database/db_connect.php";
    // include "../components/inc/sessions.php";
    include "../../components/loadplugins.php";

    // Allgemeine Funktion zum Löschen eines Eintrags aus der Datenbank
    function deleteItem($conn, $table, $id, $redirect) {
        // Direkt löschen ohne Abhängigkeitsprüfung
        $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
        if (!$stmt) {
            die("Fehler beim Erstellen des Statements: " . $conn->error);
        }

        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            setFlashMessage(type: 'error', message: 'Fehler beim Löschen des Eintrags...');
            die("Fehler beim Löschen des Eintrags: " . $stmt->error);
        }
        $stmt->close();

        // Weiterleitung nach erfolgreichem Löschen
        header("Location: $redirect");
        exit;
    }

    /* ---- Löschen einer Bewerbung ---- */
    if (isset($_GET["deleteapplication"])) {
    $id = intval($_GET["id"]); // ID sicherstellen, dass sie nur eine Zahl ist
    setFlashMessage(type: 'success', message: 'Bewerbung wurde erfolgreich gelöscht...');
    deleteItem($connect, "bewerbungen", $id, "../bewerbungen.php");
}
    /* ---- Löschen einer Bewerbung ---- */

    /* ---- Löschen einer Bewerbung ---- */
    if (isset($_GET["deleteresponse"])) {
    $id = intval($_GET["id"]); // ID sicherstellen, dass sie nur eine Zahl ist
    setFlashMessage(type: 'success', message: 'Antwort wurde erfolgreich gelöscht...');
    deleteItem($connect, "firmen_antworten", $id, "../antworten.php");
}
    /* ---- Löschen einer Bewerbung ---- */
?>