<?php
return [
    'path' => env('DB_BACKUP_PATH', storage_path('app/backups')),
    'retention_days' => env('DB_BACKUP_RETENTION_DAYS', 7),
];
