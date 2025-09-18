# camh/laravel-ollama

Laravel client cho **Ollama** (local LLM): generate, chat, embeddings + PII masking + JSON guardrails.

## Cài đặt
```bash
composer require camh/laravel-ollama
php artisan vendor:publish --tag=config --provider="Camh\\Ollama\\OllamaServiceProvider"
```

## .env
```
OLLAMA_BASE=http://127.0.0.1:11434
OLLAMA_GEN_MODEL=llama3.1
OLLAMA_EMBED_MODEL=nomic-embed-text
```

## Ví dụ
```php
use Camh\\Ollama\\Facades\\Ollama;

$text = Ollama::generate('Xin chào!', ['options'=>['temperature'=>0.2]]);
$reply = Ollama::chat([['role'=>'user','content'=>'Tạo migration users']], []);
$vec   = Ollama::embed('Xin chào thế giới');
```

## Artisan
```bash
php artisan ollama:tags
php artisan ollama:pull llama3.1
php artisan ollama:chat "Nói xin chào bằng 1 câu"
```

## Testing
```bash
composer test
```
