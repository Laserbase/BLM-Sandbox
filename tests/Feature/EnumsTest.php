<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Log;
use Src\BlmFile\BlmFile;

class EnumTest extends TestCase
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
            ['STATUS_ID',  "X", "Enum 'STATUS_ID', value 'X' is not in the allowed list of values"],
            ['STATUS_ID',   "", "Data field 'STATUS_ID' is too small, minimum length is '1', found '0'"],
            ['STATUS_ID', "-1", "Data field 'STATUS_ID' is too big, maximum length is '1', found '2'"],
            ['STATUS_ID',  "0", ""],
            ['STATUS_ID',  "1", ""],
            ['STATUS_ID',  "2", ""],
            ['STATUS_ID',  "3", ""],
            ['STATUS_ID',  "4", ""],
            ['STATUS_ID',  "5", ""],
            ['STATUS_ID',  "6", "Enum 'STATUS_ID', value '6' is not in the allowed list of values"],

            ['PROP_SUB_ID',  "X", "Enum 'PROP_SUB_ID', value 'X' is not in the allowed list of values"],
            ['PROP_SUB_ID',   "", "Data field 'PROP_SUB_ID' is too small, minimum length is '1', found '0'"], 
            ['PROP_SUB_ID', "-1", "Enum 'PROP_SUB_ID', value '-1' is not in the allowed list of values"],
            ['PROP_SUB_ID',  "0", ""],
            ['PROP_SUB_ID',  "1", ""],

            ['PROP_SUB_ID',  "68", ""],
            ['PROP_SUB_ID',  "69", "Enum 'PROP_SUB_ID', value '69' is not in the allowed list of values"],
            ['PROP_SUB_ID',  "70", "Enum 'PROP_SUB_ID', value '70' is not in the allowed list of values"],
            ['PROP_SUB_ID',  "71", ""],

            ['PROP_SUB_ID',  "511", ""],
            ['PROP_SUB_ID',  "512", "Enum 'PROP_SUB_ID', value '512' is not in the allowed list of values"],

            ['TENURE_TYPE_ID',            "X", "Enum 'TENURE_TYPE_ID', value 'X' is not in the allowed list of values"],
            ['TENURE_TYPE_ID',             "", ""], // not required
            ['TENURE_TYPE_ID',           "-1", "Data field 'TENURE_TYPE_ID' is too big, maximum length is '1', found '2'"],
            ['TENURE_TYPE_ID',            "0", "Enum 'TENURE_TYPE_ID', value '0' is not in the allowed list of values"],
            ['TENURE_TYPE_ID',            "1", ""],
            ['TENURE_TYPE_ID',            "2", ""],
            ['TENURE_TYPE_ID',            "3", ""],
            ['TENURE_TYPE_ID',            "4", ""],
            ['TENURE_TYPE_ID',            "5", ""],
            ['TENURE_TYPE_ID',            "6", "Enum 'TENURE_TYPE_ID', value '6' is not in the allowed list of values"],
            ['TENURE_TYPE_ID',           "1.", "Data field 'TENURE_TYPE_ID' is too big, maximum length is '1'"],
            ['TENURE_TYPE_ID',          "1.0", "Data field 'TENURE_TYPE_ID' is too big, maximum length is '1'"],
            ['TENURE_TYPE_ID',          "0.0", "Data field 'TENURE_TYPE_ID' is too big, maximum length is '1'"],
            ['TENURE_TYPE_ID',          "0.1", "Data field 'TENURE_TYPE_ID' is too big, maximum length is '1'"],
            ['TENURE_TYPE_ID',            "9", "Enum 'TENURE_TYPE_ID', value '9' is not in the allowed list of values"], // 1
            ['TENURE_TYPE_ID',           "99", "Data field 'TENURE_TYPE_ID' is too big, maximum length is '1', found '2'"],  // 2
            ['TENURE_TYPE_ID',  "99999999999", "Data field 'TENURE_TYPE_ID' is too big, maximum length is '1', found '11'"], // 11

            ['LET_TYPE_ID',  "X", "Enum 'LET_TYPE_ID', value 'X' is not in the allowed list of values"],
            ['LET_TYPE_ID',   "", ""], // default 0
            ['LET_TYPE_ID', "-1", "Data field 'LET_TYPE_ID' is too big, maximum length is '1', found '2'"],
            ['LET_TYPE_ID',  "0", ""], // default
            ['LET_TYPE_ID',  "1", ""],
            ['LET_TYPE_ID',  "2", ""],
            ['LET_TYPE_ID',  "3", ""],
            ['LET_TYPE_ID',  "4", ""],
            ['LET_TYPE_ID',  "5", "Enum 'LET_TYPE_ID', value '5' is not in the allowed list of values"],

            ['LET_FURN_ID',  "X", "Enum 'LET_FURN_ID', value 'X' is not in the allowed list of values"],
            ['LET_FURN_ID',   "", ""], // default 3
            ['LET_FURN_ID', "-1", "Data field 'LET_FURN_ID' is too big, maximum length is '1', found '2'"],
            ['LET_FURN_ID',  "0", ""],
            ['LET_FURN_ID',  "1", ""],
            ['LET_FURN_ID',  "2", ""],
            ['LET_FURN_ID',  "3", ""], // default
            ['LET_FURN_ID',  "4", ""],
            ['LET_FURN_ID',  "5", "Enum 'LET_FURN_ID', value '5' is not in the allowed list of values"],

            ['LET_RENT_FREQUENCY',  "X", "Enum 'LET_RENT_FREQUENCY', value 'X' is not in the allowed list of values"],
            ['LET_RENT_FREQUENCY',   "", ""], // default 1
            ['LET_RENT_FREQUENCY', "-1", "Data field 'LET_RENT_FREQUENCY' is too big, maximum length is '1', found '2'"],
            ['LET_RENT_FREQUENCY',  "0", ""],
            ['LET_RENT_FREQUENCY',  "1", ""], //'Monthly', // - DEFAULT if null
            ['LET_RENT_FREQUENCY',  "2", ""],
            ['LET_RENT_FREQUENCY',  "3", ""],
            ['LET_RENT_FREQUENCY',  "4", "Enum 'LET_RENT_FREQUENCY', value '4' is not in the allowed list of values"],
            ['LET_RENT_FREQUENCY',  "5", ""],
            ['LET_RENT_FREQUENCY',  "6", "Enum 'LET_RENT_FREQUENCY', value '6' is not in the allowed list of values"],

            ['TRANS_TYPE_ID',  "X", "Enum 'TRANS_TYPE_ID', value 'X' is not in the allowed list of values"],
            ['TRANS_TYPE_ID',   "", "Data field 'TRANS_TYPE_ID' is too small, minimum length is '1', found '0'"],
            ['TRANS_TYPE_ID', "-1", "Data field 'TRANS_TYPE_ID' is too big, maximum length is '1', found '2'"],
            ['TRANS_TYPE_ID',  "0", "Enum 'TRANS_TYPE_ID', value '0' is not in the allowed list of values"],
            ['TRANS_TYPE_ID',  "1", ""],
            ['TRANS_TYPE_ID',  "2", ""],
            ['TRANS_TYPE_ID',  "3", "Enum 'TRANS_TYPE_ID', value '3' is not in the allowed list of values"],

            ['NEW_HOME_FLAG',  "X", "Enum 'NEW_HOME_FLAG', value 'X' is not in the allowed list of values"],
            ['NEW_HOME_FLAG',   "", ""], // default N
            ['NEW_HOME_FLAG', "-1", "Data field 'NEW_HOME_FLAG' is too big, maximum length is '1', found '2'"],
            ['NEW_HOME_FLAG',  "Y", ""],
            ['NEW_HOME_FLAG',  "N", ""], // default
            ['NEW_HOME_FLAG',  "0", "Enum 'NEW_HOME_FLAG', value '0' is not in the allowed list of values"],
            ['NEW_HOME_FLAG',  "1", "Enum 'NEW_HOME_FLAG', value '1' is not in the allowed list of values"],

            ['PRICE_QUALIFIER',  "X", "Enum 'PRICE_QUALIFIER', value 'X' is not in the allowed list of values"],
            ['PRICE_QUALIFIER',   "", ""], // default 0
            ['PRICE_QUALIFIER', "-1", "Enum 'PRICE_QUALIFIER', value '-1' is not in the allowed list of values"],
            ['PRICE_QUALIFIER',  "0", ""], // - DEFAULT if null
            ['PRICE_QUALIFIER',  "1", ""],
            ['PRICE_QUALIFIER',  "2", ""],
            ['PRICE_QUALIFIER',  "3", ""],
            ['PRICE_QUALIFIER',  "4", ""],
            ['PRICE_QUALIFIER',  "5", ""],
            ['PRICE_QUALIFIER',  "7", ""],
            ['PRICE_QUALIFIER',  "8", "Enum 'PRICE_QUALIFIER', value '8' is not in the allowed list of values"],
            ['PRICE_QUALIFIER',  "9", ""],
            ['PRICE_QUALIFIER',  "10", ""],
            ['PRICE_QUALIFIER',  "11", ""],
            ['PRICE_QUALIFIER',  "12", ""],
            ['PRICE_QUALIFIER',  "13", "Enum 'PRICE_QUALIFIER', value '13' is not in the allowed list of values"],
            ['PRICE_QUALIFIER',  "14", ""],
            ['PRICE_QUALIFIER',  "15", ""],
            ['PRICE_QUALIFIER',  "16", "num 'PRICE_QUALIFIER', value '16' is not in the allowed list of values"],
        
            ['PUBLISHED_FLAG',  "X", "Enum 'PUBLISHED_FLAG', value 'X' is not in the allowed list of values"],
            ['PUBLISHED_FLAG',   "", "Data field 'PUBLISHED_FLAG' is too small, minimum length is '1', found '0'"], 
            ['PUBLISHED_FLAG', "-1", "Data field 'PUBLISHED_FLAG' is too big, maximum length is '1', found '2'"],
            ['PUBLISHED_FLAG',  "0", ""],
            ['PUBLISHED_FLAG',  "1", ""],
            ['PUBLISHED_FLAG',  "2", "Enum 'PUBLISHED_FLAG', value '2' is not in the allowed list of values"],

            ['LET_WASHING_MACHINE_FLAG',  "X", "Enum 'LET_WASHING_MACHINE_FLAG', value 'X' is not in the allowed list of values"],
            ['LET_WASHING_MACHINE_FLAG',   "", ""],
            ['LET_WASHING_MACHINE_FLAG', "-1", "Data field 'LET_WASHING_MACHINE_FLAG' is too big, maximum length is '1', found '2'"],
            ['LET_WASHING_MACHINE_FLAG',  "Y", ""],
            ['LET_WASHING_MACHINE_FLAG',  "N", ""],
            ['LET_WASHING_MACHINE_FLAG',  "0", "Enum 'LET_WASHING_MACHINE_FLAG', value '0' is not in the allowed list of values"],
            ['LET_WASHING_MACHINE_FLAG',  "1", "Enum 'LET_WASHING_MACHINE_FLAG', value '1' is not in the allowed list of values"],

            ['LET_DISHWASHER_FLAG',  "X", "Enum 'LET_DISHWASHER_FLAG', value 'X' is not in the allowed list of values"],
            ['LET_DISHWASHER_FLAG',   "", ""],
            ['LET_DISHWASHER_FLAG', "-1", "Data field 'LET_DISHWASHER_FLAG' is too big, maximum length is '1', found '2'"],
            ['LET_DISHWASHER_FLAG',  "Y", ""],
            ['LET_DISHWASHER_FLAG',  "N", ""],
            ['LET_DISHWASHER_FLAG',  "0", "Enum 'LET_DISHWASHER_FLAG', value '0' is not in the allowed list of values"],
            ['LET_DISHWASHER_FLAG',  "1", "Enum 'LET_DISHWASHER_FLAG', value '1' is not in the allowed list of values"],

            ['LET_BURGLAR_ALARM_FLAG',  "X", "Enum 'LET_BURGLAR_ALARM_FLAG', value 'X' is not in the allowed list of values"],
            ['LET_BURGLAR_ALARM_FLAG',   "", ""],
            ['LET_BURGLAR_ALARM_FLAG', "-1", "Data field 'LET_BURGLAR_ALARM_FLAG' is too big, maximum length is '1', found '2'"],
            ['LET_BURGLAR_ALARM_FLAG',  "Y", ""],
            ['LET_BURGLAR_ALARM_FLAG',  "N", ""], 
            ['LET_BURGLAR_ALARM_FLAG',  "0", "Enum 'LET_BURGLAR_ALARM_FLAG', value '0' is not in the allowed list of values"],
            ['LET_BURGLAR_ALARM_FLAG',  "1", "Enum 'LET_BURGLAR_ALARM_FLAG', value '1' is not in the allowed list of values"],

            ['LET_BILL_INC_WATER',  "X", "Enum 'LET_BILL_INC_WATER', value 'X' is not in the allowed list of values"],
            ['LET_BILL_INC_WATER',   "", ""],
            ['LET_BILL_INC_WATER', "-1", "Data field 'LET_BILL_INC_WATER' is too big, maximum length is '1', found '2'"],
            ['LET_BILL_INC_WATER',  "Y", ""],
            ['LET_BILL_INC_WATER',  "N", ""],
            ['LET_BILL_INC_WATER',  "0", "Enum 'LET_BILL_INC_WATER', value '0' is not in the allowed list of values"],
            ['LET_BILL_INC_WATER',  "1", "Enum 'LET_BILL_INC_WATER', value '1' is not in the allowed list of values"],

            ['LET_BILL_INC_GAS',  "X", "Enum 'LET_BILL_INC_GAS', value 'X' is not in the allowed list of values"],
            ['LET_BILL_INC_GAS',   "", ""],
            ['LET_BILL_INC_GAS', "-1", "Data field 'LET_BILL_INC_GAS' is too big, maximum length is '1', found '2'"],
            ['LET_BILL_INC_GAS',  "Y", ""],
            ['LET_BILL_INC_GAS',  "N", ""],
            ['LET_BILL_INC_GAS',  "0", "Enum 'LET_BILL_INC_GAS', value '0' is not in the allowed list of values"],
            ['LET_BILL_INC_GAS',  "1", "Enum 'LET_BILL_INC_GAS', value '1' is not in the allowed list of values"],

            ['LET_BILL_INC_ELECTRICITY',  "X", "Enum 'LET_BILL_INC_ELECTRICITY', value 'X' is not in the allowed list of values"],
            ['LET_BILL_INC_ELECTRICITY',   "", ""],
            ['LET_BILL_INC_ELECTRICITY', "-1", "Data field 'LET_BILL_INC_ELECTRICITY' is too big, maximum length is '1', found '2'"],
            ['LET_BILL_INC_ELECTRICITY',  "Y", ""],
            ['LET_BILL_INC_ELECTRICITY',  "N", ""],
            ['LET_BILL_INC_ELECTRICITY',  "0", "Enum 'LET_BILL_INC_ELECTRICITY', value '0' is not in the allowed list of values"],
            ['LET_BILL_INC_ELECTRICITY',  "1", "Enum 'LET_BILL_INC_ELECTRICITY', value '1' is not in the allowed list of values"],

            ['LET_BILL_INC_TV_LICIENCE',  "X", "Enum 'LET_BILL_INC_TV_LICIENCE', value 'X' is not in the allowed list of values"],
            ['LET_BILL_INC_TV_LICIENCE',   "", ""],
            ['LET_BILL_INC_TV_LICIENCE', "-1", "Data field 'LET_BILL_INC_TV_LICIENCE' is too big, maximum length is '1', found '2'"],
            ['LET_BILL_INC_TV_LICIENCE',  "Y", ""],
            ['LET_BILL_INC_TV_LICIENCE',  "N", ""],
            ['LET_BILL_INC_TV_LICIENCE',  "0", "Enum 'LET_BILL_INC_TV_LICIENCE', value '0' is not in the allowed list of values"],
            ['LET_BILL_INC_TV_LICIENCE',  "1", "Enum 'LET_BILL_INC_TV_LICIENCE', value '1' is not in the allowed list of values"],

            ['LET_BILL_INC_TV_SUBSCRIPTION',  "X", "Enum 'LET_BILL_INC_TV_SUBSCRIPTION', value 'X' is not in the allowed list of values"],
            ['LET_BILL_INC_TV_SUBSCRIPTION',   "", ""], 
            ['LET_BILL_INC_TV_SUBSCRIPTION', "-1", "Data field 'LET_BILL_INC_TV_SUBSCRIPTION' is too big, maximum length is '1', found '2'"],
            ['LET_BILL_INC_TV_SUBSCRIPTION',  "Y", ""],
            ['LET_BILL_INC_TV_SUBSCRIPTION',  "N", ""],
            ['LET_BILL_INC_TV_SUBSCRIPTION',  "0", "Enum 'LET_BILL_INC_TV_SUBSCRIPTION', value '0' is not in the allowed list of values"],
            ['LET_BILL_INC_TV_SUBSCRIPTION',  "1", "Enum 'LET_BILL_INC_TV_SUBSCRIPTION', value '1' is not in the allowed list of values"],

            ['LET_BILL_INC_INTERNET',  "X", "Enum 'LET_BILL_INC_INTERNET', value 'X' is not in the allowed list of values"],
            ['LET_BILL_INC_INTERNET',   "", ""],
            ['LET_BILL_INC_INTERNET', "-1", "Data field 'LET_BILL_INC_INTERNET' is too big, maximum length is '1', found '2'"],
            ['LET_BILL_INC_INTERNET',  "Y", ""],
            ['LET_BILL_INC_INTERNET',  "N", ""],
            ['LET_BILL_INC_INTERNET',  "0", "Enum 'LET_BILL_INC_INTERNET', value '0' is not in the allowed list of values"],
            ['LET_BILL_INC_INTERNET',  "1", "Enum 'LET_BILL_INC_INTERNET', value '1' is not in the allowed list of values"],

            ['AREA_SIZE_UNIT_ID',  "X", "Enum 'AREA_SIZE_UNIT_ID', value 'X' is not in the allowed list of values"],
            ['AREA_SIZE_UNIT_ID',   "", ""],
            ['AREA_SIZE_UNIT_ID', "-1", "Data field 'AREA_SIZE_UNIT_ID' is too big, maximum length is '1', found '2'"],
            ['AREA_SIZE_UNIT_ID',  "0", "Enum 'AREA_SIZE_UNIT_ID', value '0' is not in the allowed list of values"],
            ['AREA_SIZE_UNIT_ID',  "1", ""],
            ['AREA_SIZE_UNIT_ID',  "2", ""],
            ['AREA_SIZE_UNIT_ID',  "3", ""],
            ['AREA_SIZE_UNIT_ID',  "4", ""],
            ['AREA_SIZE_UNIT_ID',  "5", "Enum 'AREA_SIZE_UNIT_ID', value '5' is not in the allowed list of values"],

            ['BUSINESS_FOR_SALE_FLAG',  "X", "Enum 'BUSINESS_FOR_SALE_FLAG', value 'X' is not in the allowed list of values"],
            ['BUSINESS_FOR_SALE_FLAG',   "", ""], 
            ['BUSINESS_FOR_SALE_FLAG', "-1", "Data field 'BUSINESS_FOR_SALE_FLAG' is too big, maximum length is '1', found '2'"],
            ['BUSINESS_FOR_SALE_FLAG',  "0", ""],
            ['BUSINESS_FOR_SALE_FLAG',  "1", ""],
            ['BUSINESS_FOR_SALE_FLAG',  "2", "Enum 'BUSINESS_FOR_SALE_FLAG', value '2' is not in the allowed list of values"],

        ];
    }

    /**
     * @dataProvider listFields
     * @test
     */
    public function test_Enum($fieldName, $value, $result)
    {
        $requiredColumns = $this->requiredColumns;
        $requiredColumns[$fieldName] = $value;

        switch ($fieldName) {
            case 'AREA_SIZE_UNIT_ID':
            case 'BUSINESS_FOR_SALE_FLAG':
                $requiredColumns['LET_TYPE_ID'] = '4';
            break;

            default:
                // SKIP
            break;
        }

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
