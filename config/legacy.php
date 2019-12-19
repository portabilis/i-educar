<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Legacy Code
    |--------------------------------------------------------------------------
    |
    | This value determines which application is running. Default is the new
    | version using Laravel structure. Another case your application is running
    | using i-Educar legacy code.
    |
    */

    'code' => env('LEGACY_CODE', true),

    /*
    |--------------------------------------------------------------------------
    | Display Errors
    |--------------------------------------------------------------------------
    |
    | This value determines if the errors that legacy code throws is showed for
    | user in debug mode.
    |
    */

    'display_errors' => env('LEGACY_DISPLAY_ERRORS', false),

    /*
    |--------------------------------------------------------------------------
    | Path
    |--------------------------------------------------------------------------
    |
    | This value determines the path of the legacy code.
    |
    */

    'path' => env('LEGACY_PATH', 'ieducar'),

    /*
    |--------------------------------------------------------------------------
    | Migrations
    |--------------------------------------------------------------------------
    |
    | List of paths that contains migrations of the other repositories or
    | packages that works with i-Educar.
    |
    */

    'migrations' => [
        database_path('migrations/extras'),
        database_path('migrations/legacy'),
        base_path('ieducar/modules/Reports/database/migrations'),
    ],

    'env' => env('LEGACY_ENV', 'local'),

    'gtm' => env('GOOGLE_TAG_MANAGER'),

    'apis' => [
        'access_key' => env('API_ACCESS_KEY'),
        'secret_key' => env('API_SECRET_KEY'),
        'educacao_token_header' => '',
        'educacao_token_key' => '',
    ],

    'app' => [
        'name' => env('APP_NAME', 'i-Educar'),
        'diario' => [
            'nomenclatura_exame' => '0',
        ],
        'database' => [
            'hostname' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'dbname' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
        ],
        'administrative_pending' => [
            'exist' => '',
            'msg' => '',
        ],
        'aws' => [
            'bucketname' => env('AWS_BUCKET'),
            'awsacesskey' => env('AWS_ACCESS_KEY_ID'),
            'awssecretkey' => env('AWS_SECRET_ACCESS_KEY'),
        ],
        'template' => [
            'vars' => [
                'instituicao' => 'Prefeitura Municipal',
            ],
            'pdf' => [
                'titulo' => 'Relatório i-Educar',
                'logo' => '',
            ],
            'layout' => 'login.tpl',
        ],
        'gtm' => [
            'id' => env('GOOGLE_TAG_MANAGER'),
        ],
        'rdstation' => [
            'token' => '',
            'private_token' => '',
        ],
        'locale' => [
            'country' => '45',
            'province' => 'SP',
            'timezone' => 'America/Sao_Paulo',
        ],
        'admin' => [
            'reports' => [
                'sql_tempo' => '3',
                'pagina_tempo' => '5',
                'emails' => '',
            ],
        ],
        'entity' => [
            'name' => 'Prefeitura Municipal',
        ],
        'superuser' => 'admin',
        'user_accounts' => [
            'default_password_expiration_period' => '180',
        ],
        'instituicao' => [
            'data_base_deslocamento' => '1',
        ],
        'novoeducacao' => [
            'url' => '',
            'caminho_api' => '',
        ],
        'auditoria' => [
            'notas' => '1',
        ],
        'matricula' => [
            'dependencia' => '1',
        ],
        'alunos' => [
            'laudo_medico_obrigatorio' => '1',
            'nao_apresentar_campo_alfabetizado' => '0',
            'codigo_sistema' => 'Código sistema',
            'mostrar_codigo_sistema' => '1',
        ],
        'faltas_notas' => [
            'mostrar_botao_replicar' => '1',
        ],
        'mailer' => [
            'smtp' => [
                'from_name' => env('MAIL_FROM_NAME', 'Example'),
                'from_email' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
                'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
                'port' => env('MAIL_PORT', 587),
                'auth' => boolval(env('MAIL_ENCRYPTION', 'tls')),
                'username' => env('MAIL_USERNAME'),
                'password' => env('MAIL_PASSWORD'),
                'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            ],
            'debug' => false,
        ],
        'recaptcha' => [
            'public_key' => '',
            'private_key' => '',
            'options' => [
                'secure' => true,
                'lang' => 'pt',
                'theme' => 'white',
            ],
        ],
    ],

    'modules' => [
        'error' => [
            'link_to_support' => 'https://forum.ieducar.org/',
            'send_notification_email' => true,
            'notification_email' => '1',
            'show_details' => true,
            'track' => false,
            'tracker_name' => 'EMAIL',
            'honeybadger_key' => '',
            'email_recipient' => '',
        ],
    ],

    'report' => [
        'debug' => env('REPORTS_DEBUG', false),
        'caminho_fundo_certificado' => '',
        'caminho_fundo_carteira_transporte' => '',
        'lei_estudante' => 'Lei municipal',
        'lei_conclusao_ensino_medio' => '',
        'portaria_aprovacao_pontos' => 'Resolução n° 12/2011 - CME, Artigo 7°, § 2°;',
        'modelo_ficha_individual' => 'todos',
        'mostrar_relatorios' => '',
        'logo_file_name' => 'brasil.png',
        'show_error_details' => true,
        'default_factory' => env('REPORTS_FACTORY', 'Portabilis_Report_ReportFactoryPHPJasper'),
        'source_path' => env('REPORTS_SOURCE_PATH', base_path('ieducar/modules/Reports/ReportSources/')),
        'diario_classe' => [
            'dias_temporarios' => '30',
        ],
        'remote_factory' => [
            'url' => env('REPORTS_URL'),
            'token' => env('REPORTS_TOKEN'),
            'this_app_name' => '',
            'username' => '',
            'password' => '',
            'logo_name' => '',
        ],
    ],

];
