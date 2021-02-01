<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Gallery Filesystem Path
    |--------------------------------------------------------------------------
    |
    | The location of the files to display in the gallery.
    |
    */

    'path' => Str::finish(env('GALLERY_PATH'), '/'),

    /*
    |--------------------------------------------------------------------------
    | Gallery Authentication
    |--------------------------------------------------------------------------
    |
    | Determines whether users are required to log in before they can access
    | the gallery.
    |
    */

    'auth' => env('GALLERY_AUTH', false),

    /*
    |--------------------------------------------------------------------------
    | Gallery Registration
    |--------------------------------------------------------------------------
    |
    | Determines whether users can register accounts on their own. When false,
    | users have to be added via the user:add command.
    |
    */

    'registration' => env('GALLERY_REGISTRATION', false),

];
