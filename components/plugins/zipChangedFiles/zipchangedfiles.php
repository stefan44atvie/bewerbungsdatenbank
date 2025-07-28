<?php
/**
 * changed_zip_creator.php – Funktion, die ein ZIP mit nur geänderten Dateien erstellt.
 */

function createChangedFilesZip(string $sourceDir, string $zipTarget, string $mode = 'hash'): bool {
    $tmpDir = __DIR__ . '/tmp';
    if (!is_dir($tmpDir)) {
        mkdir($tmpDir, 0777, true);
    }
    putenv('TMPDIR=' . $tmpDir);
    echo sys_get_temp_dir();
    $hashStore = __DIR__ . '/.hash_snapshot.json';
    $referenceTime = strtotime('2024-12-01 00:00:00'); // Nur für mtime-Modus

    if (!is_dir($sourceDir)) {
        echo "❌ Quellverzeichnis existiert nicht: $sourceDir";
        return false;
    }

    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sourceDir));
    $changedFiles = [];
    $newHashes = [];
    $baseLength = strlen($sourceDir) + 1;

    if ($mode === 'mtime') {
        foreach ($rii as $file) {
            if ($file->isFile() && $file->getMTime() >= $referenceTime) {
                $changedFiles[] = [
                    'path' => $file->getPathname(),
                    'relative' => substr($file->getPathname(), $baseLength)
                ];
            }
        }
    }

    if ($mode === 'hash') {
        $oldHashes = file_exists($hashStore) ? json_decode(file_get_contents($hashStore), true) : [];

        foreach ($rii as $file) {
            if ($file->isFile()) {
                $path = $file->getPathname();
                $relative = substr($path, $baseLength);
                $hash = md5_file($path);
                $newHashes[$relative] = $hash;

                if (!isset($oldHashes[$relative]) || $oldHashes[$relative] !== $hash) {
                    $changedFiles[] = ['path' => $path, 'relative' => $relative];
                }
            }
        }
    }

    $zip = new ZipArchive();
    if (!$zip->open($zipTarget, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
        echo "❌ ZIP-Datei konnte nicht erstellt werden.";
        return false;
    }

    foreach ($changedFiles as $file) {
        $zip->addFile($file['path'], $file['relative']);
    }
    $zip->close();

    setFlashMessage(type: 'success', message: '✅ ZIP mit " . count($changedFiles) . " geänderten Dateien erstellt: $zipTarget<br>');
    echo "✅ ZIP mit " . count($changedFiles) . " geänderten Dateien erstellt: $zipTarget<br>";

    // --- Zeitstempel im JSON speichern
    if ($mode === 'hash') {
        $newHashes['_last_run'] = date('Y-m-d H:i:s');
        file_put_contents($hashStore, json_encode($newHashes, JSON_PRETTY_PRINT));
    }

    return true;
}
?>