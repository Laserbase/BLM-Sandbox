<?php

namespace Src\BlmFile;

use Log;

class BlmFile {
    protected $resource = null;
    protected $formatDate = 'Y-m-d H:i:s';

    protected $sectionTags = [
        'HEADER' => '#HEADER#',
        'DEFINITION' => '#DEFINITION#',
        'DATA' => '#DATA#',
        'END' => '#END#'
    ];
    protected $header = [
        'Version' => "",
        'EOF' => '^',
        'EOR' => '~',
        'Property Count' => 0,
        'Generated Date' => '',
    ];
    protected $headerMandatory = [
        'Version',
        'EOF',
        'EOR'
    ];
    protected $maxHeaderLines = 25;
    protected $columns = [];
    protected $columnDefinition = [
        // "xAGENT_REF" => 'string:mandatory:nullable',

        "AGENT_REF" => 'string:mandatory:filled:len=20',
        "BRANCH_ID" => 'int:mandatory:filled', // provided by Rightmove
        "STATUS_ID" => 'int:mandatory:filled:len=1', // 
            // 0 = Available
            // 1 = SSTC - Sold Subject To Completion
            // 2 = SSTCM - Scotland - Sold Subject To Concluded Missives
            // 3 = under offer - sales
            // 4 = reserved - sales
            // 5 = let agreed - letting
        
        "CREATE_DATE" => 'date:mandatory:nullable', // YYYY-MM-DD HH:MI:SS
        "UPDATE_DATE" => 'date:mandatory:nullable', // YYYY-MM-DD HH:MI:SS
    ];

