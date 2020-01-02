<?php

namespace Tests\Feature;

// ini_set('memory_limit', 21000000);

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Log;
use Src\BlmFile\BlmFile;

class HipErpCertificatesAreUsedCorrectlyTest extends TestCase
{
    protected $columnKeys = '';
    protected $columnData = '';

    // Required Columns
    private $requiredColumns = [ 
        'AGENT_REF' => '999999_FBM2766',
        'BRANCH_ID' => '999999',
        'STATUS_ID' => '1',
        'CREATE_DATE' => '2019-12-17 15:49:30',
        'UPDATE_DATE' => '2019-12-17 15:49:30',
        'DISPLAY_ADDRESS' => 'SZ ADF TestingEstate Agency. ZG Test (ML), Snowdon Drive, Winterhill, Milton Keynes, MK6 1AJ',
        'PUBLISHED_FLAG' => '1',
        'LET_FURN_ID' => '',
        'LET_RENT_FREQUENCY' => '',
        'TRANS_TYPE_ID' => '1',
        'BEDROOMS' => '4',
        'PRICE' => '250000',
        'PRICE_QUALIFIER' => '2', // 2 – Guide Price,
        'PROP_SUB_ID' => '0',
        'ADDRESS_1' => 'SZ ADF TestingEstate Agency. ZG Test (ML)',
        'ADDRESS_2' => 'Snowdon Drive, Winterhill',
        'TOWN' => 'Milton Keynes',
        'POSTCODE1' => 'MK6',
        'POSTCODE2' => '1AJ',
        'FEATURE1' => 'House',
        'FEATURE2' => 'Garden',
        'FEATURE3' => 'Lake',
        'LET_TYPE_ID' => '0', // 0 = not specified DEFAULT
        'SUMMARY' => 'whatever whatever whatever whatever',
        'DESCRIPTION' => 'whatever whatever whatever whatever whatever whatever',
        'NEW_HOME_FLAG' => 'N',

        'MEDIA_IMAGE_00' => '999999_FBM2766_IMG_00.jpg',

        'MEDIA_IMAGE_60' => '999999_FBM2766_IMG_60.jpg',
        'MEDIA_IMAGE_TEXT_60' => 'EPC',

        'MEDIA_DOCUMENT_50' => '999999_FBM2766_DOC_50.pdf',
        'MEDIA_DOCUMENT_TEXT_50' => 'HIP',
    ];

