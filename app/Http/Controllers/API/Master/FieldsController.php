<?php

namespace App\Http\Controllers\API\Master;

# controllers
use App\Http\Controllers\Controller;

# requests
use Illuminate\Http\Request;
use App\Http\Requests\Master\FieldRequest;

# models
use App\Models\Master\Field;

class FieldsController extends Controller
{
    public function index()
    {
        return response()->json(Field::all());
    }

    public function store(FieldRequest $request)
    {
        $field = Field::create($request->all());
        return response()->json($field);
    }
    
    public function update(Field $field, FieldRequest $request)
    {
        $field->update($request->all());
        return response()->json($field);
    }

    public function show(Field $field)
    {
        return response()->json($field);
    }

    public function destroy(Field $field)
    {
        $field->delete();
        return response()->json();
    }
}