    protected $columnDefinitionV3 = [
        "DISPLAY_ADDRESS" => 'string:mandatory:filled:len=120', // Address of the property that should be displayed on the Live Rightmove site
        "PUBLISHED_FLAG" => 'int:mandatory:filled:len=1', // 0 = hidden/invisible 1 = visible

        "LET_DATE_AVAILABLE" => 'date:optional:nullable', // date:mandatory:nullable
        "LET_BOND" => 'num:optional:nullable', // deposit amount 
        "ADMINISTRATION_FEE" => 'string:optional:nullable:len=4096', // all fees applicable to the property
        "LET_TYPE_ID" => 'num:optional:nullable:len=1', // mandatory
            // 0 = not specified DEFAULT
            // 1 = long term
            // 2 = short term
            // 3 = student
            // 4 = commercial

        "LET_FURN_ID" => 'int:mandatory:nullable:len=1', // 
            // 0 = furnished
            // 1 = part furnished
            // 2 = unfurnished
            // 3 = not specified DEFAULT
            // 4 = furnished / unfurnished ???

        "LET_RENT_FREQUENCY" => 'int:mandatory:nullable:len=1', //
            // 0 = weekly
            // 1 = monthly - DEFAULT if null
            // 2 = quarterly
            // 3 = annual
            // 4 =
            // 5 = per-person per-week - students

        "LET_CONTRACT_IN_MONTHS" => 'int:optional:nullable:len=2', // student
        "LET_WASHING_MACHINE_FLAG" => 'string:optional:nullable:len=1', // Y/N student
        "LET_DISHWASHER_FLAG" => 'string:optional:nullable:len=1', // Y/N student
        "LET_BURGLAR_ALARM_FLAG" => 'string:optional:nullable:len=1', // Y/N student
        "LET_BILL_INC_WATER" => 'string:optional:nullable:len=1', // Y/N student
        "LET_BILL_INC_GAS" => 'string:optional:nullable:len=1', // Y/N student
        "LET_BILL_INC_ELECTRICITY" => 'string:optional:nullable:len=1', // Y/N student
        "LET_BILL_INC_TV_LICIENCE" => 'string:optional:nullable:len=1', // Y/N student
        "LET_BILL_INC_TV_SUBSCRIPTION" => 'string:optional:nullable:len=1', // Y/N student
        "LET_BILL_INC_INTERNET" => 'string:optional:nullable:len=1', // Y/N student
        
        "TENURE_TYPE_ID" => 'int:optional:nullable:len=1', // mandatory
        "TRANS_TYPE_ID" => 'int:nullable:mandatory:len=1', // 1 = resale, 2 = lettings        

        "BEDROOMS" => 'int:mandatory:filled',
        "PRICE" => 'num:mandatory:filled',
        "PRICE_QUALIFIER" => 'int:mandatory:nullable',
                /*  0 – Default, 
                    1 – POA, 
                    2 – Guide Price, 
                    3 – Fixed Price, 
                    4 – Offers in Excess of, 
                    5 – OIRO, Offers In The Region Of
                    6 – Sale by Tender, 
                    7 – From (new homes and commercial only), 
                    8 UNKNOWN
                    9 – Shared Ownership, 
                    10 – Offers Over, 
                    11 – Part Buy Part Rent, 
                    12 – Shared Equity, 
                    13 UNKNOWN
                    14 – Equity Loan, 
                    15 – Offers Invited
                **/
        "PROP_SUB_ID" => 'int:mandatory:filled', // One of the valid property types. Ref. Property Type table

        "ADDRESS_1" => 'string:mandatory:filled:len=60',
        "ADDRESS_2" => 'string:mandatory:filled:len=60',
        "ADDRESS_3" => 'string:optional:nullable:len=60',
        "ADDRESS_4" => 'string:optional:nullable:len=60',
        "TOWN" => 'string:mandatory:filled:len=60',
        "POSTCODE1" => 'string:mandatory:filled:len=10',
        "POSTCODE2" => 'string:mandatory:filled:len=10',

        "FEATURE1" => 'string:mandatory:filled:len=200',
        "FEATURE2" => 'string:mandatory:filled:len=200',
        "FEATURE3" => 'string:mandatory:filled:len=200',
        "FEATURE4" => 'string:optional:nullable:len=200',
        "FEATURE5" => 'string:optional:nullable:len=200',
        "FEATURE6" => 'string:optional:nullable:len=200',
        "FEATURE7" => 'string:optional:nullable:len=200',
        "FEATURE8" => 'string:optional:nullable:len=200',
        "FEATURE9" => 'string:optional:nullable:len=200',
        "FEATURE10" => 'string:optional:nullable:len=200',
        
        "SUMMARY" => 'string:mandatory:filled:len=1024',
        "DESCRIPTION" => 'string:mandatory:filled:len=32768',

        "NEW_HOME_FLAG" => 'string:mandatory:nullable:len=1', // Y / N

        "MEDIA_IMAGE_00" => 'string:mandatory:filled:len=100:recursive',
        "MEDIA_IMAGE" => 'string:optional:nullable:len=100:recursive',
        "MEDIA_IMAGE_TEXT" => 'string:optional:nullable:len=20:recursive',

        // in spec, but not in test file, coment out for now
        // "MEDIA_IMAGE_60" => 'string:mandatory:nullable:len=20:recursive', // Name of the property EPC graphic. MEDIA_IMAGE_60 is for EPC Graphics that would be shown on site.
        // "MEDIA_IMAGE_TEXT_60" => 'string:mandatory:nullable:len=3:recursive', // Caption to go with the EPC of MEDIA_IMAGE_60, this MUST READ “EPC”.

        "MEDIA_FLOOR_PLAN" => 'string:optional:nullable:len=100:recursive',
        "MEDIA_FLOOR_PLAN_TEXT" => 'string:optional:nullable:len=20:recursive',

        "MEDIA_DOCUMENT" => 'string:optional:nullable:len=200:recursive',
        "MEDIA_DOCUMENT_TEXT" => 'string:optional:nullable:len=20:recursive',

        "MEDIA_VIRTUAL_TOUR" => 'string:optional:nullable:len=200:recursive',
        "MEDIA_VIRTUAL_TOUR_TEXT" => 'string:optional:nullable:len=20:recursive',
    ];
        
    protected $columnDefinitionV3i = [
        "HOUSE_NAME_NUMBER" => 'string:mandatory:filled:len=60',
        "STREET_NAME", 'string:mandatory:filled:len=100',
        "OS_TOWN_CITY" => 'string:mandatory:filled:len=100',
        "OS_REGION" => 'string:mandatory:filled:len=100',
        "ZIPCODE" => 'string:optional:nullable:len=100',
        "COUNTRY_CODE" => 'string:mandatory:filled:len=2',
        "EXACT_LATITUDE" => 'num:mandatory:filled:len=15',
        "EXACT_LONGDITUDE" => 'num:mandatory:filled:len=15',
    ];

    protected $errors = [];

