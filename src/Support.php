<?php

namespace Vietiso\OneGuide;

class Support
{
    public static function trimString($value)
    {
        if (is_string($value)) {
            return preg_replace('~^[\s\x{FEFF}\x{200B}]+|[\s\x{FEFF}\x{200B}]+$~u', '', $value ?? '') ?? trim($value);
        }

        return $value;
    }
}