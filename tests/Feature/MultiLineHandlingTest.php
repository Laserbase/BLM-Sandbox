<?php

namespace Tests\Feature;

// ini_set('memory_limit', 21000000);

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Log;
use Src\BlmFile\BlmFile;

class MultiLineHandlingTest extends TestCase
{
    protected $fileFile = __DIR__.'/../files/sanity/check-multiline-handling.blm';
    
    public function test_multiLineHandling()
    {
        $memoryStart = memory_get_usage();
        $prev = $memoryStart;

        $resource = fopen($this->fileFile, 'r');
        $this->assertTrue( is_resource($resource) );
        
        $blm = new BlmFile();

        $memoryUsage = memory_get_usage() - $prev;
        Log::debug("=== '".\basename(__FILE__)."' memoryUsage='{$memoryUsage}', Line='".__LINE__."' === NEW ===");
        $prev = $memoryUsage;

        $blm->setup($resource);

        $memoryUsage = memory_get_usage() - $prev;
        Log::debug("=== '".\basename(__FILE__)."' memoryUsage='{$memoryUsage}', Line='".__LINE__."' === SETUP ===");
        $prev = $memoryUsage;

        foreach($blm->readData() as $row) {
            $memoryUsage = memory_get_usage() - $prev;
            Log::debug("=== '".\basename(__FILE__)."' memoryUsage='{$memoryUsage}', Line='".__LINE__."' === EACH ===");
            $prev = $memoryUsage;
            // Log::debug("test=".print_r($row['AGENT_REF'],true));
        }

        $memoryUsage = memory_get_usage() - $memoryStart;
        Log::debug("=== '".\basename(__FILE__)."' memoryUsage='{$memoryUsage}', Line='".__LINE__."' === EXIT ===");
    }

}
