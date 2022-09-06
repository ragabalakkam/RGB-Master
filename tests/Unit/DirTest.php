<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Helpers\Classes\Dir;
use Illuminate\Support\Str;

class DirTest extends TestCase
{
    /** @test */
    public function can_normalize_path()
    {
        $dir = "C:/xampp/htdocs/rgbksa/afaqstore/../app/Helpers/Classes/..";
        $out = "C:/xampp/htdocs/rgbksa/app/Helpers";

        $this->assertEquals($out, Dir::normalize_path($dir));
    }

    /** @test */
    public function can_list_dirs()
    {
        $dir = base_path('app');
        $output = Dir::listDirs($dir);

        $count = count(array_unique(array_filter(glob("$dir/{*,*/*,*/*,*/*/*,*/*/*/*,*/*/*/*/*,*/*/*/*/*/*,*/*/*/*/*/*/*}", GLOB_BRACE), fn($d) => is_dir($d))));
        $this->assertEquals($count, count($output));
    }

    /** @test */
    public function can_list_files()
    {
        $dir = base_path('app');
        $output = array_map('forward_slashes', Dir::listFiles($dir));

        $count = count(array_unique(glob("$dir/{*,*/*,*/*,*/*/*,*/*/*/*,*/*/*/*/*,*/*/*/*/*/*,*/*/*/*/*/*/*}.*", GLOB_BRACE)));
        $this->assertEquals($count, count($output));
    }

    /** @test */
    public function can_create_dir()
    {
        $tmp = base_path('../' . Str::random(50));
        $dir = "$tmp/test/dir/to/create/";
        
        $this->assertFileDoesNotExist($dir);
        $this->assertTrue(Dir::create($dir));
        $this->assertFileExists($dir);

        # delete the whole test dir
        Dir::remove($tmp);
        $this->assertFileDoesNotExist($tmp);
    }

    /** @test */
    public function can_remove_dir_with_all_its_subdirs()
    {
        $tmp = base_path('../' . Str::random(50));
        $dir = "$tmp/test/dir/to/create/";
        Dir::create($dir);
        
        $this->assertFileExists($dir);
        $this->assertTrue(Dir::remove($tmp));
        $this->assertFileDoesNotExist($dir);
    }
}
