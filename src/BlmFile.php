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

        "AGENT_REF" => 'string:20:mandatory:filled',
        "BRANCH_ID" => 'int:mandatory:filled', // provided by Rightmove
        "STATUS_ID" => 'int:mandatory:filled', // 0 = Available
        // 1 = SSTC
        // 2 = SSTCM - Scotland
        // 3 = under offer - sales
        // 4 = reserved - sales
        // 5 = let agreed - letting
        
        "CREATE_DATE" => 'date:mandatory:nullable', // YYYY-MM-DD HH:MI:SS
        "UPDATE_DATE" => 'date:mandatory:nullable', // YYYY-MM-DD HH:MI:SS
    ];

    protected $columnDefinitionV3 = [
        "DISPLAY_ADDRESS" => 'string:120:mandatory:filled',
        "PUBLISHED_FLAG" => 'int:mandatory:filled', // 0 = hidden/invisible 1 = visible

        "LET_DATE_AVAILABLE" => 'date:optional:nullable', // date:mandatory:nullable
        "LET_BOND" => 'num:optional:nullable', // deposit amount 
        "ADMINISTRATION_FEE" => 'string:4096:optional:nullable', // all fees applicable to the property
        "LET_TYPE_ID" => 'num:optional:nullable', // mandatory
        // 0 = not specified
        // 1 = long term
        // 2 = short term
        // 3 = student
        // 4 = commercial

        "LET_FURN_ID" => 'int:1:mandatory:nullable', // 
        // 0 = furnished
        // 1 = part furnished
        // 2 = unfurnished
        // 3 = not specified
        // 4 = furnished / unfurnished ???

        "LET_RENT_FREQUENCY" => 'int:1:mandatory:nullable', //
        // 0 = weekly
        // 1 = monthly - default if null
        // 2 = quarterly
        // 3 = annual
        // 4 =
        // 5 = per-person per-week - students

        "LET_CONTRACT_IN_MONTHS" => 'int:2:optional:nullable', // student
        "LET_WASHING_MACHINE_FLAG" => 'string:1:optional:nullable', // Y/N student
        "LET_DISHWASHER_FLAG" => 'string:1:optional:nullable', // Y/N student
        "LET_BURGLAR_ALARM_FLAG" => 'string:1:optional:nullable', // Y/N student
        "LET_BILL_INC_WATER" => 'string:1:optional:nullable', // Y/N student
        "LET_BILL_INC_GAS" => 'string:1:optional:nullable', // Y/N student
        "LET_BILL_INC_ELECTRICITY" => 'string:1:optional:nullable', // Y/N student
        "LET_BILL_INC_TV_LICIENCE" => 'string:1:optional:nullable', // Y/N student
        "LET_BILL_INC_TV_SUBSCRIPTION" => 'string:1:optional:nullable', // Y/N student
        "LET_BILL_INC_INTERNET" => 'string:1:optional:nullable', // Y/N student
        
        "TENURE_TYPE_ID" => 'int:optional:nullable', // mandatory
        "TRANS_TYPE_ID" => 'int:nullable:mandatory', // 1 = resale, 2 = lettings        

        "BEDROOMS" => 'int:mandatory:filled',
        "PRICE" => 'num:mandatory:filled',
        "PRICE_QUALIFIER" => 'string:mandatory:nullable',
        "PROP_SUB_ID" => 'int:mandatory:filled',

        "ADDRESS_1" => 'string:60:mandatory:filled',
        "ADDRESS_2" => 'string:60:mandatory:filled',
        "ADDRESS_3" => 'string:60:optional:nullable',
        "ADDRESS_4" => 'string:60:optional:nullable',
        "TOWN" => 'string:60:mandatory:filled',
        "POSTCODE1" => 'string:10:mandatory:filled',
        "POSTCODE2" => 'string:10:mandatory:filled',

        "FEATURE1" => 'string:200:mandatory:filled',
        "FEATURE2" => 'string:200:mandatory:filled',
        "FEATURE3" => 'string:200:mandatory:filled',
        "FEATURE4" => 'string:200:optional:optional',
        "FEATURE5" => 'string:200:optional:optional',
        "FEATURE6" => 'string:200:optional:optional',
        "FEATURE7" => 'string:200:optional:optional',
        "FEATURE8" => 'string:200:optional:optional',
        "FEATURE9" => 'string:200:optional:optional',
        "FEATURE10" => 'string:200:optional:optional',
        
        "SUMMARY" => 'string:1024:mandatory:filled',
        "DESCRIPTION" => 'string:32768:mandatory:filled',

        "NEW_HOME_FLAG" => 'string:1:mandatory:nullable', // Y / N

        "MEDIA_IMAGE_00" => 'string:100:mandatory:filled:recursive',
        "MEDIA_IMAGE" => 'string:100:optional:nullable:recursive',
        "MEDIA_IMAGE_TEXT" => 'string:20::optional:nullable:recursive',

        // in spec, but not in test file, coomentbout for now
        // "MEDIA_IMAGE_60" => 'string:20:mandatory:nullable:recursive', // Name of the property EPC graphic. MEDIA_IMAGE_60 is for EPC Graphics that would be shown on site.
        // "MEDIA_IMAGE_TEXT_60" => 'string:3::mandatory:nullable:recursive', // Caption to go with the EPC of MEDIA_IMAGE_60, this MUST READ “EPC”.

        "MEDIA_FLOOR_PLAN" => 'string:100:optional:nullable:recursive',
        "MEDIA_FLOOR_PLAN_TEXT" => 'string:20:optional:nullable:recursive',

        "MEDIA_DOCUMENT" => 'string:200:optional:nullable:recursive',
        "MEDIA_DOCUMENT_TEXT" => 'string:20:optional:nullable:recursive',

        "MEDIA_VIRTUAL_TOUR" => 'string:200:optional:nullable:recursive',
        "MEDIA_VIRTUAL_TOUR_TEXT" => 'string:20:optional:nullable:recursive',
    ];
        
    protected $columnDefinitionV3i = [
        "HOUSE_NAME_NUMBER" => 'string:60:mandatory:filled',
        "STREET_NAME", 'string:100:mandatory:filled',
        "OS_TOWN_CITY" => 'string:100:mandatory:filled',
        "OS_REGION" => 'string:100:mandatory:filled',
        "ZIPCODE" => 'string:100:optional:nullable',
        "COUNTRY_CODE" => 'string:2:mandatory:filled',
        "EXACT_LATITUDE" => 'num:15:mandatory:filled',
        "EXACT_LONGDITUDE" => 'num:15:mandatory:filled',
    ];

    protected $errors = [];

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
            return strpos($columnDefinition, ':mandatory') !== false;
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
    protected function cannonicalColumnName($columnName)
    {
        if ('MEDIA_IMAGE_00' == $columnName) {
            // skip
        } elseif ( preg_match("#^(MEDIA.+)_(\d+)$#", $columnName, $matches)) {
            return $matches[1];
        }

        return $columnName;
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
        $columnName = $this->cannonicalColumnName($columnName);
        if ('MEDIA_IMAGE_00' == $columnName) {
            // skip
        } elseif ( preg_match("#^(.+)_(\d\d)$#", $columnName, $matches)) {
            $name = $matches[1];
            $number = $matches[2];

            return $this->validateDataColumnRecursive($name, $number, $columnValue);
        }
        
        $this->validateDataColumnSingle($columnName, $columnValue);
    }

    protected function validateDataColumnRecursive($name, $number, $columnValue)
    {
        $definition = $this->columnDefinition[$name];
        Log::debug("validateDataColumnRecursive['{$name}', '{$number}', '{$columnValue}', '{$definition}']");

    }

    protected function validateDataColumnSingle(String $name, String $columnValue)
    {
        $definition = $this->columnDefinition[$name];
        Log::debug("validateDataColumnSingle['{$name}', '{$columnValue}', '{$definition}']");

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
