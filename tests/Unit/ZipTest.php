<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Helpers\Classes\Zip;
use App\Helpers\Classes\Dir;

class ZipTest extends TestCase
{
    private function getVars()
    {
        $zip = base_path('../test.zip');
        $dir = base_path('../app');
        $exc = [
            'Console',
        ];

        # copy test files to a new dir that lays out of project scope
        recursive_copy(str_replace('../', '', $dir), $dir);

        return [$zip, $dir, $exc];
    }

    private function removeTestFiles()
    {
        [$zip, $dir] = $this->getVars();

        if (file_exists($zip))
            unlink($zip);

        if (file_exists("$dir.zip"))
            unlink("$dir.zip");

        if (file_exists($dir))
            Dir::remove($dir);
    }

    /** @test */
    public function can_compress_files_to_null_filename()
    {
        [$zip, $dir] = $this->getVars();
        $zip = null;

        # correctly compress $dir into $zip
        $result = Zip::compress($dir, $zip);
        $zip = "$dir.zip";
        $this->assertTrue($result);

        # zip & dir exist (Not deleted)
        $this->assertFileExists($zip);
        $this->assertFileExists($dir);

        # files count are exactly the same
        $this->assertEquals(count(Zip::listContents($zip)), count(Dir::listFiles($dir)));

        $this->removeTestFiles();
    }

    /** @test */
    public function can_compress_files_to_given_filename()
    {
        [$zip, $dir] = $this->getVars();

        # correctly compress $dir into $zip
        $result = Zip::compress($dir, $zip);
        $this->assertTrue($result);

        # zip & dir exist (Not deleted)
        $this->assertFileExists($zip);
        $this->assertFileExists($dir);

        # files count are exactly the same
        $this->assertEquals(count(Zip::listContents($zip)), count(Dir::listFiles($dir)));

        $this->removeTestFiles();
    }

    /** @test */
    public function can_compress_files_with_exclusions()
    {
        [$zip, $dir, $exc] = $this->getVars();

        # correctly compress $dir into $zip
        $result = Zip::compress($dir, $zip, $exc);
        $this->assertTrue($result);
        $this->assertFileExists($zip);
        $this->assertFileExists($dir);

        # make srue that exclusions are not included
        $contents = Zip::listContents($zip);
        $excludes = array_unique(array_reduce($exc, fn ($acc, $cur) => [...$acc, ...glob("$dir/$cur{,/*,/*,/*/*,/*/*/*}.*", GLOB_BRACE)], []));
        $this->assertEquals(count(Zip::listContents($zip)), count(array_unique(glob("$dir/{*,*/*,*/*/*,*/*/*/*,*/*/*/*/*}.*", GLOB_BRACE))) - count($excludes));
        foreach ($exc as $file) {
            $this->assertArrayNotHasKey("$dir/$file", $contents);
        }

        $this->removeTestFiles();
    }

    /** @test */
    public function can_compress_files_then_delete_them()
    {
        [$zip, $dir] = $this->getVars();

        Zip::compressThenDelete($dir, $zip);
        $this->assertFileExists($zip);
        $this->assertFileDoesNotExist($dir);

        $this->removeTestFiles();
    }

    /** @test */
    public function can_list_contents()
    {
        [$zip, $dir, $exc] = $this->getVars();
        $output_dir = "$dir-output";

        Zip::compress($dir, $zip, $exc);

        $extracted_files = Zip::extract($zip, $output_dir);
        $contents_list = Zip::listContents($zip);

        # files are all forward-slashed
        $files_that_contain_backward_slashes = array_filter($contents_list, fn ($f) => str_contains($f, '\\'));
        $this->assertEquals([], $files_that_contain_backward_slashes);

        # extracted files are the same as listed
        $extracted_files_arr = array_map(fn ($f) => forward_slashes("$output_dir/$f"), $contents_list);
        $listed_files_arr = array_map('forward_slashes', $extracted_files);
        $this->assertEquals([], array_diff($extracted_files_arr, $listed_files_arr));

        # differences between source dir files and listed files produces the excluded files
        $src_dir_files = array_map(fn ($f) => str_replace("$dir/", '', $f), Dir::listFiles($dir));
        $dst_dir_files = array_map(fn ($f) => str_replace("$output_dir/", '', $f), Dir::listFiles($output_dir));
        $exclusions = array_reduce($exc, fn ($acc, $cur) => [...$acc, ...(is_dir("$dir/$cur") ? array_map(fn ($x) => str_replace("$dir/", '', $x), Dir::listFiles("$dir/$cur")) : [$cur])], []);
        $this->assertEquals($exclusions, array_diff($src_dir_files, $dst_dir_files));

        $this->removeTestFiles();
        Dir::remove($output_dir);
    }

    /** @test */
    public function can_extract_files()
    {
        [$zip, $dir] = $this->getVars();

        # extract zip that doesn't exist
        $result = Zip::extract($zip, $dir);
        $this->assertNull($result);

        # extract zip
        Zip::compress($dir, $zip);
        $result = Zip::extract($zip, $dir);
        $this->assertIsArray($result);

        $this->removeTestFiles();
    }

    /** @test */
    public function can_extract_files_then_delete_zip_file()
    {
        [$zip, $dir, $exc] = $this->getVars();
        Zip::compress($dir, $zip, $exc);

        Zip::extractThenDelete($zip, $dir);
        $this->assertFileExists($dir);
        $this->assertFileDoesNotExist($zip);

        $this->removeTestFiles();
    }
}
