<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

# requests
use Illuminate\Http\Request;
use App\Http\Requests\Configurations\ConfigurationRequest;

# models
use App\Models\Configuration;

class ConfigurationsController extends Controller
{
    public function index()
    {
        $configurations = [];
        $keys = [
            'app',
        ];

        foreach ($keys as $key) {
            $configurations[] = getConfigModel($key);
        }

        $configurations = [...array_filter($configurations)];

        $cache_path = config('path.configurations_cache');
        put_contents("$cache_path/configurations.json", json_encode($configurations));

        return response()->json($configurations);
    }

    public function store(ConfigurationRequest $request)
    {
        $configuration = Configuration::create($request->all());
        $this->index();
        return response()->json($configuration);
    }

    public function show($key)
    {
        $configuration = getConfigModel($key);

        if ($configuration)
            return response()->json(base64_encode(is_array($configuration) ? json_encode($configuration) : $configuration));

        return response()->json(null, 404);
    }

    public function update(ConfigurationRequest $request, $key)
    {
        $configuration = getConfigModel($key);

        # preprossing
        // switch ($key) {}

        setConfig($key, $request->value);
        $this->index();

        # postprocessing
        // switch ($key) {}

        return response()->json(getConfigModel($key));
    }

    public function destroy(Configuration $configuration)
    {
        $configuration->delete();
        $this->index();
        return response()->json();
    }
}
