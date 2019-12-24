<?php

namespace Tests\Feature;

// ini_set('memory_limit', 21000000);

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Log;
use Src\BlmFile\BlmFile;

class MediaFilesnamesAreInCorrectFormatTest extends TestCase
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
        'NEW_HOME_FLAG' => '0',
        'MEDIA_IMAGE_00' => 'XX99XX_FBM2766_IMG_00.jpg',
    ];

    public function listMediaColumns()
    {
        return [
            // name          - value                      - result
            ['MEDIA_IMAGE_01', 'XX99XX_FBM2766_IMG_01.xxx', "media column 'MEDIA_IMAGE_01', value 'XX99XX_FBM2766_IMG_01.xxx' must end in one of '.jpg', '.gif', '.png'"],
            ['MEDIA_IMAGE_01', '99xx99_FBM2766_IMG_01.jpg', "must begin with 'XX99XX_FBM2766', found '99xx99_FBM2766_IMG_01.jpg'"],
            ['MEDIA_IMAGE_01', 'XX99XX_FBMxxxx_IMG_01.jpg', "must begin with 'XX99XX_FBM2766', found 'XX99XX_FBMxxxx_IMG_01.jpg'"],
            ['MEDIA_IMAGE_01', 'XX99XX_FBM2766_xxx_01.jpg', "must begin with 'XX99XX_FBM2766_IMG_', found 'XX99XX_FBM2766_xxx_01.jpg'"],
            ['MEDIA_IMAGE_01', 'XX99XX_FBM2766_IMG_00.jpg', "must begin with 'XX99XX_FBM2766_IMG_01', found 'XX99XX_FBM2766_IMG_00.jpg'"],

            ['MEDIA_FLOOR_PLAN_01', 'XX99XX_FBM2766_FLP_01.xxx', "media column 'MEDIA_FLOOR_PLAN_01', value 'XX99XX_FBM2766_FLP_01.xxx' must end in one of '.jpg', '.gif', '.png'"],
            ['MEDIA_FLOOR_PLAN_01', '99xx99_FBM2766_FLP_01.jpg', "(2) Media file name not in correct format, must begin with 'XX99XX_FBM2766', found '99xx99_FBM2766_FLP_01.jpg'"],
            ['MEDIA_FLOOR_PLAN_01', 'XX99XX_FBMxxxx_FLP_01.jpg', "(2) Media file name not in correct format, must begin with 'XX99XX_FBM2766', found 'XX99XX_FBMxxxx_FLP_01.jpg'"],
            ['MEDIA_FLOOR_PLAN_01', 'XX99XX_FBM2766_xxx_01.jpg', "(3) Media column 'MEDIA_FLOOR_PLAN_01' file name not in correct format, must begin with 'XX99XX_FBM2766_FLP_', found 'XX99XX_FBM2766_xxx_01.jpg'"],
            ['MEDIA_FLOOR_PLAN_01', 'XX99XX_FBM2766_FLP_00.jpg', "(4) Media column 'MEDIA_FLOOR_PLAN_01' file name not in correct format, must begin with 'XX99XX_FBM2766_FLP_01', found 'XX99XX_FBM2766_FLP_00.jpg'"],

            ['MEDIA_DOCUMENT_01', 'XX99XX_FBM2766_DOC_01.xxx', "media column 'MEDIA_DOCUMENT_01', value 'XX99XX_FBM2766_DOC_01.xxx' must end in one of '.pdf'"],
            ['MEDIA_DOCUMENT_01', '99xx99_FBM2766_DOC_01.jpg', "media column 'MEDIA_DOCUMENT_01', value '99xx99_FBM2766_DOC_01.jpg' must end in one of '.pdf'"],
            ['MEDIA_DOCUMENT_01', 'XX99XX_FBMxxxx_DOC_01.jpg', "media column 'MEDIA_DOCUMENT_01', value 'XX99XX_FBMxxxx_DOC_01.jpg' must end in one of '.pdf'"],
            ['MEDIA_DOCUMENT_01', 'XX99XX_FBM2766_xxx_01.jpg', "media column 'MEDIA_DOCUMENT_01', value 'XX99XX_FBM2766_xxx_01.jpg' must end in one of '.pdf'"],
            ['MEDIA_DOCUMENT_01', 'XX99XX_FBM2766_DOC_00.jpg', "media column 'MEDIA_DOCUMENT_01', value 'XX99XX_FBM2766_DOC_00.jpg' must end in one of '.pdf'"],
        ];
    }
    
    /**
     * @dataProvider listMediaColumns
     * @test
     */
    public function test_MediaFilenames(String $name, String $value, String $result)
    {
        // <BRANCH>_<AGENT_REF>_<MEDIATYPE>_<n>.<file extension>

        $requiredColumns = $this->requiredColumns;
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
