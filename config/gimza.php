<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Налог администратора за почетне податке (php artisan db:seed)
    |--------------------------------------------------------------------------
    |
    | Ако ADMIN_PASSWORD није постављен у .env, seeder прави насумичну лозинку
    | и исписује је у терминалу.
    |
    */

    'admin' => [
        'email' => env('ADMIN_EMAIL', 'admin@gimza.test'),
        'username' => env('ADMIN_USERNAME', 'admin'),
        'password' => env('ADMIN_PASSWORD'),
    ],

];
