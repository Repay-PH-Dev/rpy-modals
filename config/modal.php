<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Modal Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the modal system behavior
    |
    */

    'max_depth' => env('MODAL_MAX_DEPTH', 5),
    
    'default_size' => env('MODAL_DEFAULT_SIZE', 'md'),
    
    'sizes' => [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
        '4xl' => 'sm:max-w-4xl',
        '5xl' => 'sm:max-w-5xl',
        'full' => 'sm:max-w-full sm:mx-4',
    ],
    
    'allowed_namespaces' => [
        'App\\Livewire\\Modals\\',
        'App\\Http\\Livewire\\Modals\\',
    ],
    
    'scroll_lock' => env('MODAL_SCROLL_LOCK', true),
    
    'backdrop_close' => env('MODAL_BACKDROP_CLOSE', true),
    
    'escape_close' => env('MODAL_ESCAPE_CLOSE', true),
    
    'z_index_base' => env('MODAL_Z_INDEX_BASE', 50),
    
    'z_index_increment' => env('MODAL_Z_INDEX_INCREMENT', 10),
];
