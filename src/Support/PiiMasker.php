<?php
namespace Camh\Ollama\Support;

class PiiMasker {
    public static function mask(string $text, array $patterns): string {
        foreach ($patterns as $re) $text = preg_replace($re, '***', $text);
        return $text;
    }
}
