<?php

namespace App\Http\Controllers\API\Lang;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

# models
use App\Models\Lang\Locale;
use App\Models\Lang\Translation;
use App\Models\Lang\TranslationGroup;

# requests
use Illuminate\Http\Request;
use App\Http\Requests\Locales\TranslationRequest;

class TranslationsController extends Controller
{
    public function index()
    {
        $translations = [];
        foreach (Locale::all() as $locale) {
            $translations[$locale->label] = $this->mapper($locale->translations);
        }
        return response()->json($translations, 200);
    }

    public function show($label)
    {
        if ($label == 'all')
            return $this->all();

        $locale = Locale::where('label', $label)->first();
        $translations = $this->mapper($locale->translations);
        $this->update_cache_file($label);
        return response()->json($translations);
    }

    public function store(TranslationRequest $request)
    {
        $translations = ['key' => $request->key, 'values' => []];

        $translation_group = TranslationGroup::find($request->translation_group_id) ?? null;

        foreach ($request->values as $label => $value) {
            $locale = Locale::where('label', $label)->first();
            $translation = Translation::create([
                'locale_id' => $locale->id,
                'key' => ($translation_group ? "$translation_group->name." : '') . $request->key,
                'value' => $value,
                'translation_group_id' => $translation_group->id ?? null,
            ]);
            $translations['values'][$label] = $translation->value;
        }
        $this->update_cache_file();
        return response()->json($translations);
    }

    public function update(TranslationRequest $request, $key)
    {
        $translations = ['key' => $key, 'values' => []];
        foreach ($request->values as $label => $value) {
            $locale = Locale::where('label', $label)->first();
            Translation::where(['key' => $key, 'locale_id' => $locale->id])->update(['value' => $value]);
            $translations['values'][$label] = $value;
        }
        $this->update_cache_file();
        return response()->json($translations);
    }

    public function destroy($key)
    {
        Translation::where('key', $key)->delete();
        $this->update_cache_file();
        return response()->json(null);
    }

    //

    protected function create_translation($locale_id, $key, $value)
    {
        if (is_array($value)) {
            foreach ($value as $sub_key => $sub_value) {
                $this->create_translation($locale_id, $key . '.' . $sub_key, $sub_value);
            }
        } else {
            Translation::create([
                'locale_id' => $locale_id,
                'key' => $key,
                'value' => $value,
            ]);
        }
    }

    protected function mapper($arr, $key = 'key', $value = 'value')
    {
        $results = [];

        foreach ($arr as $item) {
            $results[$item->{$key}] = $item->{$value};
        }

        return $results;
    }

    protected function all()
    {
        $translations = [];
        foreach (Locale::all() as $locale) {
            $translations[$locale->label] = $this->mapper($locale->translations);
        }
        $this->update_cache_file();
        return response()->json($translations);
    }
    
    //

    private function update_cache_file($label = null) {
        app('App\Http\Controllers\API\Lang\LocalesController')->export($label, 'cache');
    }
}
