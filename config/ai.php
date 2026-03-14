<?php

return [
    'driver' => env('AI_DRIVER', 'ollama'),

    'ollama' => [
        'url' => env('OLLAMA_URL', 'http://localhost:11434'),
        'model' => env('OLLAMA_MODEL', 'llama3.2'),
        'timeout' => (int) env('OLLAMA_TIMEOUT', 120),
    ],

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'model' => env('GROQ_MODEL', 'llama-3.1-8b-instant'),
        'timeout' => (int) env('GROQ_TIMEOUT', 60),
    ],
];
