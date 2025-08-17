<?php
// components/config/project_mapping.php

return [
    'Bewerbungsdatenbank' => [
        'dirname' => '/var/www/html/bewerbungsdatenbank',
        'version_table' => 'settings',
        'version_id' => 1,

    // Update-Server & Channel
        'update_server' => 'http://192.168.3.44/updateserver/components/api/api_updates.php',
        'update_channel'=> 'stable', // stable, beta, etc.

    //Lizenz-Infos
        'licence_file'   => __DIR__ . '/../../licences/licence.key',
        'licence_server'  => 'http://192.168.3.44/lizenzserver/components/api/api_checklicence.php',

        'active'         => true, // Markiert dieses Projekt als "aktives Projekt"
    ],
];