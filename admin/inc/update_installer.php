<?php 
// set_time_limit(600); // 5 Minuten, je nach Bedarf anpassen
// error_reporting(E_ALL);
ini_set('display_errors', 1);
@ini_set('output_buffering', 'off');
@ini_set('zlib.output_compression', false);
@ini_set('implicit_flush', 1);
while (ob_get_level() > 0) ob_end_flush();
ob_implicit_flush(true);


// Dummy-Output für Browser-Streaming erzwingen
echo str_repeat(' ', 1024);
flush(); 
ob_flush();


// ===========================
// update_installer.php - Setup der wichtigsten Pfade und Variablen
// ===========================

//benötigte Dateien 
require ('../../components/database/db_connect.php');
// Standardverzeichnisse festlegen
$projektname = $_GET['projektname'] ?? '';
$download_url = $_GET['download_url'] ?? '';
$new_version = $_GET['version'] ?? '';
$old_version = $_GET['app_version'] ?? '';

$project_mapping = include __DIR__ . '/../../components/config/project_mapping.php';

$start_date = date(format: "d.m.Y, H:i");
// ===========================
// Standard-Verzeichnisse
// ===========================

// Root-Verzeichnis des Projekts, das aktualisiert werden soll
// Beispiel: /volume1/web/agency2025
$projekt_root = $project_mapping[$projektname]['dirname'] ?? '';

// Verzeichnis für temporäre Update-Dateien (z.B. heruntergeladene ZIP-Dateien)
// Dieses Verzeichnis wird unterhalb des Scripts angelegt, kann aber auch woanders liegen
$update_dir = $projekt_root . '/update';

// Verzeichnis für Backups vor dem Update (mit Zeitstempel)
// Backup wird z.B. in __DIR__/backups/20250714_123456/
$backup_dir = $projekt_root . '/backups/' . date('Ymd_His');

// Verzeichnis für Log-Dateien (z.B. für Update-Logs)
// Einfach ein Ordner "logs" im aktuellen Verzeichnis
$log_dir = $projekt_root . '/logs';

//Download-URL holen
$download_url = $_GET['download_url'];


// ===========================
// Verzeichnisse sicherstellen
// ===========================
$dirs = [$update_dir, $backup_dir, $log_dir];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

if (!$projekt_root) {
    die("Ungültiger Projektname");
}
// ===========================
// Verzeichnisse sicherstellen
// ===========================

// Log-Datei (jeder Aufruf bekommt eigene Log-Datei mit Timestamp)
$log_file = $log_dir . '/update_log_' . date('Ymd_His') . '.log';

// Log-Funktion: schreibt Nachricht mit Timestamp in Log-Datei
function log_message(string $message): void {
    global $log_file;
    $timestamp = date('[Y-m-d H:i:s]');
    file_put_contents($log_file, $timestamp . ' ' . $message . PHP_EOL, FILE_APPEND);
}

// Debug-Flag
$debug_enabled = true;

// Debug-Funktion: schreibt nur wenn Debug an ist
function log_debug(string $message): void {
    global $debug_enabled, $log_file;
    if ($debug_enabled) {
        $timestamp = date('[Y-m-d H:i:s][DEBUG]');
        file_put_contents($log_file, $timestamp . ' ' . $message . PHP_EOL, FILE_APPEND);
    }
}

// ===========================
// Beispiel-Ausgabe (nur zur Kontrolle, kann später entfernt werden)
// ===========================
log_message("=== Update gestartet ===");
log_debug("Download URL: $download_url");

log_message("==== Installation wird gestartet ====\n");
log_message("Ausgabe der Standard-Verzeichnisse\n");
log_message("----------------------------------\n");

