<?php
namespace Camh\Ollama\Clients;

use Camh\Ollama\Contracts\OllamaClientContract;
use Illuminate\Support\Facades\Http;
use Camh\Ollama\Support\PiiMasker;
use Camh\Ollama\Support\JsonRepairer;

class OllamaHttpClient implements OllamaClientContract
{
    public function __construct(private array $cfg) {}

    private function base() { return rtrim($this->cfg['base'], '/'); }
    private function timeout() { return (int)($this->cfg['timeout'] ?? 60); }
    private function defaultModel() { return $this->cfg['default_model'] ?? 'llama3.1'; }
    private function embedModel() { return $this->cfg['embed_model'] ?? 'nomic-embed-text'; }

    public function generate(string $prompt, array $opts = []): string
    {
        $prompt = $this->mask($prompt);
        $payload = array_merge([
            'model'  => $opts['model'] ?? $this->defaultModel(),
            'prompt' => $prompt,
            'stream' => false,
        ], $opts);

        $res = Http::timeout($this->timeout())->post($this->base().'/api/generate', $payload);
        $res->throw();
        return $res->json('response', '');
    }

    public function chat(array $messages, array $opts = []): string
    {
        if (($this->cfg['mask_pii'] ?? true)) {
            foreach ($messages as &$m) $m['content'] = PiiMasker::mask($m['content'] ?? '', $this->cfg['pii_patterns']);
        }
        $payload = array_merge([
            'model'    => $opts['model'] ?? $this->defaultModel(),
            'messages' => $messages,
            'stream'   => false,
        ], $opts);

        $res = Http::timeout($this->timeout())->post($this->base().'/api/chat', $payload);
        $res->throw();
        return data_get($res->json(), 'message.content')
            ?? data_get($res->json(), 'choices.0.message.content')
            ?? '';
    }

    public function embed(string|array $input, array $opts = []): array
    {
        $res = Http::timeout($this->timeout())->post($this->base().'/api/embeddings', [
            'model' => $opts['model'] ?? $this->embedModel(),
            'input' => $input,
        ]);
        $res->throw();
        return $res->json('embedding') ?? $res->json('embeddings') ?? [];
    }

    public function stream(string $prompt, callable $onChunk, array $opts = []): void
    {
        $prompt = $this->mask($prompt);
        $payload = array_merge([
            'model'  => $opts['model'] ?? $this->defaultModel(),
            'prompt' => $prompt,
            'stream' => true,
        ], $opts);

        Http::timeout($this->timeout())
            ->withOptions({'stream': True})
            ->post($this->base().'/api/generate', $payload)
            ->throw()
            ->getBody()
            ->each(function ($chunk) use ($onChunk) { $onChunk($chunk); });
    }

    public function json(string $instruction, array $opts = [], ?array $schema = null): array
    {
        $guard = "Chỉ trả JSON thuần, không thêm chữ ngoài JSON.";
        if ($schema) $guard .= " Schema (JSON Schema): ".json_encode($schema);

        $raw = $this->generate($instruction."\n\n".$guard, $opts);
        $parsed = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE) return $parsed;

        $repaired = JsonRepairer::repairToJson($raw, $schema);
        $parsed = json_decode($repaired, true);
        if (json_last_error() === JSON_ERROR_NONE) return $parsed;

        return ['_raw' => $raw, '_error' => 'invalid_json'];
    }

    private function mask(string $s): string
    {
        return ($this->cfg['mask_pii'] ?? true) ? PiiMasker::mask($s, $this->cfg['pii_patterns']) : $s;
    }
}
