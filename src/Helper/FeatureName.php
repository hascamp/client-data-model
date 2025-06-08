<?php

namespace Hascamp\Helper;

class FeatureName
{
    public static function get(string $name, string|int|null $value = null, ?string $lang = null): string
    {
        return static::features($name, $value, $lang);
    }

    public static function features(?string $name = null, string|int|null $value = null, ?string $lang = null): array|string
    {
        $data = [

            'business_integrated' => [
                'en' => 'Business Integrated',
                'id' => 'Bisnis Terintegrasi'
            ],

            'xp' => [
                'en' => 'User-XP',
                'id' => 'User-XP'
            ],

            'point' => [
                'en' => 'Point',
                'id' => 'Poin'
            ],

            'xp-points' => [
                'en' => $value . ' Points',
                'id' => $value . ' Poin'
            ],

        ];

        if (! empty($name)) {
            if (array_key_exists($name, $data)) {

                if (empty($lang)) {
                    $data = $data[$name]['en'];
                }
    
                $data = $data[$name][$lang];

            }
        }

        return trim($data);
    }
}