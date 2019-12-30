<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Log;
use Src\BlmFile\BlmFile;

class IntTest extends TestCase
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
        'LET_TYPE_ID' => '3', // 0 = not specified DEFAULT
        'SUMMARY' => 'whatever whatever whatever whatever',
        'DESCRIPTION' => 'whatever whatever whatever whatever whatever whatever',
        'NEW_HOME_FLAG' => 'N',
    ];

    public function listEnumFields()
    {
        return [
            ['BRANCH_ID',            "X", "Int 'BRANCH_ID', value 'X', is not an int"],
            ['BRANCH_ID',             "", "Data field 'BRANCH_ID' is too small, minimum length is '1'"], 
            ['BRANCH_ID',           "-1", "Int 'BRANCH_ID', value '-1', is not an int"],
            ['BRANCH_ID',       "0", ""],
            ['BRANCH_ID',       "1", ""],
            ['BRANCH_ID',           "1.", "Int 'BRANCH_ID', value '1.', is not an int"],
            ['BRANCH_ID',          "1.0", "Int 'BRANCH_ID', value '1.0', is not an int"],
            ['BRANCH_ID',          "0.0", "Int 'BRANCH_ID', value '0.0', is not an int"],
            ['BRANCH_ID',          "0.1", "Int 'BRANCH_ID', value '0.1', is not an int"],
            ['BRANCH_ID',    "999999999", ""], // 9
            ['BRANCH_ID',   "9999999999", ""], // 10
            ['BRANCH_ID',  "99999999999", "Data field 'BRANCH_ID' is too big, maximum length is '10', found '11'"], // 11

            ['BEDROOMS',            "X", "Int 'BEDROOMS', value 'X', is not an int"],
            ['BEDROOMS',             "", "Data field 'BEDROOMS' is too small, minimum length is '1'"], 
            ['BEDROOMS',           "-1", "Int 'BEDROOMS', value '-1', is not an int"],
            ['BEDROOMS',            "0", ""],
            ['BEDROOMS',            "1", ""],
            ['BEDROOMS',           "1.", "Int 'BEDROOMS', value '1.', is not an int"],
            ['BEDROOMS',          "1.0", "Int 'BEDROOMS', value '1.0', is not an int"],
            ['BEDROOMS',          "0.0", "Int 'BEDROOMS', value '0.0', is not an int"],
            ['BEDROOMS',          "0.1", "Int 'BEDROOMS', value '0.1', is not an int"],
            ['BEDROOMS',    "999999999", ""], // 9
            ['BEDROOMS',   "9999999999", ""], // 10
            ['BEDROOMS',  "99999999999", "Data field 'BEDROOMS' is too big, maximum length is '10', found '11'"], // 11

        ];
    }

    /**
     * @dataProvider listEnumFields
     * @test
     */
    public function test_Int($fieldName, $value, $result)
    {
        $requiredColumns = $this->requiredColumns;
        $requiredColumns[$fieldName] = $value;

        $requiredColumns['MEDIA_IMAGE_00'] = '999999_FBM2766_IMG_00.jpg';

        // $requiredColumns['MEDIA_IMAGE_59'] ='999999_FBM2766_IMG_59.jpg';
        $requiredColumns['MEDIA_IMAGE_60'] = '999999_FBM2766_IMG_60.jpg';
        // $requiredColumns['MEDIA_IMAGE_61'] = '999999_FBM2766_IMG_61.jpg';

        // $requiredColumns['MEDIA_IMAGE_TEXT_59'] = 'caption';
        $requiredColumns['MEDIA_IMAGE_TEXT_60'] = 'HIP';
        // $requiredColumns['MEDIA_IMAGE_TEXT_61'] = 'EPC';

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
