<?php 
    function getAppSettings(mysqli $connect): array {
        $sql = "SELECT site_version, projektname FROM settings WHERE id = 1";
        $res = mysqli_query($connect, $sql);

        if ($res && mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            return [
                'version' => trim($row['site_version']),
                'projektname' => trim($row['projektname']),
                'current_page' => getCurrentPage()
            ];
        }

        return [
            'version' => '',
            'projektname' => '',
            'current_page' => getCurrentPage()
        ];
    }
?>