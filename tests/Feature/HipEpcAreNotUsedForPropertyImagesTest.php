<?php

namespace Tests\Feature;

// ini_set('memory_limit', 21000000);

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Log;
use Src\BlmFile\BlmFile;

class HipEpcAreNotUsedForPropertyImagesTest extends TestCase
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
        'MEDIA_IMAGE_01' => '999999_FBM2766_IMG_01.jpg',
        'MEDIA_IMAGE_02' => '',

        'MEDIA_IMAGE_59' => '999999_FBM2766_IMG_59.jpg',
        'MEDIA_IMAGE_60' => '999999_FBM2766_IMG_60.jpg',
        'MEDIA_IMAGE_61' => '999999_FBM2766_IMG_61.jpg',

        'MEDIA_IMAGE_TEXT_59' => 'caption',
        'MEDIA_IMAGE_TEXT_60' => 'HIP',
        'MEDIA_IMAGE_TEXT_61' => 'EPC',
    ];

    public function listMediaColumns()
    {
        return [
             // 1st property IMG   - value    - result
             ['MEDIA_IMAGE_TEXT_01',        '', ""],
             ['MEDIA_IMAGE_TEXT_01', 'caption', ""],
             ['MEDIA_IMAGE_TEXT_01', 'HIP'    , "Property image caption 'MEDIA_IMAGE_TEXT_01' must not be in ('HIP', 'EPC'), found 'HIP'"],
             ['MEDIA_IMAGE_TEXT_01', 'EPC'    , "Property image caption 'MEDIA_IMAGE_TEXT_01' must not be in ('HIP', 'EPC'), found 'EPC'"],

             // 2nd property IMG   - value    - result
             ['MEDIA_IMAGE_TEXT_02',        '', ""],
             ['MEDIA_IMAGE_TEXT_02', 'caption', "Media caption column 'MEDIA_IMAGE_TEXT_02' must be empty because media column 'MEDIA_IMAGE_02' is empty, caption passed 'caption'"],
             ['MEDIA_IMAGE_TEXT_02', 'HIP'    , "Media caption column 'MEDIA_IMAGE_TEXT_02' must be empty because media column 'MEDIA_IMAGE_02' is empty, caption passed 'HIP'"],
             ['MEDIA_IMAGE_TEXT_02', 'EPC'    , "Media caption column 'MEDIA_IMAGE_TEXT_02' must be empty because media column 'MEDIA_IMAGE_02' is empty, caption passed 'EPC'"],

             // Missing IMG       - value    - result
             ['MEDIA_IMAGE_TEXT_03',        '', "Media caption column 'MEDIA_IMAGE_TEXT_03' missing media column 'MEDIA_IMAGE_03', caption passed ''"],
             ['MEDIA_IMAGE_TEXT_03', 'caption', "Media caption column 'MEDIA_IMAGE_TEXT_03' missing media column 'MEDIA_IMAGE_03', caption passed 'caption'"],
             ['MEDIA_IMAGE_TEXT_03', 'HIP'    , "Media caption column 'MEDIA_IMAGE_TEXT_03' missing media column 'MEDIA_IMAGE_03', caption passed 'HIP'"],
             ['MEDIA_IMAGE_TEXT_03', 'EPC'    , "Media caption column 'MEDIA_IMAGE_TEXT_03' missing media column 'MEDIA_IMAGE_03', caption passed 'EPC'"],

             // Last property IMG  - value    - result
             ['MEDIA_IMAGE_TEXT_59',        '', ""],
             ['MEDIA_IMAGE_TEXT_59', 'caption', ""],
             ['MEDIA_IMAGE_TEXT_59', 'HIP'    , "Property image caption 'MEDIA_IMAGE_TEXT_59' must not be in ('HIP', 'EPC'), found 'HIP'"],
             ['MEDIA_IMAGE_TEXT_59', 'EPC'    , "Property image caption 'MEDIA_IMAGE_TEXT_59' must not be in ('HIP', 'EPC'), found 'EPC'"],
 
             // 1st certificate   - value    - result
             ['MEDIA_IMAGE_TEXT_60',        '', "Certificate image caption 'MEDIA_IMAGE_TEXT_60' must be in ('HIP', 'EPC'), found ''"],
             ['MEDIA_IMAGE_TEXT_60', 'caption', "Certificate image caption 'MEDIA_IMAGE_TEXT_60' must be in ('HIP', 'EPC'), found 'caption'"],
             ['MEDIA_IMAGE_TEXT_60', 'HIP'    , ""],
             ['MEDIA_IMAGE_TEXT_60', 'EPC'    , ""],
 
             // 2nd certificate   - value    - result
             ['MEDIA_IMAGE_TEXT_61',        '', "Certificate image caption 'MEDIA_IMAGE_TEXT_61' must be in ('HIP', 'EPC'), found ''"],
             ['MEDIA_IMAGE_TEXT_61', 'caption', "Certificate image caption 'MEDIA_IMAGE_TEXT_61' must be in ('HIP', 'EPC'), found 'caption'"],
             ['MEDIA_IMAGE_TEXT_61', 'HIP'    , ""],
             ['MEDIA_IMAGE_TEXT_61', 'EPC'    , ""],

        ];
    }
    
    /**
     * @dataProvider listMediaColumns
     * @test
     */
    public function test_HipEpcAreNotUsedForPropertyImages(String $name, String $value, String $result)
    {
        $requiredColumns = $this->requiredColumns;
        unset($requiredColumns[$name]);

        $requiredColumns[$name] = $value;

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
