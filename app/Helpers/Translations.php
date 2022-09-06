<?php

use App\Models\Lang\Locale;
use App\Models\Lang\Translation;

function t($key, $lang = 'en', array $attr = [])
{
    $result = Translation::where('locale_id', Locale::where('label', $lang)->first()->id)->where('key', $key)->first()->value ?? $key;
    foreach ($attr as $k => $v) {
        $result = str_replace("{" . $k . "}", $v, $result);
    }
    return $result;
}

function ar($key = null, $attr = []) { return t($key, 'ar', $attr); }
function en($key = null, $attr = []) { return t($key, 'en', $attr); }

// foreach (Locale::all() as $locale) {
// eval('function '. $locale->label . ' ($key = null, $attr = []) { return t($key, "'. $locale->label .'", $attr); }');
// }