<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Request Authentication
    |--------------------------------------------------------------------------
    |
    */

    'auth_header' => env('GRAPHQL_AUTHENTICATION_HEADER', 'Authorization'),
    'auth_credentials' => env('GRAPHQL_CREDENTIALS'),

    /*
    |--------------------------------------------------------------------------
    | Request Authentication scheme
    | 
    | Valid Schemes: basic, bearer, custom
    |--------------------------------------------------------------------------
    |
    */

    'auth_scheme' => env('GRAPHQL_AUTHENTICATION', 'bearer'),
    
    'auth_schemes' => [
        'basic'  => 'Basic ',
        'bearer' => 'Bearer ',
        'custom' => ''
    ],

    /*
    |--------------------------------------------------------------------------
    | GraphQL endpoint
    |--------------------------------------------------------------------------
    |
    */

    'graphql_endpoint' => env('GRAPHQL_ENDPOINT'),
  
];