echo "<pre>";
echo "=============================================\n";
echo "| <a class='up_titel'>Projekt: $projektname</a> |\n";
echo "=============================================\n";
echo "<a class = 'up_standardfont'>Installation wird gestartet... Uhrzeit: $start_date\n\n";
echo "<span class ='up_schrittmarke'>✅ Ausgabe der wichtigsten Verzeichnisse</span>: \n";
echo "Projekt Root: $projekt_root\n";
echo "Update Verzeichnis: $update_dir\n";
echo "Backup Verzeichnis: $backup_dir\n";
echo "Log Verzeichnis: $log_dir\n";
echo "Download-URL: $download_url </a>\n";
echo "aktuelle Version: $old_version </a>\n";
echo "neue Version: $new_version </a>\n";

echo "</pre>";

log_message("Projekt Root: $projekt_root");
log_message("Update Verzeichnis: $update_dir");
log_message("Backup Verzeichnis: $backup_dir");
log_message( "Log Verzeichnis: $log_dir");
log_message( "Download-URL: $download_url");


// ===============================
// 🌐 Schritt 3: Umgebung erkennen
// ===============================
$umgebung_date = date(format: "d.m.Y, H:i");

$env = 'web';
$hostname = gethostname();
if (strpos($hostname, 'raspi') !== false || strpos(__DIR__, '/home/pi') !== false) {
    $env = 'raspi';
} elseif (strpos($_SERVER['HTTP_HOST'] ?? '', '192.168.') !== false || strpos($_SERVER['REMOTE_ADDR'] ?? '', '192.168.') !== false) {
    $env = 'intranet';
}
log_message("Umgebung erkannt als: $env");

echo "<pre>";
echo "<span class ='up_schrittmarke'>🌍 Umgebung erkennen (lokal, Intranet oder Web) - Zeit: $umgebung_date </span>\n";
echo "<a class = 'up_standardfont'>Umgebung erkannt als: $env </a>";
echo "</pre>";

// ===========================
// 📥 Schritt 4: Eingangsparameter validieren
// ===========================
$parameter_date = date(format: "d.m.Y, H:i");

$projektname = $_GET['projektname'] ?? '';
$new_version = $_GET['version'] ?? '';
$download_urlget = $_GET['download_url'] ?? '';
if (!$projektname || !$new_version || !$download_url) {
    log_message("Ungültige Parameter. Projektname, Version oder Download-URL fehlen.");
    exit;
}

echo "<pre>";
echo "<span class ='up_schrittmarke'>🔤 Eingangsparameter - Zeit: $parameter_date</span>\n";
echo "<a class = 'up_standardfont'>Projektname: $projektname\n";
echo "Neue Version: $new_version\n";
echo "Download-URL: $download_urlget </a>\n";
echo "</pre>";

// ===========================
// 📌 Schritt 5: Projektzuordnung & Sicherheit
// ===========================
// Projektzuordnung & Sicherheit
$security_date = date(format: "d.m.Y, H:i");

$projekt_config = $project_mapping[$projektname] ?? null;
log_message("Schritt 5: Projektzuordnung & Sicherheit");

if (!$projekt_config) {
    log_message("Projekt '$projektname' nicht im Mapping gefunden.");
    exit;
}

$projektverzeichnis = $projekt_config['dirname'] ?? '';
// $projekt_root = realpath(dirname(__DIR__, 2)); // z. B. /var/www/html/agency2025

echo "<span class ='up_schrittmarke'>🔤 Projektzuordnung und Sicherheit - Zeit: $security_date</span>\n";
echo "<a class = 'up_standardfont'>Projekt-root: ".$projekt_root."\n";

$projektverzeichnis_name = basename($projekt_root);
$expected_path = $projekt_root;
if (!is_dir($expected_path)) {
    log_message("Projektverzeichnis nicht gefunden: $expected_path");
    echo "\n ❌ Projektverzeichnis nicht gefunden: $expected_path";
    exit;
}
log_message("Projektverzeichnis bestätigt: $expected_path");
echo "Projektverzeichnis bestätigt: $expected_path</a>";

if ($projekt_root !== $projektverzeichnis) {
    log_message("Sicherheitsfehler: Projektpfad '$projekt_root' stimmt nicht mit Mapping '$projektverzeichnis' überein.");
    echo "\n ❌ Sicherheitsfehler: Projektpfad '$projekt_root' stimmt nicht mit Mapping '$projektverzeichnis' überein.";
    exit;
}

