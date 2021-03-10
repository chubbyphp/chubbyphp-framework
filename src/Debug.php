<?php

declare(strict_types=1);

namespace Chubbyphp\Framework;

class Debug
{
    public static function debug(iterable $data): array
    {
        $debug = [];
        foreach ($data as $key => $value) {
            if ($value instanceof DebugInterface) {
                $value = $value->debug();
            }

            if (is_iterable($value)) {
                $debug[$key] = self::debug($value);
            } else if (is_object($value)) {
                $debug[$key] = get_class($value);
            } else {
                $debug[$key] = $value;
            }
        }

        return $debug;
    }
}
