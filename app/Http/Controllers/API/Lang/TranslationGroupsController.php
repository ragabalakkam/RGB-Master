<?php

namespace App\Http\Controllers\API\Lang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

# models
use App\Models\Lang\TranslationGroup;

class TranslationGroupsController extends Controller
{
    public function index()
    {
        return response()->json(TranslationGroup::all());
    }

    public function store(Request $request)
    {
        $group = TranslationGroup::create($request->all());
        return response()->json($group);
    }

    public function show(TranslationGroup $group)
    {
        return response()->json($group);
    }

    public function update(Request $request, TranslationGroup $group)
    {
        $group->update($request->all());
        return response()->json($group);
    }

    public function destroy(TranslationGroup $group)
    {
        $group->delete();
        return response()->json(null);
    }
}
