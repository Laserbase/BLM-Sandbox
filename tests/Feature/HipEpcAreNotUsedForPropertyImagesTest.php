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
        'AGENT_REF' => 'XX99XX_FBM2766',
        'BRANCH_ID' => 'XX99XX',
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
        'PRICE_QUALIFIER' => '',
        'PROP_SUB_ID' => '0',
        'ADDRESS_1' => 'SZ ADF TestingEstate Agency. ZG Test (ML)',
        'ADDRESS_2' => 'Snowdon Drive, Winterhill',
        'TOWN' => 'Milton Keynes',
        'POSTCODE1' => 'MK6',
        'POSTCODE2' => '1AJ',
        'FEATURE1' => 'House',
        'FEATURE2' => 'Garden',
        'FEATURE3' => 'Lake',
        'SUMMARY' => 'whatever whatever whatever whatever',
        'DESCRIPTION' => 'whatever whatever whatever whatever whatever whatever',
        'NEW_HOME_FLAG' => '0',
        'MEDIA_IMAGE_00' => 'XX99XX_FBM2766_IMG_00.jpg',

        'MEDIA_IMAGE_59' => 'XX99XX_FBM2766_IMG_59.jpg',
        'MEDIA_IMAGE_60' => 'XX99XX_FBM2766_IMG_60.jpg',
        'MEDIA_IMAGE_61' => 'XX99XX_FBM2766_IMG_61.jpg',

        'MEDIA_IMAGE_TEXT_59' => 'caption',
        'MEDIA_IMAGE_TEXT_60' => 'HIP',
        'MEDIA_IMAGE_TEXT_61' => 'EPC',
    ];

    public function listMediaColumns()
    {
        return [
             // IMG name          - value                      - result
             ['MEDIA_IMAGE_TEXT_59', 'caption', ""],
             ['MEDIA_IMAGE_TEXT_59', 'HIP'    , "Property image caption 'MEDIA_IMAGE_TEXT_59' must not be 'HIP' or 'EPC', found 'HIP'"],
             ['MEDIA_IMAGE_TEXT_59', 'EPC'    , "Property image caption 'MEDIA_IMAGE_TEXT_59' must not be 'HIP' or 'EPC', found 'EPC'"],
 
             ['MEDIA_IMAGE_TEXT_60', 'caption', "HIP/EPC image caption 'MEDIA_IMAGE_TEXT_60' must be 'HIP' or 'EPC', found 'caption'"],
             ['MEDIA_IMAGE_TEXT_60', 'HIP'    , ""],
             ['MEDIA_IMAGE_TEXT_60', 'EPC'    , ""],
 
             ['MEDIA_IMAGE_TEXT_61', 'caption', "HIP/EPC image caption 'MEDIA_IMAGE_TEXT_61' must be 'HIP' or 'EPC', found 'caption'"],
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
            $this->assertEquals($row['AGENT_REF'], 'XX99XX_FBM2766');
        }
    
    }

}
