<?php

namespace Tests\Feature;

// ini_set('memory_limit', 21000000);

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Log;
use Src\BlmFile\BlmFile;

class ColumnsAppearOnlyOnceTest extends TestCase
{
    protected $columnKeys = '';
    protected $columnData = '';

    // Required Columns
    private $requiredColumns = [ 
        'ok' => [
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
        'MEDIA_IMAGE_TEXT_00' => 'caption',
        ],
        'error' => [
            'AGENT_REF' => '999999_FBM2766',
            'BRANCH_ID' => '888888',
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
            'DUMMYKEY' => 'DUMMYDATA',
            'DESCRIPTION' => 'whatever whatever whatever whatever whatever whatever',
            'NEW_HOME_FLAG' => 'N',
            'SUMMARY' => 'whatever whatever whatever whatever',
            'MEDIA_IMAGE_00' => '888888_FBM2766_IMG_00.jpg',
            'MEDIA_IMAGE_TEXT_00' => 'caption',
            ]
    ];

    public function listColumns()
    {
        return [ ['ok'], ['error'] ];
    }
    
    /**
     * @dataProvider listColumns
     * @test
     */
    public function ColumnsAppearOnlyOnce(String $option)
    {
        $requiredColumns = $this->requiredColumns[$option];

        $columnKeys = implode('^', array_keys($requiredColumns)).'^~';
        $columnData = implode('^', array_values($requiredColumns)).'^~';

        $blm = new BlmFile();
        $blm->Version = '3';

        $blm->selectVersionColumnDefinitions('3');

        if ('error' === $option) {
            $columnKeys = str_replace('DUMMYKEY', 'AGENT_REF', $columnKeys);
            $columnData = str_replace('DUMMYDATA', '888888_FBM2766', $columnData);
            
            $this->expectExceptionMessage("Definition column 'AGENT_REF' must only appear once");
        }
        $blm->validateDefinition($columnKeys);

        $row = $blm->validateData($columnData);
        if ('ok' === $option) {
            $this->assertEquals($row['AGENT_REF'], '999999_FBM2766');
        }
    
    }

}
