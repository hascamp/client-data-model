<?php

return [

    "services" => [
        "app" => \Hascamp\Direction\Builder\Services\ClientApplication::class,
    ],

    'license_key' => env("X_LICENSE_KEY", null),
    'app_id' => env("X_APP_ID", null),
    'connection' => env("X_CONNECTION", null),

];