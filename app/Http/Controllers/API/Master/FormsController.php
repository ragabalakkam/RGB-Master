<?php

namespace App\Http\Controllers\API\Master;

# controllers
use App\Http\Controllers\Controller;

# requests
use Illuminate\Http\Request;
use App\Http\Requests\Master\FieldRequest;
use App\Http\Requests\Master\FormRequest;

# models
use App\Models\Master\Form;

class FormsController extends Controller
{
    public function index()
    {
        $forms = Form::where('active', true)->get();
        foreach ($forms as $form) {
            $form->fields;
        }
        return response()->json($forms);
    }

    public function store(FormRequest $request)
    {
        $form = Form::create($request->all());
        
        $response = $this->createFields($form, $request->fields);
        if ($response->getStatusCode() != 200) {
            $form->delete();
            return $response;
        }
        
        return $this->show($form);
    }
    
    public function update(Form $form, FormRequest $request)
    {
        $form->update($request->all());
        $old_fields = $form->fields;
        $form->fields()->delete();
        
        $response = $this->createFields($form, $request->fields);
        if ($response->getStatusCode() != 200) {
            $form->fields()->delete();
            $this->createFields($form, $old_fields);
            return $response;
        }

        return $this->show($form);
    }

    public function show(Form $form)
    {
        $form->fields;
        return response()->json($form);
    }

    public function destroy(Form $form)
    {
        $form->delete();
        return response()->json();
    }

    //

    public function createFields(Form $form, array $fields)
    {
        $valid = true;

        foreach ($fields as $field) {
            $response = app('App\Http\Controllers\API\Master\FieldsController')
                ->store(new FieldRequest(array_merge($field, ['form_id' => $form->id])));

            if ($response->getStatusCode() != 200) {
                $valid = false;
                break;
            }
        }

        if (!$valid) return $response;
        return response()->json($form);
    }
}
