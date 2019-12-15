<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Log;
use Src\BlmFile\BlmFile;

class ColumnDefinitionsTest extends TestCase
{
    protected $fileName = __DIR__.'/../files/sanity/201812031105_check_column_definitions.blm';
    protected $blm = null;
    
    public function listColumnDefinitions()
    {
        $result = [];

        $blm = new BlmFile();
        $columns = $blm->selectVersionColumnDefinitions();
        foreach($columns as $columnName => $definition) {
            $result[$columnName] = [$columnName, $definition];
        }

        return $result;
    }
    
    public function test_columnDefinitionsTest()
    {
        // Log::debug("=== ".\basename(__FILE__)." ===");

        $file = fopen($this->fileName, "r");
        $this->assertTrue( is_resource($file) );

        $blm = new BlmFile();
        $blm->setup($file);

        foreach($blm->readData() as $row) {
            // Log::debug("test=".print_r($row,true));
        }

        fclose($file);
        $this->assertFalse( is_resource($file) );

        // Log::debug("=== ".\basename(__FILE__)." ===EXIT===");
    }

    /**
     * @dataProvider listColumnDefinitions
     * @test
     */
    public function test_allColumnDefinitionsAreInspected($columnName, $columnDefinition)
    {
        // Log::debug("=== Test All Column Definitions Are Inspected - '{$columnName}' ===");

        $fileName = __DIR__.'/../files/column_definitions/check-'.$columnName;
        if ($columnDefinition['recursive']) {
            $fileName .= '_01';
        }
        $fileName .= '.blm';

        $file = fopen($fileName, "r");
        $this->assertTrue( is_resource($file) );
        
        if ($columnDefinition['required']) {
            $this->expectExceptionMessage("Mandatory column(s) '{$columnName}' missing");
        }

        $blm = new BlmFile();
        $blm->setup($file);

        fclose($file);
        $this->assertFalse( is_resource($file) );

        // Log::debug("=== EXIT - '{$columnName}' ===");
    }

}
