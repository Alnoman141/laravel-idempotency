<?php

namespace Alnoman141\LaravelIdempotency\Support;

class MiddlewareOptions
{
    public function parse(?string $options): array
    {
        if (!$options) {
            return [];
        }

        $items = explode(',', $options);

        $result = [];

        foreach ($items as $item) {

            if (!str_contains($item, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $item);

            $result[$key] = $value;
        }

        return $result;
    }
}