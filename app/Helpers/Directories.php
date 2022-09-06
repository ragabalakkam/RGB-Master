<?php

# Directories

function create_dir_if_not_exist($path)
{
  $path = explode('/', forward_slashes($path));
  $full_path = array_shift($path);

  foreach ($path as $val) {
    $full_path .= "/$val";
    if (!is_dir($full_path)) mkdir($full_path);
  }
}

function remove_dir_if_exist($dir)
{
  if (file_exists($dir))
    return remove_dir($dir);

  return false;
}

function remove_dir($dir)
{
  if (!$dir)
    return;

  if (!is_dir($dir))
    throw new InvalidArgumentException("$dir must be a directory");
    
  if (substr($dir, strlen($dir) - 1, 1) != '/')
    $dir .= '/';
    
  $files = scandir_clear($dir);
  foreach ($files as $file) {
    $file = "$dir/$file";
    !is_link($file) && is_dir($file) ? remove_dir($file) : unlink($file);
  }
  
  return rmdir($dir);
}

function remove_link($path)
{
  return file_exists($path) ? (config('app.live') ? unlink($path) : rmdir($path)) : null;
}

function rename_dir($src, $dest)
{
  create_dir_if_not_exist($dest);
  recursive_copy($src, $dest);
  remove_dir($src);
}

function scandir_clear($dir)
{
  return [...array_diff(scandir($dir), ['.', '..'])];
}

function include_if_exists($filepath)
{
  return file_exists($filepath) ? include $filepath : null;
}

function recursive_copy($src, $dst, $except = [], $show_output = false)
{
  $dir = opendir($src);
  create_dir_if_not_exist($dst);

  while ($file = readdir($dir)) {
    $excepted = !!count(array_filter($except, function ($exc) use ($src, $file) { 
      return endsWith("$src/$file", "/$exc"); 
    }));

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


# Zip files

function zip(string $rootPath, string $dest = '', array $exclusions = [])
{
  $dest = endsWith($dest, '.zip') ? $dest : "$dest.zip";
  create_dir_if_not_exist(dirname($dest));
  
  $zip = new ZipArchive();
  $zip->open($dest, ZipArchive::CREATE | ZipArchive::OVERWRITE);

  $files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
  );

  $exclusions = array_map(fn ($f) => forward_slashes("$rootPath/$f"), $exclusions);

  foreach ($files as $name => $file)
  {
    if (!$file->isDir() && !count(array_filter($exclusions, fn ($ex) => startsWith(forward_slashes($file->getRealPath()), $ex))))
    {
      $filePath = forward_slashes($file->getRealPath());
      $relativePath = substr($filePath, strlen($rootPath) + 1);

      $zip->addFile($filePath, $relativePath);
    }
  }

  return $zip->close();
}

function extract_zip($filepath, $destination = null)
{
  if (!$destination)
    $destination = public_path();

  $zip = new \ZipArchive();

  if ($zip->open($filepath) === TRUE)
  {
    $zip->extractTo($destination);
    $zip->close();

    return scandir_clear($destination);
  }

  return false;
}


# Slashes

function handle_duplicate_slashes($route, $slash = '/')
{
  return preg_replace("~$slash+~", $slash, $route);
}

function forward_slashes($route, $postfix = '')
{
  return handle_duplicate_slashes(str_replace('\\', '/', $route . $postfix));
}

function backward_slashes($route, $postfix = '')
{
  return handle_duplicate_slashes(str_replace('/', '\\', $route . $postfix), '\\');
}


# Create file with all missing directories

function put_contents($path, $content)
{
  $full_path = base_path();

  $path = explode('/', forward_slashes($path));
  $filename = array_pop($path);

  foreach ($path as $val) {
    $full_path .= "/$val";
    create_dir_if_not_exist($full_path);
  }

  return file_put_contents("$full_path/$filename", utf8_encode($content));
}


# Get contents of a file in array format

function get_contents_array($path)
{
  $file_path = forward_slashes(base_path($path));
  return json_decode(str_replace('\n', '', file_get_contents($file_path)), true);
}


# get all files / directories / sub-directories

function listDirs($dir)
{
  foreach(glob("$dir/*") as $file){
      $files[] = $file;
      if (is_dir($file)) $files = array_merge($files, listDirs($file) ?? []);
  }
  return $files ?? [];
}

function listFiles($dir)
{
  $files = [];
  foreach(glob("$dir/*") as $file) {
    if (is_dir($file)) $files = array_merge($files, listFiles($file) ?? []);
    else $files[] = $file;
  }
  return $files ?? [];
}