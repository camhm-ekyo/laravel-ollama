<?php
namespace Camh\Ollama;

use Illuminate\Support\ServiceProvider;
use Camh\Ollama\Contracts\OllamaClientContract;
use Camh\Ollama\Clients\OllamaHttpClient;

class OllamaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/ollama.php', 'ollama');

        $this->app->singleton(OllamaClientContract::class, function ($app) {
            return new OllamaHttpClient(config('ollama'));
        });
    }

    public function boot(): void
    {
        $this->publishes([ __DIR__.'/../config/ollama.php' => config_path('ollama.php') ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Camh\Ollama\Console\Commands\TagsCommand::class,
                \Camh\Ollama\Console\Commands\PullCommand::class,
                \Camh\Ollama\Console\Commands\ChatCommand::class,
            ]);
        }
    }
}
