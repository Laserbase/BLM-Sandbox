<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
// use 
use Tests\TestCase;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter as Adapter;
use \Log;

class StartupTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_zip_test()
    {        
        $zip = new Filesystem(new Adapter(__DIR__.'/../files/141212100024_FBM_2014120711.zip'));
        $this->assertCount(34, $zip->listContents());
        // dd($zip);
        // $this->assertEquals([], $zip);

        $blmFiles = $this->extractBlm($zip);
        $imgFiles = $this->extractImg($zip);

        $this->checkBlm($blmFiles);
        $this->checkImg($imgFiles);
        
        // $this->assertEquals([], $contents);

        // $this->assertEquals('', $contents);
        // dd($contents);
    }

    /**
     * @param \League\Flysystem\Filesystem $zip
     * @return array
     */
    protected function extractBlm(\League\Flysystem\Filesystem $zip) 
    {
        $result = [];
        foreach($zip->listContents() as $file) {
            if (\Str::upper($file['extension']) === 'BLM') {
                $result[] = $file;
            }
        }

        return $result;
    }

    /**
     * @param \League\Flysystem\Filesystem $zip
     * @return array
     */
    protected function extractImg(\League\Flysystem\Filesystem $zip) 
    {
        $result = [];
        foreach($zip->listContents() as $file) {
            if (\Str::upper($file['extension']) !== 'BLM') {
                $result[] = $file['basename'];
                // array_push($result, $file['basename']);
            }
        }

        return $result;
    }

    /**
     * @param Array $blmFiles
     * @return void
     */
    protected function checkBlm(array $blmFiles)
    {
        $blmTest[] = [
            "type" => "file",
            "size" => 1060632,
            "timestamp" => 1417950102,
            "path" => "FBM_2014120711.blm",
            "dirname" => "",
            "basename" => "FBM_2014120711.blm",
            "extension" => "blm",
            "filename" => "FBM_2014120711"
        ];

        $this->assertEquals($blmTest, $blmFiles);
    }

    /**
     * @param Array $imgFiles
     * @return void
     */
    protected function checkImg(array $imgFiles)
    {

        $imgTest = [
            "FBM_FBM4118_FLP_00.GIF",
            "FBM_FBM4118_IMG_00.JPG",
            "FBM_FBM4118_IMG_01.JPG",
            "FBM_FBM4118_IMG_02.JPG",
            "FBM_FBM4118_IMG_03.JPG",
            "FBM_FBM4118_IMG_04.JPG",
            "FBM_FBM4118_IMG_05.JPG",
            "FBM_FBM4118_IMG_06.JPG",
            "FBM_FBM4118_IMG_07.JPG",
            "FBM_FBM4118_IMG_08.JPG",
            "FBM_FBM4118_IMG_09.JPG",

            "FBM_FBM4124_FLP_00.GIF",
            "FBM_FBM4124_IMG_00.JPG",
            "FBM_FBM4124_IMG_01.JPG",
            "FBM_FBM4124_IMG_02.JPG",
            "FBM_FBM4124_IMG_03.JPG",
            "FBM_FBM4124_IMG_04.JPG",
            "FBM_FBM4124_IMG_05.JPG",
            "FBM_FBM4124_IMG_06.JPG",
            "FBM_FBM4124_IMG_07.JPG",
            "FBM_FBM4124_IMG_08.JPG",
            "FBM_FBM4124_IMG_09.JPG",

            "FBM_FBM5121_FLP_00.GIF",
            "FBM_FBM5121_IMG_00.JPG",
            "FBM_FBM5121_IMG_01.JPG",
            "FBM_FBM5121_IMG_02.JPG",
            "FBM_FBM5121_IMG_03.JPG",
            "FBM_FBM5121_IMG_04.JPG",
            "FBM_FBM5121_IMG_05.JPG",
            "FBM_FBM5121_IMG_06.JPG",
            "FBM_FBM5121_IMG_07.JPG",
            "FBM_FBM5121_IMG_08.JPG",
            "FBM_FBM5121_IMG_09.JPG",
        ];

        $this->assertEquals($imgTest, $imgFiles);        
    }
}
