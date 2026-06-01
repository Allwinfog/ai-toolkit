<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Provider Settings
    |--------------------------------------------------------------------------
    */
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'organization' => env('OPENAI_ORGANIZATION'),
        'models' => [
            'text' => ['gpt-4o', 'gpt-4o-mini', 'gpt-3.5-turbo'],
            'image' => ['dall-e-3', 'dall-e-2'],
            'code' => ['gpt-4o', 'gpt-4o-mini'],
        ],
        'defaults' => [
            'text_model' => 'gpt-4o-mini',
            'image_model' => 'dall-e-3',
            'code_model' => 'gpt-4o-mini',
            'max_tokens' => 2048,
            'temperature' => 0.7,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Plan Limits (overridden by DB plan settings)
    |--------------------------------------------------------------------------
    */
    'plans' => [
        'free' => [
            'text_generations' => 10,
            'image_generations' => 5,
            'code_generations' => 10,
            'words_per_generation' => 1000,
        ],
        'starter' => [
            'text_generations' => 100,
            'image_generations' => 50,
            'code_generations' => 100,
            'words_per_generation' => 3000,
        ],
        'pro' => [
            'text_generations' => 500,
            'image_generations' => 200,
            'code_generations' => 500,
            'words_per_generation' => 5000,
        ],
        'unlimited' => [
            'text_generations' => -1,
            'image_generations' => -1,
            'code_generations' => -1,
            'words_per_generation' => 10000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Templates Categories
    |--------------------------------------------------------------------------
    */
    'template_categories' => [
        'blog' => 'Blog & Articles',
        'marketing' => 'Marketing & Ads',
        'social' => 'Social Media',
        'email' => 'Email & Newsletters',
        'seo' => 'SEO & Meta',
        'ecommerce' => 'E-Commerce',
        'creative' => 'Creative Writing',
        'business' => 'Business & Professional',
        'code' => 'Code Generation',
    ],
];
