<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Log;
use Src\BlmFile\BlmFile;

class HeaderItemsTest extends TestCase
{    
    public function listHeaderFiles()
    {
        return [
            ['missing_EOF', ""],
            ['missing_EOR', ""],
            ['missing_optional_items', ""],
            ['missing_version', ""],
            ['missing_agent_item', "Error: Unknown Blm variable 'Agent Specific Field'"],
            ['with_agent_item', ""],
        ];
    }

    /**
     * @dataProvider listHeaderFiles
     * @test
     */
    public function test_headerItems($fileName, $result)
    {
        $file = fopen(__DIR__.'/../files/sanity/201812031105_check_header_'.$fileName.'.blm', "r");
        $this->assertTrue( is_resource($file) );

        $blm = new BlmFile();
        $blm->setup($file);

        if ('' !== $result) {
            $this->expectExceptionMessage($result);
        }

        switch ($fileName) {
            case 'missing_agent_item':
            case 'with_agent_item':
                $this->assertEquals($blm->{'Agent Specific Field'}, "whatever");
            break;

            default:
                $this->assertEquals($blm->Version, "3");
            break;
        }

        fclose($file);
        $this->assertFalse( is_resource($file) );
    }
}
