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

            ['bad_char_EOF_EOR_identical', "EndOfField character '~' must be different than EndOfRecord character '~', defaults '^' and '~'"],
            ['bad_char_EOF_duplicated', "invalid header item 'EOF' failed with value ''^^''"],
            ['bad_char_EOR_duplicated', "invalid header item 'EOR' failed with value ''~~''"],
            ['bad_char_EOF', "EndOfField character 'A' must be a symbol character, default '^'"],
            ['bad_char_EOR', "EndOfRecord character 'A' must be a symbol character, default '~' "],
            ['bad_char_EOF_bad_symbol', "EndOfField character '#' must be a valid symbol, default '^'"],
            ['bad_char_EOR_bad_symbol', "EndOfRecord character '#' must be a valid symbol character, default '~'"],
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

        if ('' !== $result) {
            $this->expectExceptionMessage($result);
        }

        $blm = new BlmFile();
        $blm->setup($file);

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