    public function listMediaColumns()
    {
        return [
            // images

            ['MEDIA_IMAGE_TEXT_60',    '', 'required', "Certificate image caption 'MEDIA_IMAGE_TEXT_60' must be in ('HIP', 'EPC'), found ''"],
            ['MEDIA_IMAGE_TEXT_60',    '',    'unset', "Media caption column 'MEDIA_IMAGE_TEXT_60' missing media column 'MEDIA_IMAGE_60', caption passed ''"],
            ['MEDIA_IMAGE_TEXT_60',    '',         '', ""],

            ['MEDIA_IMAGE_TEXT_60', 'HIP', 'required', ""],
            ['MEDIA_IMAGE_TEXT_60', 'HIP',    'unset', "Media caption column 'MEDIA_IMAGE_TEXT_60' missing media column 'MEDIA_IMAGE_60', caption passed 'HIP'"],
            ['MEDIA_IMAGE_TEXT_60', 'HIP',         '', "Media caption column 'MEDIA_IMAGE_TEXT_60' must be empty because media column 'MEDIA_IMAGE_60' is empty, caption passed 'HIP'"],

            ['MEDIA_IMAGE_TEXT_60', 'EPC', 'required', ""],
            ['MEDIA_IMAGE_TEXT_60', 'EPC',    'unset', "Media caption column 'MEDIA_IMAGE_TEXT_60' missing media column 'MEDIA_IMAGE_60', caption passed 'EPC'"],
            ['MEDIA_IMAGE_TEXT_60', 'EPC',         '', "Media caption column 'MEDIA_IMAGE_TEXT_60' must be empty because media column 'MEDIA_IMAGE_60' is empty, caption passed 'EPC'"],
 
            ['MEDIA_IMAGE_TEXT_60', 'ERR', 'required', "Certificate image caption 'MEDIA_IMAGE_TEXT_60' must be in ('HIP', 'EPC'), found 'ERR'"],
            ['MEDIA_IMAGE_TEXT_60', 'ERR',    'unset', "Media caption column 'MEDIA_IMAGE_TEXT_60' missing media column 'MEDIA_IMAGE_60', caption passed 'ERR'"],
            ['MEDIA_IMAGE_TEXT_60', 'ERR',         '', "Media caption column 'MEDIA_IMAGE_TEXT_60' must be empty because media column 'MEDIA_IMAGE_60' is empty, caption passed 'ERR'"],

            // certificates
            ['MEDIA_DOCUMENT_TEXT_50',    '', 'required', "Data field 'MEDIA_DOCUMENT_50' HIP/EPC Certificate caption 'MEDIA_DOCUMENT_TEXT_50' must be in ('HIP', 'EPC')"],
            ['MEDIA_DOCUMENT_TEXT_50',    '',    'unset', "Media caption column 'MEDIA_DOCUMENT_TEXT_50' missing media column 'MEDIA_DOCUMENT_50', caption passed ''"],
            ['MEDIA_DOCUMENT_TEXT_50',    '',         '', ""],

            ['MEDIA_DOCUMENT_TEXT_50', 'HIP', 'required', ""],
            ['MEDIA_DOCUMENT_TEXT_50', 'HIP',    'unset', " Media caption column 'MEDIA_DOCUMENT_TEXT_50' missing media column 'MEDIA_DOCUMENT_50', caption passed 'HIP'"],
            ['MEDIA_DOCUMENT_TEXT_50', 'HIP',         '', "Media caption column 'MEDIA_DOCUMENT_TEXT_50' must be empty because media column 'MEDIA_DOCUMENT_50' is empty, caption passed 'HIP'"],

            ['MEDIA_DOCUMENT_TEXT_50', 'EPC', 'required', ""],
            ['MEDIA_DOCUMENT_TEXT_50', 'EPC',    'unset', "Media caption column 'MEDIA_DOCUMENT_TEXT_50' missing media column 'MEDIA_DOCUMENT_50', caption passed 'EPC'"],
            ['MEDIA_DOCUMENT_TEXT_50', 'EPC',         '', "Media caption column 'MEDIA_DOCUMENT_TEXT_50' must be empty because media column 'MEDIA_DOCUMENT_50' is empty, caption passed 'EPC'"],

            ['MEDIA_DOCUMENT_TEXT_50', 'ERR', 'required', "HIP/EPC Certificate caption 'MEDIA_DOCUMENT_TEXT_50' must be in 'HIP', 'EPC', found 'ERR'"],
            ['MEDIA_DOCUMENT_TEXT_50', 'ERR',    'unset', "Media caption column 'MEDIA_DOCUMENT_TEXT_50' missing media column 'MEDIA_DOCUMENT_50', caption passed 'ERR'"],
            ['MEDIA_DOCUMENT_TEXT_50', 'ERR',         '', "Media caption column 'MEDIA_DOCUMENT_TEXT_50' must be empty because media column 'MEDIA_DOCUMENT_50' is empty, caption passed 'ERR'"],
             
        ];
    }
    
    /**
     * @dataProvider listMediaColumns
     * @test
     */
    public function test_HipErpCertificatesAreUsedCorrectlyTest(String $name, String $value, String $required, String $result)
    {
        $mediaColumn = str_replace('_TEXT', '', $name);

        $requiredColumns = $this->requiredColumns;
        $requiredColumns[$name] = $value;

        switch ($required) {
            case 'unset':
                unset($requiredColumns[$mediaColumn]);
            break;

            case 'required':
                // SKIP
            break;

            case '':
            default:
                $requiredColumns[$mediaColumn] = '';
            break;
        }

        if ($result === 'AAAjjj') {
            dd($name, $value, $requiredColumns, $mediaColumn);
        }

        $columnKeys = implode('^', array_keys($requiredColumns)).'^~';
        $columnData = implode('^', array_values($requiredColumns)).'^~';

        $blm = new BlmFile();
        $blm->Version = '3';

        $blm->selectVersionColumnDefinitions('3');

        $blm->validateDefinition($columnKeys);

        if ($result) {
            $this->expectExceptionMessage($result);
        }

        $row = $blm->validateData($columnData);
        if ('' === $result) {
            $this->assertEquals($row['AGENT_REF'], '999999_FBM2766');
        }
    
    }

}
