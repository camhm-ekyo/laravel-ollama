<?php
namespace Camh\Ollama\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PullCommand extends Command
{
    protected $signature = 'ollama:pull {model}';
    protected $description = 'Pull an Ollama model';

    public function handle(): int
    {
        $model = $this->argument('model');
        $base = config('ollama.base');

        $res = Http::post(rtrim($base,'/').'/api/pull', ['name' => $model, 'stream' => false]);
        if (!$res->ok()) {
            $this->error('Pull failed: '.$res->body());
            return 1;
        }
        $this->info('Pulled: '.$model);
        return 0;
    }
}
