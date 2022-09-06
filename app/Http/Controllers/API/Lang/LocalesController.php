<?php

namespace App\Http\Controllers\API\Lang;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

# models
use App\Models\Lang\Locale;
use App\Models\Lang\Translation;

# request
use App\Http\Requests\Locales\LocaleRequest;

class LocalesController extends Controller
{
    private $locales_map = [
        'en' => ['English', 'الإنجليزية'],
        'ar' => ['Arabic', 'العربية'],
    ];

    #=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

    public function index()
    {
        return response()->json(Locale::all());
    }

    public function show(Locale $locale)
    {
        return response()->json($locale);
    }

    public function store(LocaleRequest $request)
    {
        $locale = Locale::create($request->all());
        return response()->json($locale);
    }

    public function update(LocaleRequest $request, Locale $locale)
    {
        $locale->update($request->all());
        return response()->json($locale);
    }

    public function destroy(Locale $locale)
    {
        $locale->delete();
        return response()->json(null);
    }

    #=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

    // helpers

    private function create_or_update_translation($locale_id, $key, $value)
    {
        if (is_array($value)) {
            foreach ($value as $sub_key => $sub_value) {
                $this->create_or_update_translation($locale_id, $key . '.' . $sub_key, $sub_value);
            }
        } else {
            if ($translation = Translation::where(['locale_id' => $locale_id, 'key' => $key])->first()) {
                $translation->update(['value' => $value]);
            } else {
                Translation::create([
                    'locale_id' => $locale_id,
                    'key' => $key,
                    'value' => $value,
                ]);
            }
        }
    }

    private function import_translations_from_file($filename, $mode = 'backup')
    {
        $label = endsWith($filename, '.json') ? substr($filename, 0, -5) : $filename;

        # creates locale if not exists
        if (!($locale = Locale::where('label', $label)->first())) {
            $locale = Locale::create([
                'name' => localize($this->locales_map[$label]),
                'label' => strToLower($label),
                'dir' => $label == 'ar' ? 'rtl' : 'ltr',
            ]);
        }

        $dir = config("path.locales_$mode");
        $contents = get_contents_array("$dir/$label.json");

        foreach ($contents as $key => $value) {
            $this->create_or_update_translation($locale->id, $key, $value);
        }
    }

    // export json (actions needed)

    public function export($label = null, $mode = 'backup')
    {
        if ($label && !($locale = Locale::where('label', $label)->first()))
            return response()->json("[ERROR] '" . $label . "' is not a valid label", 404);

        $locales = $label ? array($locale) : Locale::all();

        $dir = config("path.locales_$mode");

        foreach ($locales as $locale) {

            $translations = [];

            foreach ($locale->translations as $translation) {
                $translations[$translation->key] = $translation->value;
            }

            put_contents("$dir/{$locale->label}.json", json_encode($translations));
        }

        return response()->json();
    }

    // import json

    public function import($label = null, $mode = 'backup')
    {
        if ($label && !($locale = Locale::where('label', $label)->first())) {
            return response()->json("[ERROR] '" . $label . "' is not a valid label");
        }

        $files = $label ? ["$label.json"] : scandir_clear(base_path(config('path.locales_backup')));

        foreach ($files as $file) {
            $this->import_translations_from_file($file, $mode);
        }

        return response()->json();
    }

    // AT FACTORY_RESET

    public function factory_reset()
    {
        # truncate locales table
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('locales')->truncate();
        DB::table('translations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        # load locales & translations
        foreach (scandir_clear(base_path(config('path.locales_reset'))) as $file) {
            $this->import_translations_from_file($file, 'reset');
        }

        $this->export(null, 'cache');

        return response()->json('Factory reset successfully !');
    }
}
