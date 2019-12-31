<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Log;
use Src\BlmFile\BlmFile;

class NumTest extends TestCase
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

    public function listFields()
    {
        return [
            // 'num|min:0', // deposit amount
            ['LET_BOND',            "X", "Data field 'LET_BOND', value 'X', is not a decimal number"],
            ['LET_BOND',             "", ""], // optional 
            ['LET_BOND',           "-1", "Data field 'LET_BOND', value '-1', is not a decimal number"],
            ['LET_BOND',       "0", ""],
            ['LET_BOND',       "1", ""],
            ['LET_BOND',           "1.", ""],
            ['LET_BOND',          "1.0", ""],
            ['LET_BOND',          "0.0", ""],
            ['LET_BOND',   "9.99999999", ""],
            ['LET_BOND',   "99.99999999", ""],
            ['LET_BOND',  "99999999999", ""],

            // "PRICE" => 'num|required|min:1',
            ['PRICE',                 "X", "Data field 'PRICE', value 'X', is not a decimal number"],
            ['PRICE',                  "", "Data field 'PRICE' is too small, minimum length is '1'"], // required
            ['PRICE',                "-1", "Data field 'PRICE', value '-1', is not a decimal number"],
            ['PRICE',            "0", ""],
            ['PRICE',            "1", ""],
            ['PRICE',           "1.", ""],
            ['PRICE',          "1.0", ""],
            ['PRICE',          "0.0", ""],
            ['PRICE',   "9.99999999", ""],
            ['PRICE',  "99.99999999", ""],

            ['PRICE',    "0..0", "Data field 'PRICE', value '0..0', is not a decimal number"],
            ['PRICE',  " 99.99", "Data field 'PRICE', value ' 99.99', is not a decimal number"],
            ['PRICE',  "+99.99", "Data field 'PRICE', value '+99.99', is not a decimal number"],

            // "MIN_SIZE_ENTERED" => 'num|min:0|max:15', // (Commercial only)
            ['MIN_SIZE_ENTERED',            "X", "Data field 'MIN_SIZE_ENTERED', value 'X', is not a decimal number"],
            ['MIN_SIZE_ENTERED',             "", ""], // not required
            ['MIN_SIZE_ENTERED',           "-1", "Data field 'MIN_SIZE_ENTERED', value '-1', is not a decimal number"],
            ['MIN_SIZE_ENTERED',            "0", ""],
            ['MIN_SIZE_ENTERED',            "1", ""],
            ['MIN_SIZE_ENTERED',           "1.", ""],
            ['MIN_SIZE_ENTERED',          "1.0", ""],
            ['MIN_SIZE_ENTERED',          "0.0", ""],
            ['MIN_SIZE_ENTERED',           ".9", ""],
            ['MIN_SIZE_ENTERED',   "9.99999999", ""],
            ['MIN_SIZE_ENTERED',  "99.99999999", ""],

            ['MIN_SIZE_ENTERED',    "99999999999.99", ""], // 14
            ['MIN_SIZE_ENTERED',   "999999999999.99", ""], // 15
            ['MIN_SIZE_ENTERED',  "9999999999999.99", "Data field 'MIN_SIZE_ENTERED' is too big, maximum length is '15', found '16'"], // 16

        ];
    }

    /**
     * @dataProvider listFields
     * @test
     */
    public function test_Num($fieldName, $value, $result)
    {
        $requiredColumns = $this->requiredColumns;
        $requiredColumns[$fieldName] = $value;

        $requiredColumns['MEDIA_IMAGE_00'] = '999999_FBM2766_IMG_00.jpg';
        $requiredColumns['MEDIA_IMAGE_60'] = '999999_FBM2766_IMG_60.jpg';
        $requiredColumns['MEDIA_IMAGE_TEXT_60'] = 'HIP';
        if ('MIN_SIZE_ENTERED' === $fieldName) {
            $requiredColumns['LET_TYPE_ID'] = '4'; // commercial letting
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