// ===========================
// ⬇️ Schritt 6: ZIP-Datei herunterladen
// ===========================
$download_date = date(format: "d.m.Y, H:i");
$d_date = date("dmY_Hi");
$tmp_zip = tempnam(sys_get_temp_dir(), 'update_');

$target_subdir = $update_dir . '/' . $d_date;
$chunks_dir = $target_subdir. '/' . '/chunks/';
$final_zip_path = $target_subdir . '/update.zip'; // NEU: Zieldatei in Unterordner

log_message("Versuche, ZIP von URL herunterzuladen: $download_url");
echo "\n\n";
echo "<span class ='up_schrittmarke'>🔤 Download der Update-Datei - Zeit: $download_date</span>\n";
echo "<a class='up_standardfont'>Versuche, ZIP von URL herunterzuladen: $download_url</a><br>";

function downloadFile($url, $path) {
    $ch = curl_init($url);
    $fp = fopen($path, 'w');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $success = curl_exec($ch);
    if (!$success) {
        log_message("cURL Fehler: " . curl_error($ch));
        echo "<a class='up_standardfont'>❌ cURL Fehler: " . curl_error($ch) . "</a><br>";
    }
    curl_close($ch);
    fclose($fp);
    return $success;
}

if (!downloadFile($download_url, $tmp_zip)) {
    log_message("ZIP-Download fehlgeschlagen.");
    echo "❌ ZIP-Download fehlgeschlagen.";
    exit;
}

if (!file_exists($tmp_zip)) {
    log_message("ZIP-Datei existiert nach Download nicht.");
    echo "❌ ZIP-Datei existiert nach Download nicht.";
    exit;
}

// Zielverzeichnis anlegen
if (!is_dir($target_subdir)) {
    mkdir($target_subdir, 0777, true);
}

// ZIP-Datei in Unterverzeichnis kopieren
copy($tmp_zip, $final_zip_path);

log_message("ZIP-Datei erfolgreich heruntergeladen und kopiert. Verzeichnis: $target_subdir");
echo "<a class='up_standardfont'>✅ ZIP-Datei erfolgreich heruntergeladen und kopiert.<br>Datei wurde in das Verzeichnis $target_subdir verschoben.</a><br><br>";


// ===========================
// 📦 ZIP-Datei in Teile splitten (z. B. 10 MB)
// ===========================
log_message("Starte Split-Vorgang der ZIP-Datei...");
echo "<span class ='up_schrittmarke'>🔤 Splitten der Update-Datei in Teile zu je max. 10MB</span><br>";

$chunk_size = 10 * 1024 * 1024; // 10 MB
$chunk_prefix = 'update.part_';

// 🔸 Zielspeicherort für Chunks: projektspezifisch
// $chunks_dir = $update_dir . '/chunks/';
if (!is_dir($chunks_dir)) {
    mkdir($chunks_dir, 0777, true);
}

$input_handle = fopen($final_zip_path, 'rb');
if (!$input_handle) {
    log_message("Fehler beim Öffnen der ZIP-Datei zum Splitten.");
    echo "Fehler beim Öffnen der ZIP-Datei zum Splitten.";
    exit;
}

$chunk_index = 0;
while (!feof($input_handle)) {
    $chunk_data = fread($input_handle, $chunk_size);
    $chunk_name = $chunk_prefix . str_pad($chunk_index, 3, '0', STR_PAD_LEFT);
    $chunk_path = $chunks_dir . $chunk_name;

    file_put_contents($chunk_path, $chunk_data);
    log_message("ZIP-Datei in Teil gespeichert: $chunk_name");
    echo "<a class='up_standardfont'>Teil gespeichert: $chunk_name</a><br>";

    $chunk_index++;
}
fclose($input_handle);

log_message("ZIP-Datei wurde in $chunk_index Teile zu je max. 10 MB gesplittet.");
echo "<a class='up_standardfont'>✅ ZIP-Datei wurde in $chunk_index Teile zu je max. 10 MB gesplittet.</a><br>";

