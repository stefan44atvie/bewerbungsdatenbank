<?php 
    function formatFileSize($bytes)
        {
            if (!is_numeric($bytes)) {
                return "Invalid size";
            }
            $units = ["Bytes", "KB", "MB", "GB", "TB"];
            $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
            $formattedSize = number_format($bytes / pow(1024, $power), 2);
            return $formattedSize . " " . $units[$power];
        }
?>