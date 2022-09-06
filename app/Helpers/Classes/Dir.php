<?php

namespace App\Helpers\Classes;

use InvalidArgumentException;

class Dir
{
	public static function normalize_path($dir)
	{
		$dir = explode('/', forward_slashes($dir));

		while ($index = array_search('..', $dir)) {
			$dir = [...array_slice($dir, 0, $index - 1), ...array_slice($dir, $index + 1)];
		}

		return implode('/', $dir);
	}

	public static function listDirs($dir)
	{
		foreach (glob("$dir/*") as $dir) {
			if (is_dir($dir)) {
				$dirs[] = $dir;
				$dirs = [...$dirs, ...self::listDirs($dir)];
			}
		}
		return $dirs ?? [];
	}

	public static function listFiles($dir)
	{
		foreach (glob("$dir/*") as $file) {
			if (is_dir($file)) $files = [...$files ?? [], ...self::listFiles($file)];
			else $files[] = $file;
		}
		return $files ?? [];
	}

	public static function create($path)
	{
		$path = explode('/', forward_slashes($path));
		$full_path = array_shift($path);

		foreach ($path as $val) {
			$full_path .= "/$val";
			if (!is_dir($full_path)) mkdir($full_path);
		}

		return true;
	}

	public static function remove($dir)
	{
		if (!file_exists($dir))
			return false;

		if (!$dir)
			return;

		if (!is_dir($dir))
			throw new InvalidArgumentException("$dir must be a directory");

		if (substr($dir, strlen($dir) - 1, 1) != '/')
			$dir .= '/';

		$files = scandir_clear($dir);
		foreach ($files as $file) {
			$file = "$dir/$file";
			!is_link($file) && is_dir($file) ? self::remove($file) : unlink($file);
		}

		return rmdir($dir);
	}

	public static function copy($src, $dst, $except = [], $show_output = false)
	{
		$dir = opendir($src);
		create_dir_if_not_exist($dst);

		while ($file = readdir($dir)) {
			$excepted = !!count(array_filter($except, fn ($exc) => endsWith("$src/$file", "/$exc")));

			if (!$excepted && !in_array($file, ['.', '..'])) {
				if (is_dir($src . '/' . $file)) {
					recursive_copy($src . '/' . $file, $dst . '/' . $file, $except, $show_output);
				} else {
					copy($src . '/' . $file, $dst . '/' . $file);
					if ($show_output) echo "copied : $src/$file\r\n";
				}
			} else if ($excepted) {
				if ($show_output) echo "excepted : $src/$file\r\n";
			}
		}

		closedir($dir);
	}
}
