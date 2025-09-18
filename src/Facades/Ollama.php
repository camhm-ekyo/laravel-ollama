<?php
namespace Camh\Ollama\Facades;

use Illuminate\Support\Facades\Facade;
use Camh\Ollama\Contracts\OllamaClientContract;

class Ollama extends Facade
{
    protected static function getFacadeAccessor()
    {
        return OllamaClientContract::class;
    }
}
