<?php

use Hascamp\Helper\FeatureName;
use Illuminate\Support\Facades\App;

if(! function_exists('feature_lang')){

    function feature_lang(?string $name, string|int|null $value = null) : string {

        if ($name) {
            return FeatureName::get($name, $value, App::currentLocale());
        }

        return "";

    }

}