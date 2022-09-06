<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

# facades
use Illuminate\Support\Facades\File;

class FilesController extends Controller
{
    public function store(Request $request)
    {
        if (!($file = $request->file('file')))
            return response()->json(['errors' => ['file' => 'invalid']]);

        $path = public_path("storage/chunks/{$file->getClientOriginalName()}");
        create_dir_if_not_exist(dirname($path));

        File::append($path, $file->get());

        if ($request->has('is_last') && $request->boolean('is_last')) {
            $name = basename($path, '.part');
            $dest = "storage/large-files/$name";
            create_dir_if_not_exist(dirname($dest));

            $success = File::move($path, $dest);
            return response()->json($success ? ['path' => $dest] : null, $success ? 200 : 422);
        }

        return response()->json(['uploaded' => true]);
    }
}
