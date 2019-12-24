<?php

namespace Tests\Feature;

// ini_set('memory_limit', 21000000);

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Log;
use Src\BlmFile\BlmFile;

class EnsureLettingFieldsAreDependantOnFlagTest extends TestCase
{
    protected $columnKeys = '';
    protected $columnData = '';

    // Required Columns
    private $testData = [ 
        'ok neutral' => [
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
            //------------------------------------------------------
            'LET_TYPE_ID' => 0, // 0 = not specified DEFAULT
            //------------------------------------------------------
            'SUMMARY' => 'whatever whatever whatever whatever',
            'DESCRIPTION' => 'whatever whatever whatever whatever whatever whatever',
            'NEW_HOME_FLAG' => '0',
            'MEDIA_IMAGE_00' => 'XX99XX_FBM2766_IMG_00.jpg',
            'MEDIA_IMAGE_TEXT_00' => 'caption',
        ],
        'ok student letting' => [
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
            //------------------------------------------------------
            'LET_TYPE_ID' => 3,
            "LET_CONTRACT_IN_MONTHS" => '12', // student
            "LET_WASHING_MACHINE_FLAG" => 'Y', // Y/N student
            "LET_DISHWASHER_FLAG" => 'N', // Y/N student
            "LET_BURGLAR_ALARM_FLAG" => 'Y', // Y/N student
            "LET_BILL_INC_WATER" => 'Y', // Y/N student
            "LET_BILL_INC_GAS" => 'Y', // Y/N student
            "LET_BILL_INC_ELECTRICITY" => 'Y', // Y/N student
            "LET_BILL_INC_TV_LICIENCE" => 'Y', // Y/N student
            "LET_BILL_INC_TV_SUBSCRIPTION" => 'Y', // Y/N student
            "LET_BILL_INC_INTERNET" => 'Y', // Y/N student
            //------------------------------------------------------
            'SUMMARY' => 'whatever whatever whatever whatever',
            'DESCRIPTION' => 'whatever whatever whatever whatever whatever whatever',
            'NEW_HOME_FLAG' => '0',
            'MEDIA_IMAGE_00' => 'XX99XX_FBM2766_IMG_00.jpg',
            'MEDIA_IMAGE_TEXT_00' => 'caption',
        ],
        'error student letting' => [
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
            //------------------------------------------------------
            'LET_TYPE_ID' => 0, // 0 = not specified DEFAULT
            "LET_CONTRACT_IN_MONTHS" => '12', // student
            "LET_WASHING_MACHINE_FLAG" => 'Y', // Y/N student
            "LET_DISHWASHER_FLAG" => 'N', // Y/N student
            "LET_BURGLAR_ALARM_FLAG" => 'Y', // Y/N student
            "LET_BILL_INC_WATER" => 'Y', // Y/N student
            "LET_BILL_INC_GAS" => 'Y', // Y/N student
            "LET_BILL_INC_ELECTRICITY" => 'Y', // Y/N student
            "LET_BILL_INC_TV_LICIENCE" => 'Y', // Y/N student
            "LET_BILL_INC_TV_SUBSCRIPTION" => 'Y', // Y/N student
            "LET_BILL_INC_INTERNET" => 'Y', // Y/N student
            //------------------------------------------------------
            'SUMMARY' => 'whatever whatever whatever whatever',
            'DESCRIPTION' => 'whatever whatever whatever whatever whatever whatever',
            'NEW_HOME_FLAG' => '0',
            'MEDIA_IMAGE_00' => 'XX99XX_FBM2766_IMG_00.jpg',
            'MEDIA_IMAGE_TEXT_00' => 'caption',
        ],
        'ok commercial letting' => [
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
            //-------------------------------------------------------------------
            'LET_TYPE_ID' => 4, // (Commercial only)
            "MIN_SIZE_ENTERED" => '15.5', // (Commercial only)
            "MAX_SIZE_ENTERED" => '15.5', // (Commercial only)
            "AREA_SIZE_UNIT_ID" => '1', // 1 - sq ft, (Commercial only)
            "BUSINESS_FOR_SALE_FLAG" => '0', // 0 - Not a business for sale; (Commercial only)
            "PRICE_PER_UNIT" => '15', // (Commercial only)
            "COMM_CLASS_ORDER_1" => 'B1', // B1, Business (Commercial only)
            //-------------------------------------------------------------------
            'SUMMARY' => 'whatever whatever whatever whatever',
            'DESCRIPTION' => 'whatever whatever whatever whatever whatever whatever',
            'NEW_HOME_FLAG' => '0',
            'MEDIA_IMAGE_00' => 'XX99XX_FBM2766_IMG_00.jpg',
            'MEDIA_IMAGE_TEXT_00' => 'caption'
        ],
        'error commercial letting' => [
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
            //-------------------------------------------------------------------
            'LET_TYPE_ID' => '0', // 0 = not specified DEFAULT
            "MIN_SIZE_ENTERED" => '15.5', // (Commercial only)
            "MAX_SIZE_ENTERED" => '15.5', // (Commercial only)
            "AREA_SIZE_UNIT_ID" => '1', // 1 - sq ft, (Commercial only)
            "BUSINESS_FOR_SALE_FLAG" => '0', // 0 - Not a business for sale; (Commercial only)
            "PRICE_PER_UNIT" => '15', // (Commercial only)
            "COMM_CLASS_ORDER_1" => 'B1', // B1, Business (Commercial only)
            //-------------------------------------------------------------------
            'SUMMARY' => 'whatever whatever whatever whatever',
            'DESCRIPTION' => 'whatever whatever whatever whatever whatever whatever',
            'NEW_HOME_FLAG' => '0',
            'MEDIA_IMAGE_00' => 'XX99XX_FBM2766_IMG_00.jpg',
            'MEDIA_IMAGE_TEXT_00' => 'caption',
            ],
        ];

    public function listMediaColumns()
    {
        return [ 
            ['ok neutral', 'ok', ""], 
            ['ok student letting', 'ok', ""], 
            ['error student letting', 'error', "Column 'LET_CONTRACT_IN_MONTHS' is for Student Letting only"],
            ['ok commercial letting', 'ok', ""],
            ['error commercial letting', 'error', "Column 'MIN_SIZE_ENTERED' is for Commercial Letting only"],
            ];
    }
    
    /**
     * @dataProvider listMediaColumns
     * @test
     */
    public function EnsureLettingFieldsAreDependantOnFlag(String $option, String $status, String $result)
    {
        $testData = $this->testData[$option];
        Log::debug("Option='{$option}'");

        $columnKeys = implode('^', array_keys($testData)).'^~';
        $columnData = implode('^', array_values($testData)).'^~';

        $blm = new BlmFile();
        $blm->Version = '3';
        $blm->selectVersionColumnDefinitions('3');

        if ('error' === $status) {
            $this->expectExceptionMessage($result);
        }
Log::debug("testData=".print_r($testData, true));
        $blm->validateDefinition($columnKeys);
        $row = $blm->validateData($columnData);

        if ('ok' === $status) {
            $this->assertEquals($row['AGENT_REF'], 'XX99XX_FBM2766');
        }
    
    }

}
