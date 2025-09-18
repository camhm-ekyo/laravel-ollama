<?php
namespace Camh\Ollama\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ChatCommand extends Command
{
    protected $signature = 'ollama:chat {prompt*} {--model=}';
    protected $description = 'Quick chat with Ollama (single prompt)';

    public function handle(): int
    {
        $prompt = implode(' ', $this->argument('prompt'));
        $model = $this->option('model') ?? config('ollama.default_model');
        $base = config('ollama.base');

        $res = Http::timeout(60)->post(rtrim($base,'/').'/api/generate', [
            'model' => $model,
            'prompt' => $prompt,
            'stream' => false,
        ]);
        if (!$res->ok()) {
            $this->error('Chat failed: '.$res->body());
            return 1;
        }
        $this->line($res->json('response'));
        return 0;
    }
}
