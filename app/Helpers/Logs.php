<?php

function _log($filename, $contents = [])
{
    $log_file_path = public_path("storage/logs/$filename.log");
    create_dir_if_not_exist(dirname($log_file_path));

    $old_contents = file_exists($log_file_path) ? file_get_contents($log_file_path) : '';
    return file_put_contents($log_file_path, $old_contents . implode("\r\n", [...$contents, str_repeat('-', 125), '']));
}