// ===========================
// 🗂️ Schritt 7: Backup erstellen
// ===========================
// $backup_dir = $projekt_root . '/backups/' . date('Ymd_His');
$backup_include_exts = ['php', 'css', 'js', 'html', 'json', 'lock','jpg','png','webp' ]; // erlaubte Dateitypen
$backupstart_date = date("d.m.Y, H:i");

// $projekt_root sollte gesetzt und gültig sein
$projekt_root = realpath($projekt_root);
if (!$projekt_root) {
    die("❌ Projekt root nicht gefunden\n");
}

echo "\n<span class='up_schrittmarke'>🔤 Backup des aktuellen Projekts - Zeit: $backupstart_date</span>\n";
echo "Es werden Dateien mit der Endung .php, .html, .css und .js gesichert\n";

// === 1. Dateien im Projekt-Root sichern ===
$dir = new DirectoryIterator($projekt_root);
foreach ($dir as $fileinfo) {
    if ($fileinfo->isDot() || $fileinfo->isDir()) continue;

    $path = $fileinfo->getPathname();
    $relativePath = $fileinfo->getFilename(); // nur Dateiname im Root

    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    log_message("Datei im Root gefunden: $relativePath");

    if (!in_array($ext, $backup_include_exts)) {
        log_message("Übersprungen (.$ext nicht erlaubt)\n\n");
        continue;
    }

    $ziel = $backup_dir . '/' . $relativePath;
    if (!is_dir(dirname($ziel))) mkdir(dirname($ziel), 0775, true);

    if (copy($path, $ziel)) {
        log_message("Kopiert nach: $ziel");
    } else {
        log_message("Fehler beim Kopieren: $path");
    }
}

// === 2. Backup für definierte Unterverzeichnisse ===
$unterverzeichnisse = ['admin', 'components', 'user', 'media'];

