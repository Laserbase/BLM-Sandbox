<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Log;
use Src\BlmFile\BlmFile;

class InitialNonZipTest extends TestCase
{
    protected $fileName = __DIR__.'/../files/141212100024_FBM_2014120711/FBM_2014120711.blm';
    
    public function test_nonZipBlmFile()
    {
        $file = fopen($this->fileName, "r");
        $this->assertTrue( is_resource($file) );

        $blm = new BlmFile();
        $blm->setup($file);
        foreach($blm->readData() as $row) {
            // Log::debug("test=".print_r($row,true));
        }

        Log::debug("===".\basename(__FILE__)."===EXIT===");
    }

}
