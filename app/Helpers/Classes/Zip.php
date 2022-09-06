<?php

namespace App\Helpers\Classes;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use App\Helpers\Classes\Dir;
use ZipArchive;
use Exception;

class Zip
{
	/**
	 * Compress @directory into @destination .zip file
	 */

	public static function compress(string $directory, string $filename = null, array $exclusions = []): bool
	{
		# check that directory already exists
		if (!file_exists($directory)) {
			throw new Exception("Directory \"$directory\" does not exist");
		}

		# prepare directory
		$directory = Dir::normalize_path($directory);

		# prepare filename
		$filename = $filename ? (endsWith($filename, '.zip') ? $filename : "$filename.zip") : "$directory.zip";
		Dir::create(dirname($filename));

		# prepare exclusions
		$exclusions = array_map(fn ($file) => forward_slashes("$directory/$file"), $exclusions);

		# instantiate zip
		$zip = new ZipArchive();
		$zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);

		# get iterable files
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($directory),
			RecursiveIteratorIterator::LEAVES_ONLY
		);

		# compress
		foreach ($files as $name => $file) {
			$real_path = forward_slashes($file->getRealPath());
			if ($real_path && !$file->isDir() && !count(array_filter($exclusions, fn ($ex) => startsWith($real_path, $ex)))) {
				$relativePath = substr(forward_slashes($name), strlen($directory) + 1);
				$zip->addFile($real_path, $relativePath);
			}
		}

		return $zip->close();
	}

	public static function compressThenDelete(string $directory, string $destination = null, array $exclusions = []): bool
	{
		$result = self::compress($directory, $destination, $exclusions);
		remove_dir($directory);
		return $result;
	}


	/**
	 * List .zip file contents without extraction
	 */

	public static function listContents(string $filename): array
	{
		$zip = new ZipArchive();
		$zip->open($filename);

		for ($i = 0; $i < $zip->numFiles; $i++) {
			$stat = $zip->statIndex($i);
			$contents[] = $stat['name'];
		}

		return $contents ?? [];
	}


	/**
	 * Extract .zip @filename to @destination directory
	 */

	public static function extract(string $filepath, string $destination = null): ?array
	{
		$destination = $destination ?? public_path();
		Dir::create($destination);

		$zip = new ZipArchive();

		if ($zip->open($filepath) === TRUE) {
			$zip->extractTo($destination);
			$zip->close();
			return Dir::listFiles($destination);
		}

		return null;
	}

	public static function extractThenDelete(string $filepath, string $destination = null): ?array
	{
		$result = self::extract($filepath, $destination);
		unlink($filepath);
		return $result;
	}
}