foreach ($unterverzeichnisse as $ordnername) {
    $ordner_pfad = $projekt_root . '/' . $ordnername;
    if (!is_dir($ordner_pfad)) {
        log_message("Verzeichnis nicht gefunden: $ordner_pfad");
        continue;
    }

    log_message("Suche Dateien in: $ordner_pfad");

    $rii = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($ordner_pfad, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($rii as $file) {
        if ($file->isDir()) continue;

        $path = $file->getPathname();
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        // relativer Pfad ab projekt_root
        $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', substr($path, strlen($projekt_root) + 1));

        log_message("Datei in $ordnername/: $relativePath");

        if (!in_array($ext, $backup_include_exts)) {
            log_message("Übersprungen (.$ext nicht erlaubt)");
            continue;
        }

        $ziel = $backup_dir . '/' . $relativePath;
        if (!is_dir(dirname($ziel))) mkdir(dirname($ziel), 0775, true);

        if (copy($path, $ziel)) {
            log_message("Kopiert nach: $ziel");
        } else {
            log_message("Fehler beim Kopieren: $path");
        }
    }
}

// === 3. Backup kompletter Verzeichnisse ohne Filterung ===
$voll_backup_verzeichnisse = ['licences', 'logs', 'pdf_exports'];

foreach ($voll_backup_verzeichnisse as $ordnername) {
    $ordner_pfad = $projekt_root . '/' . $ordnername;
    if (!is_dir($ordner_pfad)) {
        log_message("Komplett-Backup-Verzeichnis nicht gefunden: $ordner_pfad\n");
        continue;
    }

    log_message("Starte vollständiges Backup von: $ordner_pfad\n");

    $rii = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($ordner_pfad, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($rii as $item) {
        $quelle = $item->getPathname();
        $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', substr($quelle, strlen($projekt_root) + 1));
        $ziel = $backup_dir . '/' . $relativePath;

        if ($item->isDir()) {
            if (!is_dir($ziel)) {
                if (mkdir($ziel, 0775, true)) {
                    log_message("Verzeichnis erstellt: $ziel\n");
                } else {
                    log_message("Fehler beim Erstellen von Verzeichnis: $ziel\n");
                }
            }
        } elseif ($item->isFile()) {
            if (!is_dir(dirname($ziel))) mkdir(dirname($ziel), 0775, true);
            if (copy($quelle, $ziel)) {
                log_message("Datei kopiert: $relativePath\n");
            } else {
                log_message("Fehler beim Kopieren von $relativePath\n");
            }
        }
    }
}

// ===========================
// 🧵 Schritt 8: ZIP-Chunks zusammensetzen & entpacken
// ===========================
$chunk_date = date("d.m.Y, H:i");

log_message("🧵 Starte Zusammensetzen und Entpacken der ZIP-Chunks...");
echo "<span class='up_schrittmarke'>\n🧵 Zusammensetzen der ZIP-Datei & Entpacken - Zeit: $chunk_date </span><br>";
echo "<a class='up_standardfont'>Zuvor gesplittete Dateien werden jetzt wieder zusammengesetzt...</a>\n";
// 🔸 Quellverzeichnis der Chunks: projektspezifisch
// $chunks_dir = $update_dir . '/chunks/';

// 🔸 Ziel für die zusammengesetzte ZIP-Datei
$reassembled_zip_path = $target_subdir . '/reassembled_update.zip';

$chunk_files = glob($chunks_dir . 'update.part_*');
if (!$chunk_files || count($chunk_files) === 0) {
    log_message("Keine ZIP-Chunks gefunden in: $chunks_dir");
    echo "<a class='up_standardfont'>❌ Keine ZIP-Chunks zum Zusammensetzen gefunden.</a><br>";
    exit;
}

// 🔸 Reihenfolge sicherstellen
natsort($chunk_files);

$reassembled_handle = fopen($reassembled_zip_path, 'wb');
if (!$reassembled_handle) {
    log_message("❌ Fehler beim Erstellen der rekonstruierten ZIP-Datei.");
    echo "<a class='up_standardfont'>❌ Fehler beim Erstellen der rekonstruierten ZIP-Datei.</a><br>";
    exit;
}

// 🔗 Zusammensetzen der ZIP
foreach ($chunk_files as $chunk_file) {
    $chunk_data = file_get_contents($chunk_file);
    fwrite($reassembled_handle, $chunk_data);
    log_message("Chunk hinzugefügt: " . basename($chunk_file));
    echo "<a class='up_standardfont'>Chunk hinzugefügt: " . basename($chunk_file) . "</a><br>";
}
fclose($reassembled_handle);

// 📏 Sicherheitsprüfung
if (filesize($reassembled_zip_path) === 0) {
    log_message("❌ Die zusammengesetzte ZIP-Datei ist leer!");
    echo "<a class='up_standardfont'>❌ Die zusammengesetzte ZIP-Datei ist leer – Abbruch!</a><br>";
    exit;
}

log_message("✅ Alle Chunks wurden erfolgreich zusammengesetzt.");
echo "<a class='up_standardfont'>✅ Alle Chunks wurden erfolgreich zusammengesetzt.</a><br>";

// 🔓 Entpacken ins Projektziel
$zip = new ZipArchive();
if ($zip->open($reassembled_zip_path) === TRUE) {
    $unzip_dir = $target_subdir . '/unzipped/';
    if (!is_dir($unzip_dir)) {
        mkdir($unzip_dir, 0777, true);
    }

    $zip->extractTo($unzip_dir);
    $zip->close();
    log_message("✅ ZIP-Datei erfolgreich entpackt nach: $unzip_dir");
    echo "<a class='up_standardfont'>✅ ZIP-Datei erfolgreich entpackt nach: $unzip_dir</a><br>";
} else {
    log_message("❌ Fehler beim Entpacken der zusammengesetzten ZIP-Datei.");
    echo "<a class='up_standardfont'>❌ Fehler beim Entpacken der ZIP-Datei.</a><br>";
    exit;
}

// ===========================
// 🛠️ Schritt 9: Umgebungsspezifische Dateien umbenennen
// ===========================
log_message("Starte Umbenennung der umgebungsspezifischen Dateien für Umgebung: $env");
echo "\n<span class='up_schrittmarke'>🛠️ Umbenennung von Dateien für Umgebung <code>$env</code></span><br>";

$env_suffix_map = [
    'raspi' => '_raspi',
    'intranet' => '_nas',
    'web' => '_www'
];

$suffix = $env_suffix_map[$env] ?? null;

if (!$suffix) {
    log_message("Unbekannte Umgebung: $env – Umbenennung abgebrochen.");
    echo "<a class='up_standardfont'>❌ Unbekannte Umgebung <code>$env</code> – Umbenennung abgebrochen.</a><br>";
} else {
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($projekt_root));
    $renamed_count = 0;

    foreach ($rii as $file) {
        if ($file->isDir()) continue;

        $file_path = $file->getPathname();
        $file_name = $file->getFilename();

        if (strpos($file_name, $suffix) !== false) {
            $new_name = str_replace($suffix, '', $file_name);
            $new_path = $file->getPath() . '/' . $new_name;

            // Überschreiben, falls Ziel bereits existiert
            if (file_exists($new_path)) {
                unlink($new_path);
            }

            rename($file_path, $new_path);
            log_message("Umbenannt: $file_name → $new_name");
            echo "<a class='up_standardfont'>✅ Umbenannt: <code>$file_name</code> → <code>$new_name</code></a><br>";
            $renamed_count++;
        }
    }

    log_message("$renamed_count Datei(en) umbenannt.");
    echo "<a class='up_standardfont'>✅ <b>$renamed_count</b> Datei(en) wurden umbenannt.</a><br>";
}

// ===========================
// 🧹 Schritt 10: Nicht zur Umgebung passende Dateien löschen
// ===========================
log_message("🧹 Lösche nicht zur Umgebung passende Dateien für Umgebung: $env");
echo "\n<span class='up_schrittmarke'>🧹 Entferne unpassende Umgebungsdateien</span><br>";

$all_suffixes = ['_raspi', '_nas', '_www'];
$keep_suffix = $env_suffix_map[$env];
$delete_suffixes = array_diff($all_suffixes, [$keep_suffix]);

$deleted_count = 0;

$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($projekt_root));
foreach ($rii as $file) {
    if ($file->isDir()) continue;

    $file_path = $file->getPathname();
    $file_name = $file->getFilename();

    foreach ($delete_suffixes as $suffix_to_delete) {
        if (strpos($file_name, $suffix_to_delete) !== false) {
            unlink($file_path);
            log_message("🗑️ Datei gelöscht: $file_name");
            echo "<a class='up_standardfont'>🗑️ Datei gelöscht: <code>$file_name</code></a><br>";
            $deleted_count++;
            break; // Weiter zur nächsten Datei
        }
    }
}

