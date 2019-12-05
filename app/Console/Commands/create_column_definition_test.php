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

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

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

        $contents = file_get_contents($inFile);
        $targetFile = __DIR__.'/../../../tests/files/column_definitions/check';

        $blm = new BlmFile;
        $columns = $blm->getAllColumnDefinitions();

        foreach($columns as $name => $dummy) {
            $outFile = $targetFile.'-'.$name.'.blm';
            $replace = str_replace($name, $name.'xxx', $contents);
            
            $this->info("Creating test file '{$outFile}'");
            file_put_contents($outFile, $replace);

        }

        $this->info("FINISHED");
    }
}
