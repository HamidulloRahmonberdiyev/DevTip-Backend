<?php

declare(strict_types=1);

return [
    'enabled' => (bool) env('AI_LOG_ANALYZER_ENABLED', false),

    /*
    | AI reply language. Defaults to APP_LOCALE. When "en", Uzbek questions still get Uzbek replies.
    | Examples: en, uz, ru
    */
    'locale' => env('AI_LOG_ANALYZER_LOCALE', env('APP_LOCALE', 'en')),

    'ai' => [
        'provider' => env('AI_PROVIDER', 'ollama'),
    ],

    'providers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            'timeout' => (int) env('OPENAI_TIMEOUT', 45),
            'max_prompt_chars' => (int) env('OPENAI_MAX_PROMPT_CHARS', 12000),
        ],

        'anthropic' => [
            'api_key' => env('ANTHROPIC_API_KEY'),
            'model' => env('ANTHROPIC_MODEL', 'claude-sonnet-4-20250514'),
            'base_url' => env('ANTHROPIC_BASE_URL', 'https://api.anthropic.com/v1'),
            'anthropic_version' => env('ANTHROPIC_VERSION', '2023-06-01'),
            'max_tokens' => (int) env('ANTHROPIC_MAX_TOKENS', 4096),
            'timeout' => (int) env('ANTHROPIC_TIMEOUT', 60),
            'max_prompt_chars' => (int) env('ANTHROPIC_MAX_PROMPT_CHARS', 12000),
        ],

        'cohere' => [
            'api_key' => env('COHERE_API_KEY'),
            'model' => env('COHERE_MODEL', 'command-r-plus-08-2024'),
            'base_url' => env('COHERE_BASE_URL', 'https://api.cohere.ai/v1'),
            'timeout' => (int) env('COHERE_TIMEOUT', 60),
            'max_prompt_chars' => (int) env('COHERE_MAX_PROMPT_CHARS', 12000),
        ],

        'gemini' => [
            'api_key' => env('GEMINI_API_KEY'),
            'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
            'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
            'timeout' => (int) env('GEMINI_TIMEOUT', 60),
            'max_prompt_chars' => (int) env('GEMINI_MAX_PROMPT_CHARS', 12000),
        ],

        'mistral' => [
            'api_key' => env('MISTRAL_API_KEY'),
            'model' => env('MISTRAL_MODEL', 'mistral-small-latest'),
            'base_url' => env('MISTRAL_BASE_URL', 'https://api.mistral.ai/v1'),
            'timeout' => (int) env('MISTRAL_TIMEOUT', 60),
            'max_prompt_chars' => (int) env('MISTRAL_MAX_PROMPT_CHARS', 12000),
        ],

        'xai' => [
            'api_key' => env('XAI_API_KEY'),
            'model' => env('XAI_MODEL', 'grok-3-mini'),
            'base_url' => env('XAI_BASE_URL', 'https://api.x.ai/v1'),
            'timeout' => (int) env('XAI_TIMEOUT', 60),
            'max_prompt_chars' => (int) env('XAI_MAX_PROMPT_CHARS', 12000),
        ],

        'ollama' => [
            'url' => rtrim((string) env('OLLAMA_URL', 'http://127.0.0.1:11434'), '/'),
            'model' => env('OLLAMA_MODEL', 'llama3.2'),
            'api_key' => env('OLLAMA_API_KEY'),
            'timeout' => (int) env('OLLAMA_TIMEOUT', 60),
            'max_prompt_chars' => (int) env('OLLAMA_MAX_PROMPT_CHARS', 12000),
        ],

        'jina' => [
            'api_key' => env('JINA_API_KEY'),
            'model' => env('JINA_MODEL', 'gpt-4o-mini'),
            'base_url' => env('JINA_BASE_URL', ''),
            'timeout' => (int) env('JINA_TIMEOUT', 60),
            'max_prompt_chars' => (int) env('JINA_MAX_PROMPT_CHARS', 12000),
        ],

        'voyageai' => [
            'api_key' => env('VOYAGEAI_API_KEY'),
            'model' => env('VOYAGEAI_MODEL', 'gpt-4o-mini'),
            'base_url' => env('VOYAGEAI_BASE_URL', ''),
            'timeout' => (int) env('VOYAGEAI_TIMEOUT', 60),
            'max_prompt_chars' => (int) env('VOYAGEAI_MAX_PROMPT_CHARS', 12000),
        ],

        'elevenlabs' => [
            'api_key' => env('ELEVENLABS_API_KEY'),
            'model' => env('ELEVENLABS_MODEL', 'gpt-4o-mini'),
            'base_url' => env('ELEVENLABS_BASE_URL', ''),
            'timeout' => (int) env('ELEVENLABS_TIMEOUT', 60),
            'max_prompt_chars' => (int) env('ELEVENLABS_MAX_PROMPT_CHARS', 12000),
        ],
    ],

    'debugging_chat' => [
        'enabled' => (bool) env('AI_DEBUG_CHAT_ENABLED', true),
        'include_recent_log' => (bool) env('AI_DEBUG_CHAT_INCLUDE_LOG', true),
        'max_log_tail_chars' => (int) env('AI_DEBUG_CHAT_MAX_LOG_CHARS', 8000),
        'log_paths' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('AI_DEBUG_CHAT_LOG_PATHS', storage_path('logs/laravel.log')))
        ))),
        'include_migrations' => (bool) env('AI_DEBUG_CHAT_INCLUDE_MIGRATIONS', true),
        'migrations_path' => env('AI_DEBUG_CHAT_MIGRATIONS_PATH', database_path('migrations')),
        'max_migration_files' => (int) env('AI_DEBUG_CHAT_MAX_MIGRATION_FILES', 40),

        /*
        | Read .php paths from the log (stack traces) and pull source snippets + related files
        | resolved from `use` imports (host app composer.json PSR-4). Vendor is off by default.
        */
        'include_code_from_log' => (bool) env('AI_DEBUG_CHAT_INCLUDE_CODE', true),
        'code_context_scan_imports' => (bool) env('AI_DEBUG_CHAT_CODE_SCAN_IMPORTS', true),
        'code_context_lines_around' => (int) env('AI_DEBUG_CHAT_CODE_LINES_AROUND', 80),
        'code_context_max_chars' => (int) env('AI_DEBUG_CHAT_CODE_MAX_CHARS', 32000),
        'code_context_max_files' => (int) env('AI_DEBUG_CHAT_CODE_MAX_FILES', 16),
        'code_context_max_file_chars' => (int) env('AI_DEBUG_CHAT_CODE_MAX_FILE_CHARS', 14000),
        'code_context_max_related' => (int) env('AI_DEBUG_CHAT_CODE_MAX_RELATED', 12),
        'code_context_import_scan_bytes' => (int) env('AI_DEBUG_CHAT_CODE_IMPORT_SCAN_BYTES', 65536),
        'code_context_allow_vendor' => (bool) env('AI_DEBUG_CHAT_CODE_ALLOW_VENDOR', false),
        'code_context_allowed_roots' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('AI_DEBUG_CHAT_CODE_ALLOWED_ROOTS', ''))
        ))),
        'code_context_extra_deny_paths' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('AI_DEBUG_CHAT_CODE_EXTRA_DENY', ''))
        ))),
    ],

    'telegram' => [
        'enabled' => (bool) env('TELEGRAM_NOTIFICATIONS_ENABLED', true),
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
        'timeout' => (int) env('TELEGRAM_TIMEOUT', 15),
        'share_button_enabled' => (bool) env('TELEGRAM_SHARE_BUTTON_ENABLED', true),
        'webhook' => [
            'enabled' => (bool) env('TELEGRAM_WEBHOOK_ENABLED', true),
            'path' => env('TELEGRAM_WEBHOOK_PATH', 'ai-log-analyzer/telegram/webhook'),
            'secret' => env('TELEGRAM_WEBHOOK_SECRET'),
            'register_on_boot' => (bool) env('TELEGRAM_WEBHOOK_REGISTER_ON_BOOT', true),
        ],
    ],

    'queue' => [
        'enabled' => (bool) env('AI_LOG_ANALYZER_QUEUE', true),
        'connection' => env('AI_LOG_ANALYZER_QUEUE_CONNECTION'),
        'name' => env('AI_LOG_ANALYZER_QUEUE_NAME'),
    ],

    'rate_limit' => [
        'enabled' => (bool) env('AI_LOG_ANALYZER_RATE_LIMIT', true),
        'cache_ttl' => (int) env('AI_LOG_ANALYZER_RATE_LIMIT_TTL', 600),
        'max_per_window' => (int) env('AI_LOG_ANALYZER_RATE_LIMIT_MAX', 1),
    ],

    'log_levels' => [
        'error',
        'critical',
        'alert',
        'emergency',
    ],

    'redact_context_keys' => [
        'password',
        'password_confirmation',
        'token',
        'secret',
        'api_key',
        'apikey',
        'authorization',
        'cookie',
        'credit_card',
    ],

    'ignore_message_patterns' => [
        '/^\[ai-log-analyzer\]/i',
    ],
];
