<?php

namespace Tests\Feature;

// ini_set('memory_limit', 21000000);

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Log;
use Src\BlmFile\BlmFile;

class IsMediaTypeCorrectTest extends TestCase
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
    ];
    
    public function test_IsImgMediaTypeMissingExtensionTest()
    {
        $requiredColumns = $this->requiredColumns;
        $requiredColumns['MEDIA_IMAGE_00'] = 'XX99XX_FBM2766_IMG_01';

        $columnKeys = implode('^', array_keys($requiredColumns)).'^~';
        $columnData = implode('^', array_values($requiredColumns)).'^~';

        $blm = new BlmFile();
        $blm->Version = '3';

        $blm->selectVersionColumnDefinitions('3');

        $blm->validateDefinition($columnKeys);

        $this->expectExceptionMessage("Error: Not a valid BLM file, media column 'MEDIA_IMAGE_00', value 'XX99XX_FBM2766_IMG_01' must end in one of '.jpg', '.gif', '.png'");
        $row = $blm->validateData($columnData);
    }

    public function test_IsImgMediaTypeUnknownTest()
    {
        $requiredColumns = $this->requiredColumns;
        $requiredColumns['MEDIA_IMAGE_00'] = 'XX99XX_FBM2766_IMG_01.xxx';

        $columnKeys = implode('^', array_keys($requiredColumns)).'^~';
        $columnData = implode('^', array_values($requiredColumns)).'^~';

        $blm = new BlmFile();
        $blm->Version = '3';

        $blm->selectVersionColumnDefinitions('3');

        $blm->validateDefinition($columnKeys);

        $this->expectExceptionMessage("Error: Not a valid BLM file, media column 'MEDIA_IMAGE_00', value 'XX99XX_FBM2766_IMG_01.xxx' must end in one of '.jpg', '.gif', '.png'");
        $row = $blm->validateData($columnData);
    }

}
