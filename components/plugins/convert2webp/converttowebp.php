<?php 

    function ConvertToWebP($source, $destination, $quality = 80)
        {
            $fileExtension = strtolower(pathinfo($source, PATHINFO_EXTENSION));
            $image = false;
        
            switch ($fileExtension) {
                case 'jpeg':
                case 'jpg':
                    $image = imagecreatefromjpeg($source);
                    break;
                case 'png':
                    $image = imagecreatefrompng($source);
                    imagepalettetotruecolor($image); // Notwendig für PNGs mit Farbpalette
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                    break;
                default:
                    return false; // Falls Dateiformat nicht unterstützt wird
            }
        
            if (!$image) {
                return false; // Falls Bild nicht geladen werden konnte
            }
        
            // WebP speichern und Ergebnis prüfen
            if (!imagewebp($image, $destination, $quality)) {
                imagedestroy($image);
                return false;
            }
        
            imagedestroy($image);
            return true;
        }
        // function deleteLeerzeichen($mystring){
        //     $newstring = preg_replace('/\s+/', '', $mystring);

        //     return $newstring;
        // }


        // define('PROJECT_ROOT', dirname(__DIR__, 3));
        // // dann überall:
        // require PROJECT_ROOT . '/vendor/autoload.php';
        // require_once realpath(__DIR__ . '/../../../vendor/autoload.php');   
        $autoload = realpath(__DIR__ . '/../../../vendor/autoload.php');
        if (!$autoload || !file_exists($autoload)) {
            die("❌ Autoload nicht gefunden oder ungültig: $autoload");
        }

        function cleanString($input) {
            // Ersetze Umlaute durch ASCII-Äquivalente
            $umlaute = ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue', 'ß' => 'ss'];
            $input = strtr($input, $umlaute);
        
            // Entferne Leerzeichen und Sonderzeichen (nur Buchstaben und Zahlen bleiben)
            $input = preg_replace("/[^a-zA-Z0-9]/", "", $input);
        
            return $input;
        }
    ?> 