<?php
namespace Camh\Ollama\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TagsCommand extends Command
{
    protected $signature = 'ollama:tags';
    protected $description = 'List local Ollama models';

    public function handle(): int
    {
        $base = config('ollama.base');
        $res = Http::get(rtrim($base,'/').'/api/tags');
        if (!$res->ok()) {
            $this->error('Cannot reach Ollama at '.$base);
            return 1;
        }
        $this->line(json_encode($res->json(), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        return 0;
    }
}
