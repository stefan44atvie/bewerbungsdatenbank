<?php 

// session.php - Zentrale Datei für alle Sessions

date_default_timezone_set('Europe/Vienna');

// SICHERSTELLEN, dass KEINE Ausgabe erfolgt!
if (session_status() === PHP_SESSION_ACTIVE) {
    session_write_close(); // Falls eine andere Session aktiv ist, beenden
}
// Session-Name setzen, um mehrere Projekte zu trennen
session_name("Bewerbungsdatenbank");

// // Session-Speicherort ändern (optional)
// session_save_path(__DIR__ . "/sessions");

// Sicherere Cookie-Einstellungen setzen
session_set_cookie_params([
    'lifetime' => 0,        // Session-Cookie (bis Browser geschlossen wird)
    'path' => '/',          // Gilt für alle Unterseiten
    'secure' => false,      // TRUE, falls HTTPS genutzt wird
    // 'httponly' => true,     // Kein Zugriff per JavaScript möglich
    'samesite' => 'Lax'     // Schutz gegen CSRF
]);

// Session starten
session_start();

// Session-ID regelmäßig erneuern (Schutz gegen Session-Hijacking)
if (!isset($_SESSION['last_regeneration'])) {
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 300) { // Alle 5 Minuten erneuern
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// // Falls Benutzer nicht eingeloggt ist, weiterleiten (optional)
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }


/* ---- Automatisches Ausloggen nach 5 Minuten Inaktivität ---- */
// Prüfen, ob die letzte Aktivität gesetzt ist
if (isset($_SESSION['last_activity'])) {
    // Zeit seit der letzten Aktion berechnen
    $inactivity = time() - $_SESSION['last_activity'];

    // Wenn mehr als 300 Sekunden (5 Minuten) vergangen sind -> Logout
    if ($inactivity > 300) {
        session_unset();     // Alle Session-Variablen löschen
        session_destroy();   // Session beenden
        header("Location: index.php"); // Nutzer zur Logout-Seite weiterleiten
        exit();
    }
}

// Zeitstempel der letzten Aktivität aktualisieren
$_SESSION['last_activity'] = time();

?>