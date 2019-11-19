<?php

namespace Src\BlmFile;

use Log;

class BlmFile {
    protected $resource = null;
    protected $formatDate = 'Y-m-d H:i:s';

    protected $header = [
        'Version' => "",
        'EOF' => '^',
        'EOR' => '~',
        'Property Count' => 0,
        'Generated Date' => '',
    ];
    protected $sections = [
        'HEADER' => '#HEADER#',
        'DEFINITION' => '#DEFINITION#',
        'DATA' => '#DATA#',
        'END' => '#END#'
    ];
    protected $headerMandatory = [
        'Version',
        'EOF',
        'EOR',
        'Property Count',
        'Generated Date'
    ];
    protected $maxHeaderLines = 25;
    protected $columns = [];
    protected $columnDefinition = [
        // "xAGENT_REF" => 'string:mandatory:nullable',

        "AGENT_REF" => 'string:20:mandatory:mandatory',
        "BRANCH_ID" => 'int:mandatory:mandatory', // provided by Rightmove
        "STATUS_ID" => 'int:mandatory:mandatory', // 0 = Available
        // 1 = SSTC
        // 2 = SSTCM - Scotland
        // 3 = under offer - sales
        // 4 = reserved - sales
        // 5 = let agreed - letting
        
        "CREATE_DATE" => 'date:mandatory:nullable', // YYYY-MM-DD HH:MI:SS
        "UPDATE_DATE" => 'date:mandatory:nullable', // YYYY-MM-DD HH:MI:SS
    ];

    protected $columnDefinitionV3 = [
        "DISPLAY_ADDRESS" => 'string:120:mandatory:mandatory',
        "PUBLISHED_FLAG" => 'int:mandatory:mandatory', // 0 = hidden/invisible 1 = visible

        "LET_DATE_AVAILABLE" => 'date:nullable:nullable', // date:mandatory:nullable
        "LET_BOND" => 'num:nullable:nullable', // deposit amount 
        "ADMINISTRATION_FEE" => 'string:4096:nullable:nullable', // all fees applicable to the property
        "LET_TYPE_ID" => 'num:nullable:nullable', // mandatory
        // 0 = not specified
        // 1 = long term
        // 2 = short term
        // 3 = student
        // 4 = commercial

        "LET_FURN_ID" => 'int:mandatory:nullable', // 
        // 0 = furnished
        // 1 = part furnished
        // 2 = unfurnished
        // 3 = not specified
        // 4 = furnished / unfurnished ???

        "LET_RENT_FREQUENCY" => 'int:mandatory:nullable', //
        // 0 = weekly
        // 1 = monthly - default if null
        // 2 = quarterly
        // 3 = annual
        // 4 =
        // 5 = per-person per-week - students

        "LET_CONTRACT_IN_MONTHS" => 'int:2:nullable:nullable', // student
        "LET_WASHING_MACHINE_FLAG" => 'string:1:nullable:nullable', // Y/N student
        "LET_DISHWASHER_FLAG" => 'string:1:nullable:nullable', // Y/N student
        "LET_BURGLAR_ALARM_FLAG" => 'string:1:nullable:nullable', // Y/N student
        "LET_BILL_INC_WATER" => 'string:1:nullable:nullable', // Y/N student
        "LET_BILL_INC_GAS" => 'string:1:nullable:nullable', // Y/N student
        "LET_BILL_INC_ELECTRICITY" => 'string:1:nullable:nullable', // Y/N student
        "LET_BILL_INC_TV_LICIENCE" => 'string:1:nullable:nullable', // Y/N student
        "LET_BILL_INC_TV_SUBSCRIPTION" => 'string:1:nullable:nullable', // Y/N student
        "LET_BILL_INC_INTERNET" => 'string:1:nullable:nullable', // Y/N student
        
        "TENURE_TYPE_ID" => 'int:nullable:nullable', // mandatory
        "TRANS_TYPE_ID" => 'int:nullable:mandatory', // 1 = resale, 2 = lettings        

        "BEDROOMS" => 'int:mandatory:mandatory',
        "PRICE" => 'num:mandatory:mandatory',
        "PRICE_QUALIFIER" => 'string:mandatory:nullable',
        "PROP_SUB_ID" => 'int:mandatory:mandatory',

        "ADDRESS_1" => 'string:60:mandatory:mandatory',
        "ADDRESS_2" => 'string:60:mandatory:mandatory',
        "ADDRESS_3" => 'string:60:nullable:nullable',
        "ADDRESS_4" => 'string:60:nullable:nullable',
        "TOWN" => 'string:60:mandatory:mandatory',
        "POSTCODE1" => 'string:10:mandatory:mandatory',
        "POSTCODE2" => 'string:10:mandatory:mandatory',

        "FEATURE1" => 'string:200:mandatory:mandatory',
        "FEATURE2" => 'string:200:mandatory:mandatory',
        "FEATURE3" => 'string:200:mandatory:mandatory',
        "FEATURE4" => 'string:200:optional:optional',
        "FEATURE5" => 'string:200:optional:optional',
        "FEATURE6" => 'string:200:optional:optional',
        "FEATURE7" => 'string:200:optional:optional',
        "FEATURE8" => 'string:200:optional:optional',
        "FEATURE9" => 'string:200:optional:optional',
        "FEATURE10" => 'string:200:optional:optional',
        
        "SUMMARY" => 'string:1024:mandatory:mandatory',
        "DESCRIPTION" => 'string:32768:mandatory:mandatory',

        "NEW_HOME_FLAG" => 'string:1:mandatory:nullable', // Y / N

        "MEDIA_IMAGE_00" => 'string:100:mandatory:nullable',
        "MEDIA_IMAGE_TEXT_00" => 'string:20::nullable:nullable',

        "MEDIA_FLOOR_PLAN_00" => 'string:100:nullable:nullable',
        "MEDIA_FLOOR_PLAN_00" => 'string:100:nullable:nullable',
        "MEDIA_FLOOR_PLAN_TEXT_00" => 'string:20:nullable:nullable',

        "MEDIA_DOCUMENT_00" => 'string:200:nullable:nullable',
        "MEDIA_DOCUMENT_TEXT_00" => 'string:20:nullable:nullable',

        "MEDIA_VIRTUAL_TOUR_00" => 'string:200:nullable:nullable',
        "MEDIA_VIRTUAL_TOUR_TEXT_00" => 'string:20:nullable:nullable',
    ];
        