log_message("✅ $deleted_count unpassende Datei(en) gelöscht.");
echo "<a class='up_standardfont'>✅ <b>$deleted_count</b> unpassende Datei(en) gelöscht.</a><br>";

// ===========================
// 🚚 Schritt 11: Verschieben der entpackten Dateien ins Projektverzeichnis
// ===========================
log_message("🚚 Starte Verschieben der entpackten Dateien...");
echo "\n<span class='up_schrittmarke'>🚚 Verschiebe entpackte Dateien ins Projektverzeichnis</span><br>";

// 🔸 Quelle: entpackte ZIP-Dateien
$source = $unzip_dir;

// 🔸 Ziel: Projekt-Hauptverzeichnis (z. B. /volume1/web/agency2025)
$destination = $projekt_root;

// 🔄 Verschiebe rekursiv alle Dateien von 'unzipped/' in das Projektverzeichnis
function move_recursive($source, $destination) {
    $dir = opendir($source);
    if (!$dir) {
        log_message("❌ Quelle konnte nicht geöffnet werden: $source");
        echo "<a class='up_standardfont'>❌ Quelle konnte nicht geöffnet werden: $source</a><br>";
        return;
    }

    while (false !== ($file = readdir($dir))) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        // // ⛔️ Unerwünschte Dateien überspringen
        // if (strpos($file, '._') === 0 || $file === '.DS_Store') {
        //     log_message("⏩ Überspringe Datei: $file");
        //     continue;
        // }

        $src_path = $source . '/' . $file;
        $dest_path = $destination . '/' . $file;

        // 🔁 Wenn es ein Verzeichnis ist: rekursiv verschieben
        if (is_dir($src_path)) {
            if (!is_dir($dest_path)) {
                mkdir($dest_path, 0777, true);
            }
            move_recursive($src_path, $dest_path);
            rmdir($src_path); // Leeres Verzeichnis löschen
        } else {
            if (rename($src_path, $dest_path)) {
                log_message("➡️ Datei verschoben: $file");
            } else {
                log_message("❌ Fehler beim Verschieben von: $file");
                echo "<a class='up_standardfont'>❌ Fehler beim Verschieben von: $file</a><br>";
            }
        }
    }

    closedir($dir);
}

