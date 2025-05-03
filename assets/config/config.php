<?php

use Hanafalah\ModuleIcd\Commands;
use Hanafalah\ModuleIcd\Models as ModuleIcd;

return [
    'namespace' => 'Hanafalah\\ModuleIcd',
    'app' => [
        'contracts' => [
            //ADD YOUR CONTRACTS HERE
        ]
    ],
    'libs' => [
        'model' => 'Models',
        'contract' => 'Contracts',
        'schema' => 'Schemas',
        'database' => 'Database',
        'data' => 'Data',
        'resource' => 'Resources',
        'migration' => '../assets/database/migrations'
    ],
    'version'            => [],
    'lang'               => 'en',
    'api_version'        => env('ICD_API_VERSION', 'v2'),
    'authentication'     => [
        'client_id'      => env('ICD_CLIENT_ID'),
        'client_secret'  => env('ICD_CLIENT_SECRET')
    ],
    'translate' => [
        'to'  => 'id'
    ],
    'database' => [
        'models' => [
        ]
    ],
    'commands' => [
        Commands\InstallMakeCommand::class,
        Commands\ScrappingMakeCommand::class,
        Commands\ICDTranslateCommand::class
    ]
];
