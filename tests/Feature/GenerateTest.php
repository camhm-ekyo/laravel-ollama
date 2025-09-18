<?php
namespace Camh\Ollama\Tests\Feature;

use Orchestra\Testbench\TestCase;
use Camh\Ollama\OllamaServiceProvider;
use Illuminate\Support\Facades\Http;

class GenerateTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [OllamaServiceProvider::class];
    }

    public function test_generate_fake()
    {
        Http::fake([
            '*/api/generate' => Http::response(['response' => 'hello world'], 200),
        ]);

        $text = app(\Camh\Ollama\Contracts\OllamaClientContract::class)->generate('ping');
        $this->assertEquals('hello world', $text);
    }
}
