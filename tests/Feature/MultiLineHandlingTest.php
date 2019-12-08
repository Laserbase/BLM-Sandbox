<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Log;
use Src\BlmFile\BlmFile;

class MultiLineHandlingTest extends TestCase
{
    protected $fileFile = __DIR__.'/../files/sanity/check-multiline-handling.blm';
    
    public function test_multiLineHandling()
    {
        // Log::debug("file '".\basename( __FILE__)."', line='".__LINE__."' ");

        $resource = fopen($this->fileFile, 'r');
        $this->assertTrue( is_resource($resource) );
        
        $blm = new BlmFile();
        $blm->setup($resource);
        foreach($blm->readData() as $row) {
            // Log::debug("test=".print_r($row,true));
        }

        // Log::debug("=== ".\basename(__FILE__)." === EXIT ===");
    }

}