    public function __construct()
    {
        foreach($this->columnDefinition as $name => $definitionString) {
            $this->columnDefinition[$name] = $this->definition($name, $definitionString);
        }
        foreach($this->columnDefinitionV3 as $name => $definitionString) {
            $this->columnDefinitionV3[$name] = $this->definition($name, $definitionString);
        }
        foreach($this->columnDefinitionV3i as $name => $definitionString) {
            $this->columnDefinitionV3i[$name] = $this->definition($name, $definitionString);
        }
    }
    protected function definition(String $columnName, String $definitionString)
    {
        $name = $columnName;
        if ('MEDIA_IMAGE_00' == $columnName) {
            // skip
        } elseif ( preg_match("#^(.+)_(\d\d)$#", $columnName, $matches)) {
            $name = $matches[1];
        }

        $def = explode(':', $definitionString);

        $result = [];
        $result['type'] = array_shift($def);
        $result['mandatory'] = ('mandatory' === array_shift($def));
        $result['required'] = ('filled' === array_shift($def));
        $result['recursive'] = false;
        $result['len'] = ($result['required']) ? 1 : 0;

        foreach($def as $item) {
            if ('recursive' == $item ) {
                $result['recursive'] = true;
                continue;
            }
            if ('len' == substr($item, 0, 3)) {
                $result['len'] = substr($item, 4);
            }
        }
        // Log::debug("\$columnName[{$columnName}]=".print_r($result,true));

        return $result;
    }

    protected function cannonicalColumnName($columnName)
    {
        if ('MEDIA_IMAGE_00' == $columnName) {
            // skip
        } elseif ( preg_match("#^(MEDIA.+)_(\d+)$#", $columnName, $matches)) {
            return $matches[1];
        }

        return $columnName;
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

        if ($this->sectionTags['HEADER'] !== trim($str)) {
            throw new \Exception("Error: Not a valid BLM file, Header '{$this->sectionTags['HEADER']}' must be 1st line");
        }

        $count = $this->maxHeaderLines;
        while ($str = $this->readLine()) {
            if ($this->sectionTags['DEFINITION'] === $str) {
                break;
            }

            $count -= 1;
            if ($count < 1) {
                throw new \Exception("Error: Not a valid BLM file, Too many header items, attempting to read more than '{$this->maxHeaderLines}' values");
            }
            if (! preg_match("/^([A-Z 0-9]+) *: *(.*)$/i", trim($str), $matches)) {
                throw new \Exception("Error: Not a valid BLM file, header item '{$dummy}' missing value");
            }
            if (count($matches) <> 3) {
                dd($str);
            }
            
            $name = trim($matches[1]);
            $value = trim($matches[2]);      
            if (! $this->validateHeaderItem($name, $value)) {
                throw new \Exception("Error: Not a valid BLM file, invalid header item '{$name}' failed with value '{$value}'");
            }

            $this->{$name} = $value;
        }

        return $this->checkHeader();
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
        $str = $this->readLine();
        if (! preg_match("#^[A-Z_0-9\\".$this->EOF."]+\\".$this->EOR."$#",$str)) {
            throw new \Exception("Error: Not a valid BLM file, definition incorrect found '{$str}'");
        }

        $this->validateDefinition($str);

        return $this->checkDataSection();
    }

    protected function checkDataSection()
    {
        $str = $this->readLine();
        if ($this->sectionTags['DATA'] !== trim($str)) {
            throw new \Exception('Error: Not a valid BLM file, definition missing');
        }

        return $this;
    }

    /**
     * Return next non empty line
     * @return String
     */
    protected function readLine() : String
    {
        $str = '';
        while ('' === $str) {
            $str = trim(fgets($this->resource));
        }

        return $str;
    }

