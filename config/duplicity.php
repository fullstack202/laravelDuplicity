<?php

return [
    'backup_directory' => env('DUPLICITY_BACKUP_DIRECTORY', './'),
    'backup_to_url' => env('DUPLICITY_BACKUP_TO_URL', 'file://./backup'),

    'restore_url' => env('DUPLICITY_RESTORE_URL', 'file://./backup'),
    'restore_to_directory' => env('DUPLICITY_RESTORE_TO_DIRECTORY', './restore'),

    'excludes' => [
        './backup',
        './restore',
    ],

    'database_dump' => env('DATABASE_NAME', 'database_dump') . '.sql',

    /*
    |--------------------------------------------------------------------------
    | FTP Password
    |--------------------------------------------------------------------------
    |
    | Supported by most backends which are password capable. More secure
    | than setting it in the backend url (which might be readable in the
    | operating systems process listing to other users on the same machine).
    |
    */

    'ftp_password' => env('DUPLICITY_FTP_PASSWORD'),
];
