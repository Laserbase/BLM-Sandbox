<?php

namespace Tests\Feature;

// ini_set('memory_limit', 21000000);

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Log;
use Src\BlmFile\BlmFile;

class MediaFilesnamesThatCanBeUrlTest extends TestCase
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
    ];

    public function listColumns()
    {
        return [
            // name          - value                      - result
            ['MEDIA_IMAGE_00', '999999_FBM2766_IMG_00.jpg'        , ""],
            ['MEDIA_IMAGE_00', 'http://127.0.0.1:80/mfp1-aaa.jpg' , "Media column 'MEDIA_IMAGE_00' must be a filename, found a url 'http://127.0.0.1:80/mfp1-aaa.jpg'"],
            ['MEDIA_IMAGE_00', 'https://127.0.0.1:80/mfp1-bbb.jpg', "Media column 'MEDIA_IMAGE_00' must be a filename, found a url 'https://127.0.0.1:80/mfp1-bbb.jpg'"],
            ['MEDIA_IMAGE_00', 'file:///passwd'                   , "Media column 'MEDIA_IMAGE_00' must be a filename, found a url 'file:///passwd'"],
            ['MEDIA_IMAGE_00', 'http://127.0.0.1:80/mfp.jpg'      , "Media column 'MEDIA_IMAGE_00' must be a filename, found a url 'http://127.0.0.1:80/mfp.jpg'"],

            // name          - value                      - result
            ['MEDIA_IMAGE_01', '999999_FBM2766_IMG_01.jpg', ""],
            ['MEDIA_IMAGE_01', '999999_FBMxxxx_IMG_01.jpg', "Media file name not in correct format, must begin with '999999_FBM2766', found '999999_FBMxxxx', value '999999_FBMxxxx_IMG_01.jpg'"],
            ['MEDIA_IMAGE_01', '999999_FBM2766_xxx_01.jpg', "must begin with '999999_FBM2766_IMG_', found '999999_FBM2766_xxx_01.jpg'"],
            ['MEDIA_IMAGE_01', '999999_FBM2766_IMG_01.xxx', "media column 'MEDIA_IMAGE_01', value '999999_FBM2766_IMG_01.xxx' must end in one of '.jpg', '.gif', '.png'"],
            ['MEDIA_IMAGE_01', '999999_FBM2766_IMG_00.jpg', "must begin with '999999_FBM2766_IMG_01', found '999999_FBM2766_IMG_00.jpg'"],

            // name               - value                              - result
            ['MEDIA_FLOOR_PLAN_01', '999999_FBM2766_FLP_01.jpg'        , ""],
            ['MEDIA_FLOOR_PLAN_01', 'http://127.0.0.1:80/mfp1-aaa.jpg' , ""],
            ['MEDIA_FLOOR_PLAN_01', 'https://127.0.0.1:80/mfp1-bbb.jpg', ""],
            ['MEDIA_FLOOR_PLAN_01', 'file:///passwd'                   , "Media column 'MEDIA_FLOOR_PLAN_01' wrong format, 'file' is not a valid url scheme, found 'file:"],
            ['MEDIA_FLOOR_PLAN_01', 'http://127.0.0.1:80/mfp.jpg'      , ""],

            // name             - value                              - result
            ['MEDIA_DOCUMENT_01', '999999_FBM2766_DOC_01.xxx'        , "media column 'MEDIA_DOCUMENT_01', value '999999_FBM2766_DOC_01.xxx' must end in one of '.pdf'"],
            ['MEDIA_DOCUMENT_01', '999999_FBM2766_DOC_01.pdf'        , ""], // ok
            ['MEDIA_DOCUMENT_01', '999999_FBMxxxx_DOC_01.pdf'        , "Media file name not in correct format, must begin with '999999_FBM2766', found '999999_FBMxxxx', value '999999_FBMxxxx_DOC_01.pdf'"],
            ['MEDIA_DOCUMENT_01', '999999_FBM2766_xxx_01.pdf'        , "Media column 'MEDIA_DOCUMENT_01' file name not in correct format, must begin with '999999_FBM2766_DOC_', found '999999_FBM2766_xxx_01.pdf'"],
            ['MEDIA_DOCUMENT_01', '999999_FBM2766_DOC_00.pdf'        , "Media column 'MEDIA_DOCUMENT_01' file name not in correct format, must begin with '999999_FBM2766_DOC_01', found '999999_FBM2766_DOC_00.pdf'"],
            ['MEDIA_DOCUMENT_01', '999999_FBM2766_DOC_01.pdf'        , ""],
            ['MEDIA_DOCUMENT_01', 'http://127.0.0.1:80/mfp1-aaa.jpg' , ""],
            ['MEDIA_DOCUMENT_01', 'https://127.0.0.1:80/mfp1-bbb.jpg', ""],
            ['MEDIA_DOCUMENT_01', 'file://passwd'                   , "Media column 'MEDIA_DOCUMENT_01' wrong format, 'file' is not a valid url scheme, found 'file:"],
            ['MEDIA_DOCUMENT_01', 'http://127.0.0.1:80/mfpLLL'       , ""],

        ];
    }
    
    /**
     * @dataProvider listColumns
     * @test
     */
    public function test_MediaFilenamesCanBeUrls(String $name, String $value, String $result)
    {
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
            $this->assertEquals($row['AGENT_REF'], '999999_FBM2766');
        }
    
    }

}
