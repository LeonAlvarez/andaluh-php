<?php

namespace Andaluh\Rules;

class HRules extends BaseRule
{
    const EXCEPTIONS = [
        'haz' => 'âh',
        'hez' => 'êh',
        'hoz' => 'ôh',
        'oh' => 'ôh',
        'yihad' => 'yihá',
        'h' => 'h'  # Keep an isolated h as-is
    ];

    public static function apply(string $text): string
    {
        return preg_replace_callback_array(
            [
                # chihuahua => chiguagua
                '/(?<!c)(h)(ua)/i' => function ($match) {
                    return self::isLowerCase($match[1])
                        ? "g{$match[2]}"
                        : "G{$match[2]}";
                },
                # cacahuete => cacagüete ,at the end will be cacagûete
                '/(?<!c)(h)(u)(e)/i' => function ($match) {
                    $transformed = self::keepCase($match[2], 'ü') . $match[3];
                    return self::isLowerCase($match[1])
                        ? "g{$transformed}"
                        : "G{$transformed}";
                },
                # General /h/ replacements
                '/\b(\w*?)(h)(\w*?)\b/i' => function ($match) {
                    $word = $match[0];
                    $wordLower = self::toLowerCase($word);
                    if (array_key_exists($wordLower, self::EXCEPTIONS)) {
                        return self::keepCase(
                            $wordLower,
                            self::EXCEPTIONS[$wordLower]
                        );
                    }
                    return preg_replace_callback(
                        '/(?<!c)(h)(\w?)/i',
                        function ($match) {
                            $h_char = $match[1];
                            $next_char = $match[2];

                            if ($next_char && self::isUpperCase($h_char)) {
                                return self::toUpperCase($next_char);
                            }
                            if ($next_char && self::isLowerCase($h_char)) {
                                return self::toLowerCase($next_char);
                            }

                            return '';
                        },
                        $word
                    );
                }
            ],
            $text
        );
    }
}
