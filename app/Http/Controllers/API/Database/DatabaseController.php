<?php

namespace App\Http\Controllers\API\Database;

use App\Http\Controllers\Controller;

# requests
use Illuminate\Http\Request;

# facades
use Illuminate\Support\Carbon;
use App\Helpers\Classes\MysqlDB;

class DatabaseController extends Controller
{
    public function backups()
    {
        $backups = [];
        $dirs = scandir_clear(public_path("storage/backups"));
        foreach($dirs as $key => $dir) {
            $filename = scandir_clear(public_path("storage/backups/$dir"))[0];
            $filepath = public_path("storage/backups/$dir/$filename");
            $backups[] = [
                'id'         => $key + 1,
                'name'       => substr($filename, 0, -4),
                'path'       => "/storage/backups/$dir/$filename",
                'size'       => filesize($filepath),
                'created_at' => Carbon::createFromTimestamp($dir),
            ];
        }
        return response()->json([...collect($backups)->SortByDesc('created_at')]);
    }
    
    public function client_backups()
    {
        $dir = (config('app.live') ? '/home/rgbksaco/RGB' : 'C:/xampp/htdocs/rgbksa/') . '/FTP/backups';
        create_dir_if_not_exist($dir);
        $client_dirs = scandir_clear($dir);

        foreach($client_dirs as $client_dir)
        {
            if ($latest_backup_dir = collect(scandir_clear("$dir/$client_dir"))->sortByDesc('0')->first());
                $backups[$client_dir] = "$dir/$client_dir/$latest_backup_dir/" . scandir_clear("$dir/$client_dir/$latest_backup_dir")[0];
        }
        
        if (isset($backups))
        {
            $i = 0;
            foreach($backups as $client_dir_name => $filepath) {
                $results[] = [
                    'id'         => ++$i,
                    'name'       => $client_dir_name,
                    'path'       => $filepath,
                    'size'       => filesize($filepath),
                    'created_at' => Carbon::createFromTimestamp($dir),
                ];
            }
        }

        return response()->json([...collect($results ?? [])->SortByDesc('created_at')]);
    }

    public function backup()
    {
        $db = config('database.connections.mysql');
        $db = new MysqlDB($db['database'], $db['username'], $db['password']);
        return response()->json($db->export('*', []));
    }

    public function restore(Request $request)
    {
        $db = config('database.connections.mysql');
        $db = new MysqlDB($db['database'], $db['username'], $db['password']);
        $filepath = forward_slashes(public_path($request->path));
        return response()->json($db->import($filepath));
    }

    public function delete(Request $request)
    {
        $filename = explode('/', $request->filename);
        array_pop($filename);
        return remove_dir(public_path(implode('/', $filename)));
    }

    public function rename(Request $request)
    {
        $dirs = scandir_clear(public_path("storage/backups"));
        $files = array_map(function ($dir) { return scandir_clear(public_path("storage/backups/$dir"))[0]; }, $dirs);

        $dir = $dirs[$request->id - 1];

        if ($index = array_search($request->name . '.sql', $files)) {
            if ($index !== $request->id - 1)
                return response()->json(['errors' => ['name' => ['unique']]], 422);
        }

        return response()->json(rename(
            public_path("storage/backups/$dir/" . $files[$request->id - 1]),
            public_path("storage/backups/$dir/" . $request->name . '.sql'))
        );
    }
}