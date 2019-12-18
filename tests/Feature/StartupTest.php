<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter as Zip;
use Log;
use Src\BlmFile\BlmFile;

class StartupTest extends TestCase
{
    protected $zip = null;
    protected $zipFile = __DIR__.'/../files/141212100024_FBM_2014120711.zip';

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testInstance()
    {
        // Log::debug("file '".\basename( __FILE__)."', line='".__LINE__."' ");

        $this->zip = new Filesystem(new Zip($this->zipFile));
        $this->assertInstanceOf('League\Flysystem\FileSystem', $this->zip);

        // Log::debug("===".\basename(__FILE__)." === EXIT ===");
    }
    
    public function test_zipHandling()
    {
        // Log::debug("file '".\basename( __FILE__)."', line='".__LINE__."' ");

        // Contents of zip file are not to spec
        $this->expectExceptionMessage("Error: Not a valid BLM file, Data field 'FEATURE1' is too small, minimum length is '1'");

        $this->zip = new Filesystem(new Zip($this->zipFile));
        $this->assertCount(34, $this->zip->listContents());

        $blmFiles = $this->extractBlm();
        $this->checkBlm($blmFiles);

        $imgFiles = $this->extractImg();
        $this->checkImg($imgFiles);

        $this->checkContents($blmFiles);
        // Log::debug("=== ".\basename(__FILE__)." === EXIT ===");
    }

    /**
     * @param \League\Flysystem\Filesystem $zip
     * @return array
     */
    protected function extractBlm() 
    {
        $result = [];
        foreach($this->zip->listContents() as $file) {
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
    protected function extractImg() 
    {
        $result = [];
        foreach($this->zip->listContents() as $file) {
            if (\Str::upper($file['extension']) !== 'BLM') {
                $result[] = $file['basename'];
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

    /**
     * Check all contents of blm files in zip file
     * @param Array $blmFiles blm files found in zip
     * @return void
     */
    protected function checkContents(array $blmFiles)
    {
        foreach ($blmFiles as $blmFile) {
            $this->checkContent($blmFile);
        }
    }

    /**
     * Check the content of blm file in zip file
     * @param Array $blmFile file info array
     * @return void
     */
    protected function checkContent(array $blmFile)
    {
        $filename = $blmFile['basename'];

        $resource = $this->zip->readStream($filename);
        $this->assertTrue( is_resource($resource) );
        
        $blm = new BlmFile();
        $blm->setup($resource);
        foreach($blm->readData() as $row) {
            // Log::debug("test=".print_r($row,true));
        }

        // Log::debug("=== ".\basename(__FILE__)." === EXIT ===");
    }
}
