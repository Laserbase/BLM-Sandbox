<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Log;
use Src\BlmFile\BlmFile;

class ColumnDefinitionsTest extends TestCase
{
    protected $fileName = __DIR__.'/../files/sanity/201812031105_check_column_definitions.blm';
    
    public function test_columnDefinitionsTest()
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
