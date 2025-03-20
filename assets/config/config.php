<?php

use Zahzah\ModuleIcd\Commands;
use Zahzah\ModuleIcd\Models as ModuleIcd;

return [
    'version'            => [
        
    ],
    'lang'               => 'en',
    'api_version'        => env('ICD_API_VERSION','v2'),
    'authentication'     => [
        'client_id'      => env('ICD_CLIENT_ID'), 
        'client_secret'  => env('ICD_CLIENT_SECRET')
    ],
    'translate' => [
        'to'  => 'id'
    ],
    'database' => [
        'models' => [
            'ICD'   => ModuleIcd\ICD::class,
            'ICD10' => ModuleIcd\ICD10::class,
        ]
    ],
    'commands' => [
        Commands\InstallMakeCommand::class,
        Commands\ScrappingMakeCommand::class,
        Commands\ICDTranslateCommand::class
    ]
];
