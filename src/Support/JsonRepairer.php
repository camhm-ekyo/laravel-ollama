<?php
namespace Camh\Ollama\Support;

class JsonRepairer {
    public static function repairToJson(string $raw, ?array $schema = null): string {
        $first = strpos($raw, '{'); $last = strrpos($raw, '}');
        if ($first !== false && $last !== false && $last > $first) {
            return substr($raw, $first, $last - $first + 1);
        }
        return '{}';
    }
}