    /**
     * 
     */
    protected function readHeaderItem(String $name)
    {
        $str = $this->readContentLine();
        if (! preg_match("/^{$name} *:.*$/", $str)) {
            throw new \Exception("Error: Not a valid BLM file, header item '{$name}' missing '{$str}'");
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
        Log::debug(__LINE__.", '{$name}' = '{$value}' ");
        // mandatory
        switch ($name) {
            case 'Version': return in_array($value, ['3', '3i']);
            case 'EOF': return preg_match("/^'.'$/", $value);
            case 'EOR': return preg_match("/^'.'$/", $value);
        }

        // Optional
        if ($value == '') {
            return true;
        }
        switch ($name) {
            case 'Property Count': return ($value == '') ? true : $this->isInt($value);
            case 'Generated Date': return ($value == '') ? true : $this->isDate($value);
        }

        // feed supplier
        return true;
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
        $this->validateDataSeparators();
        $this->validateMandatoryColumns();

    }

    protected function validateColumn(String $column)
    {
        if (! in_array($this->cannonicalColumnName($column), array_keys($this->columnDefinition))) {
            throw new \Exception("Error: Not a valid BLM file, Unexpected column name '{$column}' ");
        }

    }

    protected function validateDataSeparators()
    {
        if ($this->header['EOF'] == $this->header['EOR'] ) {
            throw new \Exception("Error: Not a valid BLM file, EndOfField character '{$this->header['EOF']}' must be different than EndOfRecord character '{$this->header['EOR']}'");
        }
        if (strlen($this->header['EOF']) != 1) {
            throw new \Exception("Error: '".strlen($this->header['EOF'])."' Not a valid BLM file, EndOfField character '{$this->header['EOF']}' must be a singe character, default '^' ");
        }
        if (strlen($this->header['EOR']) != 1) {
            throw new \Exception("Error: Not a valid BLM file, EndOfRecord character '{$this->header['EOR']}' must be a singe character, default '~' ");
        }

    }

    protected function validateMandatoryColumns()
    {
        $mandatory = array_filter($this->columnDefinition, function($columnDefinition) {
            return $columnDefinition['mandatory'];
        });

        $mandatory = array_keys($mandatory);
        $columns = array_map(function ($column) {
            return $this->cannonicalColumnName($column);
        }, $this->columns);

        $diff = array_diff($mandatory, $columns);
        if ($diff) {
            throw new \Exception("Error: Not a valid BLM file, Mandatory column(s) '".implode("', '", $diff)."' missing");
        }
    }

    public function readData()
    {
        $count = 0;
        $str = $this->readLine();
        while ($str) {
            if ($str === $this->sectionTags['END']) {
                break;
            }
            if ('' === $str) {
                continue;
            }
            $count += 1;

            if ($this->EOF.$this->EOR !== substr($str, -2)) {
                throw new \Exception("Error: Not a valid BLM file, EndOfRecord character '{$this->EOR}' missing from end of line, found '".substr($str, -2)."' ");
            }

            yield $this->validateData($str);

            $str = $this->readLine();
        }

        if ($count != $this->{'Property Count'}) {
            Log::debug("Warning: Expected '".$this->{'Property Count'}."' properties in file, found '{$count}'");
        }
    }

    protected function validateData(String $str) : Array
    {
        $values = $this->extractRowValues($str);
 
        $keys = array_values($this->columns);
        $row = array_combine($keys, $values);

        return $this->validateDataRow($row);
    }

    protected function extractRowValues(String $str) : Array
    {
        $str = trim($str, $this->EOR);
        $str = trim($str);

        $values = explode($this->EOF, $str);

        // The final field should be finished with the EOF delimiter and then EOR delimiter.
        //  Which means that there is a blank slot that needs to be accounted for
        $dummy = array_pop($values);
        $count_values = count($values);
        $count_columns = count($this->columns);
        if ($count_values !== $count_columns) {
            throw new \Exception("Error: Not a valid BLM file, The number of row fields '{$count_values}' is different to the number expected'{$count_columns}'");
        }

        return $values;
    }

    protected function validateDataRow(Array $row)
    {
        foreach($row as $columnName => $columnValue) {
            $this->validateDataColumn($columnName, $columnValue);
        }

        return $row;
    }

    protected function validateDataColumn($columnName, $columnValue)
    {
        $definition = $this->columnDefinition[ $this->cannonicalColumnName($columnName)];

        // is data optional?
        if (($definition['required']) && ('' == $columnValue)) {
            throw new \Exception("Error: Not a valid BLM file, Data field '{$columnName}' missing expected value");
        }


    }

    protected function isDate(String $value)
    {
        $date = Date($this->formatDate, strtotime($value));
        return Date($this->formatDate, strtotime($value)) === $value;
    }
    protected function isInt(String $value)
    {
        $int = strval($value);
        return ctype_digit($int) && ($int >= 0);
    }

}
