<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Log;
use Src\BlmFile\BlmFile;

class DateTest extends TestCase
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
        'LET_DATE_AVAILABLE' => '2019-12-17 15:49:30',
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
        'LET_TYPE_ID' => '3', // 0 = not specified DEFAULT
        'SUMMARY' => 'whatever whatever whatever whatever',
        'DESCRIPTION' => 'whatever whatever whatever whatever whatever whatever',
        'NEW_HOME_FLAG' => 'N',
    ];

    public function listEnumFields()
    {
        return [
            ['CREATE_DATE',  "X", "Date 'CREATE_DATE', value 'X', is not in the correct format 'Y-m-d H:i:s'"],
            ['CREATE_DATE',  "", ""], // optional
            ['CREATE_DATE',  '2019-01-01 00:00:00', ""], // ok
            ['CREATE_DATE',  "yesterday", "Date 'CREATE_DATE', value 'yesterday', is not in the correct format 'Y-m-d H:i:s'"],
            ['CREATE_DATE',  "2019-1-1 12:30:55", "Date 'CREATE_DATE', value '2019-1-1 12:30:55', is not in the correct format 'Y-m-d H:i:s'"],
            ['CREATE_DATE',  '2019-01-01', "Date 'CREATE_DATE', value '2019-01-01', is not in the correct format 'Y-m-d H:i:s'"],
            ['CREATE_DATE',  '2019-29-06 12:00:05', "Date 'CREATE_DATE', value '2019-29-06 12:00:05', is not in the correct format 'Y-m-d H:i:s'"],

            ['UPDATE_DATE',  "X", "Date 'UPDATE_DATE', value 'X', is not in the correct format 'Y-m-d H:i:s'"],
            ['UPDATE_DATE',  "", ""], // optional
            ['UPDATE_DATE',  '2019-01-01 00:00:00', ""], // ok
            ['UPDATE_DATE',  "yesterday", "Date 'UPDATE_DATE', value 'yesterday', is not in the correct format 'Y-m-d H:i:s'"],
            ['UPDATE_DATE',  "2019-1-1 12:30:55", "Date 'UPDATE_DATE', value '2019-1-1 12:30:55', is not in the correct format 'Y-m-d H:i:s'"],
            ['UPDATE_DATE',  '2019-01-01', "Date 'UPDATE_DATE', value '2019-01-01', is not in the correct format 'Y-m-d H:i:s'"],
            ['UPDATE_DATE',  '2019-29-06 12:00:05', "Date 'UPDATE_DATE', value '2019-29-06 12:00:05', is not in the correct format 'Y-m-d H:i:s'"],

            ['LET_DATE_AVAILABLE',  "X", "Date 'LET_DATE_AVAILABLE', value 'X', is not in the correct format 'Y-m-d H:i:s'"],
            ['LET_DATE_AVAILABLE',  "", ""], // optional
            ['LET_DATE_AVAILABLE',  '2019-01-01 00:00:00', ""], // ok
            ['LET_DATE_AVAILABLE',  "yesterday", "Date 'LET_DATE_AVAILABLE', value 'yesterday', is not in the correct format 'Y-m-d H:i:s'"],
            ['LET_DATE_AVAILABLE',  "2019-1-1 12:30:55", "Date 'LET_DATE_AVAILABLE', value '2019-1-1 12:30:55', is not in the correct format 'Y-m-d H:i:s'"],
            ['LET_DATE_AVAILABLE',  '2019-01-01', "Date 'LET_DATE_AVAILABLE', value '2019-01-01', is not in the correct format 'Y-m-d H:i:s'"],
            ['LET_DATE_AVAILABLE',  '2019-29-06 12:00:05', "Date 'LET_DATE_AVAILABLE', value '2019-29-06 12:00:05', is not in the correct format 'Y-m-d H:i:s'"],

        ];
    }

    /**
     * @dataProvider listEnumFields
     * @test
     */
    public function test_Date($fieldName, $value, $result)
    {
        $requiredColumns = $this->requiredColumns;
        $requiredColumns[$fieldName] = $value;


        $requiredColumns['MEDIA_IMAGE_00'] = '999999_FBM2766_IMG_00.jpg';

        $requiredColumns['MEDIA_IMAGE_59'] ='999999_FBM2766_IMG_59.jpg';
        $requiredColumns['MEDIA_IMAGE_60'] = '999999_FBM2766_IMG_60.jpg';
        $requiredColumns['MEDIA_IMAGE_61'] = '999999_FBM2766_IMG_61.jpg';

        $requiredColumns['MEDIA_IMAGE_TEXT_59'] = 'caption';
        $requiredColumns['MEDIA_IMAGE_TEXT_60'] = 'HIP';
        $requiredColumns['MEDIA_IMAGE_TEXT_61'] = 'EPC';

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
