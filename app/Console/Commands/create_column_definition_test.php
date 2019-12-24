<?php

namespace App\Console\Commands;

use Src\BlmFile\BlmFile;
use Illuminate\Console\Command;

class create_column_definition_test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:create_column_definition_test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command create_column_definition_test';

    private $data = [
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

        // letting
        'LET_DATE_AVAILABLE' => 'XX99XX_FBM2766',
        'LET_BOND' => 'XX99XX_FBM2766',
        'ADMINISTRATION_FEE' => 'XX99XX_FBM2766',
        'LET_FURN_ID' => 'Y',
        'LET_RENT_FREQUENCY' => 'Y',
        'LET_CONTRACT_IN_MONTHS' => 'Y',
        'LET_WASHING_MACHINE_FLAG' => 'Y',
        'LET_DISHWASHER_FLAG' => 'Y',
        'LET_BURGLAR_ALARM_FLAG' => 'Y',
        'LET_BILL_INC_WATER' => 'Y',
        'LET_BILL_INC_GAS' => 'Y',
        'LET_BILL_INC_ELECTRICITY' => 'Y',
        'LET_BILL_INC_TV_LICIENCE' => 'Y',
        'LET_BILL_INC_TV_SUBSCRIPTION' => 'Y',
        'LET_BILL_INC_INTERNET' => 'Y',
        'TENURE_TYPE_ID' => 'XX99XX_FBM2766',

        // commercial
        'MIN_SIZE_ENTERED' => 'XX99XX_FBM2766',
        'MAX_SIZE_ENTERED' => 'XX99XX_FBM2766',
        'AREA_SIZE_UNIT_ID' => 'XX99XX_FBM2766',
        'BUSINESS_FOR_SALE_FLAG' => 'XX99XX_FBM2766',
        'PRICE_PER_UNIT' => 'XX99XX_FBM2766',
        'COMM_CLASS_ORDER_1' => 'A1',
        'COMM_CLASS_ORDER_2' => 'A2',

        // media
        'MEDIA_IMAGE_00' => 'XX99XX_FBM2766_IMG_00.jpg',
        'MEDIA_IMAGE_TEXT_00' => 'caption',
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("create_column_definition_test");
        $this->info("Creating test files");

        $inFile = __DIR__.'/../../../tests/files/column_definitions/check-columns.blm';
        $this->info("inFile = '{$inFile}'");

        $template = file_get_contents($inFile);
        $targetFile = __DIR__.'/../../../tests/files/column_definitions/check';

        $blm = new BlmFile;
        $columns = $blm->selectVersionColumnDefinitions();
        // dd($columns);
        // $definition = implode("^", array_keys($columns));
        // $contents = str_replace('{{DEFINITION}}', $definition, $template);

        foreach($columns as $name => $definition) {
            // $this->info("        '{$name}' => 'XX99XX_FBM2766',");
            // continue;
            $data = $this->data;
            if (! isset($data[$name])) {
                $this->info("NOT SET '{$name}'");
                continue;
            }
            unset($data[$name]);

            $keys = array_keys($data);
            $data = array_values($data);

            $defs = implode("^", $keys);
            $data = implode("^", $data);

            $contents = str_replace('{{DEFINITION}}', $defs, $template);
            $contents = str_replace('{{DATA}}', $data, $contents);

            $outFile = $targetFile.'-'.$name;
            if ($definition['recursive']) {
                $outFile .= '_01';
            }
            $outFile .= '.blm';

            $this->info("Creating test file '".basename($outFile)."'");
            file_put_contents($outFile, $contents);

        }

        $this->info("FINISHED");
    }
}