    protected $columnDefinitionV3i = [
        "HOUSE_NAME_NUMBER" => 'string:60:mandatory:mandatory',
        "STREET_NAME", 'string:100:mandatory:mandatory',
        "OS_TOWN_CITY" => 'string:100:mandatory:mandatory',
        "OS_REGION" => 'string:100:mandatory:mandatory',
        "ZIPCODE" => 'string:100:nullable:nullable',
        "COUNTRY_CODE" => 'string:2:mandatory:mandatory',
        "EXACT_LATITUDE" => 'num:15:mandatory:mandatory',
        "EXACT_LONGDITUDE" => 'num:15:mandatory:mandatory',
    ];

    protected $errors = [];

    public function __construct()
    {
        for ($i = 1; $i < 70; $i++) { // temp work around
            $this->columnDefinition['MEDIA_IMAGE_'.sprintf('%02d', $i)] = 'string:100:nullable:nullable';
            $this->columnDefinition['MEDIA_IMAGE_TEXT_'.sprintf('%02d', $i)] = 'string:20::nullable:nullable';

            $this->columnDefinition['MEDIA_FLOOR_PLAN_'.sprintf('%02d', $i)] = 'string:100:nullable:nullable';
            $this->columnDefinition['MEDIA_FLOOR_PLAN_TEXT_'.sprintf('%02d', $i)] = 'string:20::nullable:nullable';

            $this->columnDefinition['MEDIA_DOCUMENT_'.sprintf('%02d', $i)] = 'string:200:nullable:nullable';
            $this->columnDefinition['MEDIA_DOCUMENT_TEXT_'.sprintf('%02d', $i)] = 'string:20::nullable:nullable';
            
            $this->columnDefinition['MEDIA_VIRTUAL_TOUR_'.sprintf('%02d', $i)] = 'string:200:nullable:nullable';
            $this->columnDefinition['MEDIA_VIRTUAL_TOUR_TEXT_'.sprintf('%02d', $i)] = 'string:20::nullable:nullable';
            
        }
    }

    public function __set($name, $value)
    {
        if (! isset($this->header[$name]) ) {
            throw new \Exception("Error: Unknown Blm variable '{$name}'");
        }
        
        switch ($name) {
            case 'EOF':
            case 'EOR': $this->header[$name] = trim($value, "'");
                break;
            default: $this->header[$name] = $value;
        }
    }

    public function __get($name)
    {
        if (! isset($this->header[$name]) ) {
            throw new \Exception("Error: Unknown Blm variable '{$name}'");
        }

        return $this->header[$name];
    }

    /**
     * @param $resource
     * @return this
     */
    public function setup($resource)
    {
        if (! is_resource($resource)) {
            throw new \Exception('Error: Not A file Resource');
        }

        $this->Version = "3";
        $this->EOF = '^';
        $this->EOR = '~';
        $this->{'Property Count'} = 0;
        $this->{'Generated Date'} = Date($this->formatDate);

        $this->resource = $resource;

        $this->readHeader();
        $this->readDefinition();

        return $this;
    }

