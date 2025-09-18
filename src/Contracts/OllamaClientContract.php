<?php
namespace Camh\Ollama\Contracts;

interface OllamaClientContract {
    public function generate(string $prompt, array $opts = []): string;
    public function chat(array $messages, array $opts = []): string;
    public function embed(string|array $input, array $opts = []): array;
    public function stream(string $prompt, callable $onChunk, array $opts = []): void;
    public function json(string $instruction, array $opts = [], ?array $schema = null): array;
}
