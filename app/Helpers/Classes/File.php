<?php

namespace App\Helpers\Classes;

use App\Helpers\Classes\MysqlDB;
use Illuminate\Support\Str;

class File
{
    // Encrypt / Decrypt

    private static $cypher = 'AES-128-CTR';
    private static $iiv = '1234567890123456';
    private static $encryption_key = 'RGB@2022/afaqrgb2000';

    public static function encrypt($contents)
    {
        return \openssl_encrypt($contents, self::$cypher, self::$encryption_key, 0, self::$iiv);
    }

    public static function decrypt($contents)
    {
        return \openssl_decrypt($contents, self::$cypher, self::$encryption_key, 0, self::$iiv);
    }


    // Logs

    public static function log($message)
    {
        $user = auth()->user()->name ?? 'anonymous user';
        $date = date('Y-m-d H:i:s');

        return _log('files', ["[$date] $user - $message"]);
    }


    // Checks

    public static function checkIfExists(string $path)
    {
        if (!file_exists($path))
            throw new \Exception("File/Directory $path does not exist.");

        return true;
    }


    // Zip

    public static function extract(string $filepath, string $dest)
    {
        self::checkIfExists($filepath);
        file_put_contents($filepath, self::read($filepath));

        if (!($extracted = extract_zip($filepath, $dest)))
            throw new \Exception("Unable to unzip file $filepath");

        return $extracted;
    }

    public static function compress(string $dir, string $name, array $excludes = [])
    {
        $zip_path = dirname($dir) . "/" . Str::slug(parseName(getConfig('name'), 'en') . "-$name") . ".zip";
        
        self::checkIfExists($dir);
        zip($dir, $zip_path, $excludes);
        self::write($zip_path, file_get_contents($zip_path));
        remove_dir_if_exist($dir);
        
        return substr($zip_path, strlen(public_path('storage/')));;
    }


    // Read/Write

    public static function read(string $filepath)
    {
        self::checkIfExists($filepath);
        $contents = file_get_contents($filepath);
        self::delete($filepath);
        return self::decrypt($contents);
    }

    public static function write(string $filepath, string $contents)
    {
        return file_put_contents($filepath, self::encrypt($contents));
    }

    public static function copy(string $src, string $dest)
    {
        return file_exists($src) ? x_copy($src, $dest) : null;
    }

    public static function delete(string $filepath)
    {
        self::checkIfExists($filepath);
        unlink($filepath);
    }


    // Special reads

    public static function readJson(string $filepath)
    {
        return json_decode(self::read($filepath), true);
    }

    public static function importSql(string $filepath)
    {
        self::checkIfExists($filepath);
        file_put_contents($filepath, File::read($filepath));
        $output = (new MysqlDB)->import($filepath);
        unlink($filepath);
        return $output;
    }

    public static function importDirToStorage(string $dir)
    {
        $dir = "$dir/storage";
        if (file_exists($dir)) {
            recursive_copy($dir, public_path('storage'));
            remove_dir($dir);
        }
    }


    // Special writes

    public static function writeJson(string $filepath, $contents)
    {
        return self::write($filepath, json_encode($contents));
    }

    public static function exportSql(string $filepath, array $tables)
    {
        $output = (new MysqlDB())->export(implode(',', $tables), [], $filepath);
        File::write($filepath, file_get_contents($filepath));
        return $output;
    }
}