    protected function readHeader()
    {
        $str = $this->readLine();

        if ($this->sections['HEADER'] !== trim($str)) {
            throw new \Exception("Error: Not a valid BLM file, Header '{$this->sections['HEADER']}' must be 1sr line");
        }

        $count = $this->maxHeaderLines;
        while ($str = $this->readLine()) {
            $count -= 1;
            if ($count < 1) {
                throw new \Exception("Error: Not a valid BLM file, Too many header items, attempting to read more than '{$this->maxHeaderLines}' values");
            }
            if (! preg_match("/^([A-Za-z 0-9]+) *:(.*)$/", trim($str), $matches)) {
                throw new \Exception("Error: Not a valid BLM file, header item '{$name}' missing value");
            }  
            
            $name = trim($matches[1]);
            $value = trim($matches[2]);      
            if (! $this->validateHeaderItem($name, $value)) {
                throw new \Exception("Error: Not a valid BLM file, invalid header item '{$name}' failed with value '{$value}'");
            }

            $this->{$name} = $value;
        }

        $this->checkHeader();
    }

    protected function checkHeader()
    {
        $diff = array_diff($this->headerMandatory, array_keys($this->header));
        if ($diff) {
            throw new \Exception("Error: Not a valid BLM file, invalid header, missing item(s) '".implode("', '", $diff)."' ");
        }

        return $this->selectVersionColumnDefinitions();
    }

    protected function selectVersionColumnDefinitions()
    {
        switch ($this->{'Version'}) {
            case '3': $tmp = $this->columnDefinitionV3;
                break;
            case '3i': $tmp = $this->columnDefinitionV3i;
                break;
            default: 
                throw new \Exception("Error: Not a valid BLM file, Unknown version '".$this->{'Version'}."' ");
        }
        $this->columnDefinition = array_merge($this->columnDefinition, $tmp);

        return $this;
    }

    protected function readDefinition()
    {
        $str = $this->skip();

        if ($this->sections['DEFINITION'] !== $str) {
            throw new \Exception('Error: Not a valid BLM file, definition missing');
        }

        $str = $this->readLine();
        if (! preg_match("#^[A-Z_0-9\\".$this->EOF."]+\\".$this->EOR."$#",$str)) {
            throw new \Exception("Error: Not a valid BLM file, definition incorrect found '{$str}'");
        }

        $this->validateDefinition($str);

        return $this;
    }

    public function readData() : Array
    {
        throw new \Exception('Error: Not Implemented yet');

        $str = $this->readLine();
        if ($this->sections['DATA'] !== trim($str)) {
            throw new \Exception('Eror: Not a valid BLM file, definition missing');
        }

        // while ($str = $this->readDataLine()) {
            // yield
        // }

        // return [];
    }

    /**
     * read a line from HEADER/ DEFINITION
     * @return string
     */
    protected function readLine()
    {
        return trim(fgets($this->resource));
    }

    /**
     * Return next non empty line
     * @return String
     */
    protected function skip() : String
    {
        $str = $this->readLine();
        while ('' === $str) {
            $str = $this->readLine();
        }

        return $str;
    }

    protected function readHeaderItem(String $name)
    {
        $str = $this->readLine();
        if (! preg_match("/^{$name} *:.*$/", $str)) {
            throw new \Exception("Error: Not a valid BLM file, header item '{$name}' missing");
        }

        if (! preg_match("/^{$name} *:(.*)$/", trim($str), $matches)) {
            throw new \Exception("Error: Not a valid BLM file, header item '{$name}' missing value");
        }

        $value = trim($matches[1]);

        if (! $this->validateHeaderItem($name, $value)) {
            throw new \Exception("Error: Not a valid BLM file, invalid header item '{$name}' failed with value '{$value}'");
        }

        $this->{$name} = $value;

        return $this;
    }

    protected function validateHeaderItem(String $name, String $value)
    {
        switch ($name) {
            case 'Version': return \in_array($value, ['3', '3i']);
            case 'EOF': return preg_match("/^'.'$/", $value);
            case 'EOR': return preg_match("/^'.'$/", $value);
            case 'Property Count': return $this->isInt($value);
            case 'Generated Date': return $this->isDate($value);
            default: return false;
        }
    }

    protected function validateDefinition(String $str)
    {
        $str = trim($str);
        $str = trim($str, $this->EOF.$this->EOR);
        $columns = explode($this->EOF, $str);
        foreach ($columns as $column) {
            $this->validateColumn($column);
        }

        $this->columns = $columns;
        $this->validateMandatoryColumns();

    }

    protected function validateColumn(String $column)
    {
        if (! in_array($column, array_keys($this->columnDefinition))) {
            throw new \Exception("Error: Not a valid BLM file, Unexpected column name '{$column}' ");
        }

    }

    protected function validateMandatoryColumns()
    {
        $mandatory = array_filter($this->columnDefinition, function($column) {
            return strpos($column, ':mandatory') !== false;
        });

        $mandatory = array_keys($mandatory);
        $columns = array_values($this->columns);

        $diff = array_diff($mandatory, $columns);
        if ($diff) {
            throw new \Exception("Error: Not a valid BLM file, Mandatory column(s) '".implode("', '", $diff)."' missing");
        }
    }

    protected function readDataLine()
    {
        //
    }

    protected function isDate(String $value)
    {
        return Date($this->formatDate, strtotime($value)) === $value;
    }
    protected function isInt(String $value)
    {
        $int = strval($value);
        return ctype_digit($int) && ($int >= 0);
    }

}