move_recursive($source, $destination);

log_message("✅ Alle entpackten Dateien wurden ins Projektverzeichnis verschoben.");
echo "<a class='up_standardfont'>✅ Alle entpackten Dateien wurden ins Projektverzeichnis verschoben.</a><br>";

// ===========================
// 🧹 Schritt 12: Aufräumen – Löschen von $target_subdir und allen Inhalten
// ===========================
log_message("🧹 Starte Bereinigung des Update-Verzeichnisses...");
echo "\n<span class='up_schrittmarke'>🧹 Bereinige temporäres Update-Verzeichnis: $target_subdir</span><br>";

function delete_directory_recursive($dir) {
    if (!file_exists($dir)) return;

    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;

        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            delete_directory_recursive($path);
        } else {
            unlink($path);
        }
    }
    rmdir($dir);
}

// Ausführen
delete_directory_recursive($target_subdir);

log_message("✅ Temporäres Verzeichnis $target_subdir erfolgreich gelöscht.");
echo "<a class='up_standardfont'>✅ Temporäres Verzeichnis <code>$target_subdir</code> erfolgreich gelöscht.</a><br>";

// ===========================
// 🧹 Schritt 13: VERSION IN DB UPDATEN
// ===========================
echo "\n<span class='up_schrittmarke'>📝 Aktualisiere Version in DB...</span>\n";

$version_table = $projekt_config['version_table'] ?? 'settings';
$version_id    = $projekt_config['version_id'] ?? 1;

$res = $connect->query("SELECT site_version FROM `$version_table` WHERE id = $version_id");
$row = $res->fetch_assoc();

echo "🔍 Version vor Update: " . $row['site_version'] . "<br>";
echo "📌 Neue Version: $new_version<br>";

if ($row['site_version'] === $new_version) {
    echo "ℹ️ Version $new_version ist bereits gesetzt – kein Versionsupdate notwendig.<br>";
} else {
    $stmt = $connect->prepare("UPDATE `$version_table` SET site_version = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $new_version, $version_id);
    if ($stmt->execute()) {
        echo "<strong style='color:green;'>✅ Version erfolgreich auf $new_version gesetzt.</strong><br>";
    } else {
        echo "❌ Fehler beim Aktualisieren der Version: " . $stmt->error;
    }
    $stmt->close();
}


// ===========================
// 📦 FINALE
// ===========================
echo <<<HTML
<script>
(function() {
    const showReloadBtn = () => {
        const btn = document.getElementById('reload-btn');
        if (btn) {
            btn.style.display = 'inline-block';
        } else {
            setTimeout(showReloadBtn, 200); // Wiederholen, bis der Button im DOM ist
        }
    };
    showReloadBtn();
})();
</script>
HTML;
$backupend_date = date("d.m.Y, H:i");
echo "\n\n\n✅ Backup abgeschlossen... - Zeit: $backupend_date</a>\n";
flush(); ob_flush();

?>