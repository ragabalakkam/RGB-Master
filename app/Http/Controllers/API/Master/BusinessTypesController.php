<?php

namespace App\Http\Controllers\API\Master;

# controllers
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\Master\FormsController;

# requests
use Illuminate\Http\Request;
use App\Http\Requests\Master\BusinessTypeRequest;
use App\Http\Requests\Master\FormRequest;

# models
use App\Models\Master\Form;
use App\Models\Master\BusinessType;
use App\Models\Apps\App;

class BusinessTypesController extends Controller
{
    public function index()
    {
        return response()->json(BusinessType::all());
    }

    public function store(BusinessTypeRequest $request)
    {
        if (isNull($request->app_id)) {
            $request->merge(['app_id' => App::first()->id]);
        }

        # dining tables (imgs)
        $this->modify_request_imgs($request);
        
        # forms
        $forms = [];
        foreach ($request->forms as $form) {
            $response = app(FormsController::class)->store(new FormRequest($form));
            if ($response->getStatusCode() != 200) {
                foreach ($forms as $form) {
                    $form->delete();
                }
                return $response;
            }
            $forms[] = $response->original;
        }
        $request->merge(['forms' => $forms]);
        Form::whereIn('id', array_map(function ($f) { return $f->id; }, $forms))->delete();

        # database
        $this->add_database_to_request($request);
        
        # create & export business type
        $type = BusinessType::create($request->all());
        $this->export($type);

        return response()->json($type);
    }
    
    public function update(BusinessType $type, BusinessTypeRequest $request)
    {
        # dining tables (imgs)
        $this->modify_request_imgs($request, $type);

        # database
        $this->add_database_to_request($request, $type);

        $type->update($request->all());
        return response()->json($type);
    }

    public function show(BusinessType $type)
    {
        return response()->json($type);
    }

    public function destroy(BusinessType $type)
    {
        # delete zip file if exists
        if ($zip_path = $type->zip_path)
        {
            $zip_path = public_path("storage/$zip_path");
            if (file_exists($zip_path)) unlink($zip_path);

            foreach (['desktop', 'tablet', 'mobile'] as $device)
            {
                if ($src = $type->cashier_settings[$device . '_screenshot'] ?? null)
                {
                    if (file_exists(public_path("storage/$src")))
                    {
                        $dir_to_delete = dirname(public_path("storage/$src"));
                        unlink(public_path("storage/$src"));
                    }
                }
            }

            if (isset($dir_to_delete))
                remove_dir($dir_to_delete);
        }
        
        $type->delete();
        return response()->json();
    }

    //

    public function export(BusinessType $type)
    {
        $filename = generate_random_string(30);

        $dir = storage_path("app/public/business-types/$filename");
        create_dir_if_not_exist($dir);

        # .rgb
        $contents = base64_encode(json_encode($type));
        $filepath = "$dir/.rgb";
        file_put_contents($filepath, $contents);

        # database.sql
        if ($type->database['option'] != 'none' && $type->database['filename'])
        {
            copy(public_path('storage/' . $type->database['filename']), "$dir/database.sql");
        }

        # copy all images used ('/storage/APP' & '/storage/Cashier' & all in `images` table)
        foreach (['idle', 'busy', 'reserved'] as $table_type)
        {
            if ($src = $type->cashier_settings[$table_type . '_img'] ?? null)
            {
                $dest = "$dir/storage/$src";
                create_dir_if_not_exist(dirname($dest));
                copy(public_path("storage/$src"), $dest);
            }
        }
        foreach (['desktop', 'tablet', 'mobile'] as $device)
        {
            if ($src = $type->cashier_settings[$device . '_screenshot'] ?? null)
            {
                $dest = "$dir/storage/$src";
                create_dir_if_not_exist(dirname($dest));
                copy(public_path("storage/$src"), $dest);
            }
        }
        // pending ..

        # compressing files into one zip file
        zip($dir, public_path("storage/business-types/$filename"));
        remove_dir($dir);

        # return zip_path
        $type->update(['zip_path' => "business-types/$filename.zip"]);
        return response()->json($type->zip_path);
    }

    public function import(Request $request)
    {
        $dir = public_path("storage/business-types/" . generate_random_string(30));
        create_dir_if_not_exist($dir);
        extract_zip($request->file->getRealPath(), $dir);

        $contents = json_decode(base64_decode(file_get_contents("$dir/.rgb")), true);

        # images
        if (file_exists("$dir/storage"))
        {
            recursive_copy("$dir/storage", public_path('storage'));
        }

        # database
        $db_file_path = "$dir/database.sql";
        if (file_exists($db_file_path))
        {
            copy($db_file_path, public_path('storage/' . $contents['database']['filename']));
        }

        # new name
        $new_name = json_decode($request->name, true);
        if (!count(array_filter(array_values($new_name), function ($x) { return isNull($x); })))
        {
            $contents['name'] = $new_name;
        }

        remove_dir($dir);
        return $this->store(new BusinessTypeRequest($contents));
    }

    // 

    public function modify_request_imgs(BusinessTypeRequest &$request, BusinessType $business_type = null)
    {
        $cashier_settings = [];

        # cashier tables
        foreach(['idle', 'busy', 'reserved'] as $type) {
            $image = $request->{$type};
            $cashier_settings[$type . '_img'] = is_image($image)
                ? $image->store('Cashier/Custom', 'public')
                : ($image ?? $business_type->cashier_settings[$type . '_img'] ?? "Cashier/DiningTables/$type.png");
        }

        # cashier screenshots on different devices
        foreach(['desktop', 'tablet', 'mobile'] as $device) {
            if ($src = $request->{$device . '_screenshot'} ?? null)
            {
                if (!isNull($src))
                {
                    $src = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $src));
                    $dst = "business-types/screenshots/" . time() . "/$device.png";
                    create_dir_if_not_exist(dirname(public_path("storage/$dst")));
                    file_put_contents(public_path("storage/$dst"), $src);
                }
            }
            $cashier_settings[$device . '_screenshot'] = $dst ?? null;
        }

        return $request->merge(['cashier_settings' => array_merge($request->cashier_settings, $cashier_settings)]);
    }

    public function add_database_to_request(BusinessTypeRequest &$request, BusinessType $business_type = null)
    {
        create_dir_if_not_exist(public_path('storage/databases'));

        $option = $request->database['option'] ?? 'none';
        switch ($option)
        {
            case 'blank':
                $db_filename = 'databases/blank.sql';
                break;
            case 'file':
                if ($request->database_file)
                {
                    $db_filename = 'databases/database' . time() . '.sql';
                    copy($request->database_file->getRealPath(), public_path("storage/$db_filename"));
                }
                break;
            case 'client':
                $db_filename = 'databases/database' . time() . '.sql';
                copy($request->database['file'] ?? public_path('storage/' . $request->database['filename']), public_path("storage/$db_filename"));
                break;
        }

        return $request->merge(['database' => [
            'option'    => $option,
            'filename'  => $option != 'none' ? ($db_filename ?? $request->database['filename'] ?? ($business_type ? $business_type->database['filename'] : null)) : null,
        ]]);
    }
}
