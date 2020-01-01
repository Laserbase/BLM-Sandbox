<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Log;
use Src\BlmFile\BlmFile;

class ClauddiuTest extends TestCase
{
    protected $fileName = __DIR__.'/../files/Clauddiu/BLM/data/data.blm';
    
    public function test_ClauddiuFile()
    {
        // Log::debug("=== file '".\basename( __FILE__)."', line='".__LINE__."' ");

        $file = fopen($this->fileName, "r");
        $this->assertTrue( is_resource($file) );

        $blm = new BlmFile();
        $blm->setup($file);
        foreach($blm->readData() as $row) {
            // Log::debug("test=".print_r($row,true));
        }

        // Log::debug("=== ".\basename(__FILE__)." ===EXIT===");
    }

}
