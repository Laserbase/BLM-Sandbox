<?php

namespace Src\BlmFile;

use Log;

class BlmFile {
    protected $formatDate = 'Y-m-d H:i:s';
    protected $maxHeaderLines = 25;
    protected $resource = null;

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
    protected $headerRequired = [
        'Version',
        'EOF',
        'EOR'
    ];

    protected $columnKeys = [];
    protected $columnDefinitions = [];
    protected $columnDefinitionMaster = [
        // 'type|required|min:0|max:10:recursive'
        //      type = string | date | int | num
        //      required = column must be in data feed
        // min:0 = value is optional
        //      otherwise value must be given
        // max:n = maximum length in characters for a column value
        // recursive = field name can be repeated
        //       with an underscore followed by an index number

        "AGENT_REF" => 'string|required|min:1|max:20',
        "BRANCH_ID" => 'int|required|min:1|max:10', // provided by Rightmove
        "STATUS_ID" => 'int|required|min:1|max:1', //
            // 0 = Available
            // 1 = SSTC - Sold Subject To Completion
            // 2 = SSTCM - Scotland - Sold Subject To Concluded Missives
            // 3 = under offer - sales
            // 4 = reserved - sales
            // 5 = let agreed - letting

        "CREATE_DATE" => 'date|required|min:0', // YYYY-MM-DD HH:MI:SS
        "UPDATE_DATE" => 'date|required|min:0', // YYYY-MM-DD HH:MI:SS
    ];

    protected $columnDefinitionV3 = [
        "DISPLAY_ADDRESS" => 'string|required|min:1|max:120', // Address of the property that should be displayed on the Live Rightmove site
        "PUBLISHED_FLAG" => 'int|required|min:1|max:1', // 0 = hidden/invisible 1 = visible

        "LET_DATE_AVAILABLE" => 'date|min:0|min:0', // date|required|min:0
        "LET_BOND" => 'num|min:0', // deposit amount
        "ADMINISTRATION_FEE" => 'string|min:0|max:4096', // all fees applicable to the property
        "LET_TYPE_ID" => 'num|min:0|max:1', // required
            // 0 = not specified DEFAULT
            // 1 = long term
            // 2 = short term
            // 3 = student
            // 4 = commercial

        "LET_FURN_ID" => 'int|required|min:0|max:1', //
            // 0 = furnished
            // 1 = part furnished
            // 2 = unfurnished
            // 3 = not specified DEFAULT
            // 4 = furnished / unfurnished ???

        "LET_RENT_FREQUENCY" => 'int|required|min:0|max:1', //
            // 0 = weekly
            // 1 = monthly - DEFAULT if null
            // 2 = quarterly
            // 3 = annual
            // 4 =
            // 5 = per-person per-week - students

        "LET_CONTRACT_IN_MONTHS" => 'int|min:0|max:2', // student
        "LET_WASHING_MACHINE_FLAG" => 'string|min:0|max:1', // Y/N student
        "LET_DISHWASHER_FLAG" => 'string|min:0|max:1', // Y/N student
        "LET_BURGLAR_ALARM_FLAG" => 'string|min:0|max:1', // Y/N student
        "LET_BILL_INC_WATER" => 'string|min:0|max:1', // Y/N student
        "LET_BILL_INC_GAS" => 'string|min:0|max:1', // Y/N student
        "LET_BILL_INC_ELECTRICITY" => 'string|min:0|max:1', // Y/N student
        "LET_BILL_INC_TV_LICIENCE" => 'string|min:0|max:1', // Y/N student
        "LET_BILL_INC_TV_SUBSCRIPTION" => 'string|min:0|max:1', // Y/N student
        "LET_BILL_INC_INTERNET" => 'string|min:0|max:1', // Y/N student

        "TENURE_TYPE_ID" => 'int|min:0|max:1', // required
        "TRANS_TYPE_ID" => 'int|required|min:1|max:1', // 1 = resale, 2 = lettings

        "BEDROOMS" => 'int|required|min:1',
        "PRICE" => 'num|required|min:1',
        "PRICE_QUALIFIER" => 'int|required|min:0',
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
        "PROP_SUB_ID" => 'int|required|min:1', // One of the valid property types. Ref. Property Type table

        "ADDRESS_1" => 'string|required|min:1|max:60',
        "ADDRESS_2" => 'string|required|min:1|max:60',
        "ADDRESS_3" => 'string|min:0|max:60',
        "ADDRESS_4" => 'string|min:0|max:60',
        "TOWN" => 'string|required|min:1|max:60',
        "POSTCODE1" => 'string|required|min:1|max:10',
        "POSTCODE2" => 'string|required|min:1|max:10',

        "FEATURE1" => 'string|required|min:1|max:200',
        "FEATURE2" => 'string|required|min:1|max:200',
        "FEATURE3" => 'string|required|min:1|max:200',
        "FEATURE4" => 'string|min:0|max:200',
        "FEATURE5" => 'string|min:0|max:200',
        "FEATURE6" => 'string|min:0|max:200',
        "FEATURE7" => 'string|min:0|max:200',
        "FEATURE8" => 'string|min:0|max:200',
        "FEATURE9" => 'string|min:0|max:200',
        "FEATURE10" => 'string|min:0|max:200',

        "SUMMARY" => 'string|required|min:1|max:1024', // ALL HTML will be stripped page 15 of pdf
        "DESCRIPTION" => 'string|required|min:1|max:32768', // Basic HTML tags can be used for bold, underlining, italicising page 15 of pdf

        "NEW_HOME_FLAG" => 'string|required|min:0|max:1', // Y / N or empty

        // All links to Floor plans, Brochures and Virtual Tours must only link to the physical media 
        //  and not to a webpage consisting of the media and external links.
        "MEDIA_IMAGE_00" => 'string|required|min:1|max:100',
        "MEDIA_IMAGE" => 'string|min:0|max:100|recursive',
        "MEDIA_IMAGE_TEXT" => 'string|min:0|max:20|recursive',

        // in spec, but not in test file, coment out for now
        // "MEDIA_IMAGE_60" => 'string|required|min:0|max:20|recursive', // Name of the property EPC graphic. MEDIA_IMAGE_60 is for EPC Graphics that would be shown on site.
        // "MEDIA_IMAGE_TEXT_60" => 'string|required|min:0|max:3|recursive', // Caption to go with the EPC of MEDIA_IMAGE_60, this MUST READ “EPC”.

        "MEDIA_FLOOR_PLAN" => 'string|min:0|max:100|recursive',
        "MEDIA_FLOOR_PLAN_TEXT" => 'string|min:0|max:20|recursive',

        "MEDIA_DOCUMENT" => 'string|min:0|max:200|recursive',
        "MEDIA_DOCUMENT_TEXT" => 'string|min:0|max:20|recursive',

        "MEDIA_VIRTUAL_TOUR" => 'string|min:0|max:200|recursive',
        "MEDIA_VIRTUAL_TOUR_TEXT" => 'string|min:0|max:20|recursive',
    ];

    protected $columnDefinitionV3i = [
        "HOUSE_NAME_NUMBER" => 'string|required|min:1|max:60',
        "STREET_NAME", 'string|required|min:1|max:100',
        "OS_TOWN_CITY" => 'string|required|min:1|max:100',
        "OS_REGION" => 'string|required|min:1|max:100',
        "ZIPCODE" => 'string|min:0|max:100',
        "COUNTRY_CODE" => 'string|required|min:1|max:2',
        "EXACT_LATITUDE" => 'num|required|min:1|max:15',
        "EXACT_LONGDITUDE" => 'num|required|min:1|max:15',
    ];

    public function __construct()
    {
        $this->setupDefinitions();
    }

    /**
     * set column definitions from hard coded string
     */   
    protected function setupDefinitions()
    {
        foreach($this->columnDefinitionMaster as $name => $definitionString) {
            $this->columnDefinitionMaster[$name] = $this->stringToDefinition($name, $definitionString);
        }
        foreach($this->columnDefinitionV3 as $name => $definitionString) {
            $this->columnDefinitionV3[$name] = $this->stringToDefinition($name, $definitionString);
        }
        foreach($this->columnDefinitionV3i as $name => $definitionString) {
            $this->columnDefinitionV3i[$name] = $this->stringToDefinition($name, $definitionString);
        }        
    }

    /**
     * transform $definitionString into an associated array
     * @param String $columnName column defined in spec
     * @param String $definitionString hard coded column definition
     * @return Array column definitions
     */
    protected function stringToDefinition(String $columnName, String $definitionString)
    {
        $name = $columnName;
        if ('MEDIA_IMAGE_00' == $columnName) {
            // skip
        } elseif ( preg_match("#^(.+)_(\d\d)$#", $columnName, $matches)) {
            $name = $matches[1];
        }

        $def = explode('|', $definitionString);

        $result = [];
        $result['type'] = array_shift($def);
        $result['required'] = false;
        $result['recursive'] = false;
        $result['min'] = 0;
        $result['max'] = 4096;

        foreach($def as $item) {
            if ('required' == $item ) {
                $result['required'] = true;
                continue;
            }
            if ('recursive' == $item ) {
                $result['recursive'] = true;
                continue;
            }

            $type = substr($item, 0, 3);
            $size = substr($item, 4);
            if ($type === 'min') {
                $result['min'] = (int) $size;
            }
            if ($type === 'max') {
                $result['max'] = (int) $size;
            }
        
        }

        return $result;
    }

    /**
     * return column name, strip suffix if column is recursive
     * @param String $columnName
     * @return String
     */
    protected function cannonicalColumnName(String $columnName)
    {
        if ('MEDIA_IMAGE_00' == $columnName) {
            // skip
        } elseif ( preg_match("#^(MEDIA.+)_(\d+)$#", $columnName, $matches)) {
            return $matches[1];
        }

        return $columnName;
    }

    /**
     * set header parameter
     * @return void
     */
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

    /**
     * get header parameter
     */
    public function __get($name)
    {
        if (! isset($this->header[$name]) ) {
            throw new \Exception("Error: Unknown Blm variable '{$name}'");
        }

        return $this->header[$name];
    }

    /**
     * return column definitions for the specified version
     * 
     * @param String $version default '3'
     * @return Array of column definitions
     */
    protected function getAllColumnDefinitions(String $version = '3')
    {
        $result = [];

        foreach($this->columnDefinitionMaster as $name => $definition) {
            $result[$name] = $this->columnDefinitionMaster[$name];
        }

        switch ($version) {
            case '3':
                foreach($this->columnDefinitionV3 as $name => $definition) {
                    $result[$name] = $this->columnDefinitionV3[$name];
                }
            break;

            case '3i':
                foreach($this->columnDefinitionV3i as $name => $definition) {
                    $result[$name] = $this->columnDefinitionV3i[$name];
                }
            break;
        }

        return $result;
    }

    /**
     * given a php resource, read setup info from data file then set and check corresponding definitions
     * @param $resource
     * @return $this
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
        $this->checkHeader();
        $this->checkDataSection();
    }

    /**
     * read header section
     */
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
                throw new \Exception("Error: Not a valid BLM file, header item '{$str}' missing 'name : value' syntax");
            }
            if (count($matches) <> 3) {
                throw new \Exception("Error: Not a valid BLM file, header item '{$str}' incorrect format expecting 'name : value' syntax");
            }

            $name = trim($matches[1]);
            $value = trim($matches[2]);
            if (! $this->validateHeaderItem($name, $value)) {
                throw new \Exception("Error: Not a valid BLM file, invalid header item '{$name}' failed with value '{$value}'");
            }

            $this->{$name} = $value;
        }

    }

    /**
     * check header section
     */
    protected function checkHeader()
    {
        $diff = array_diff($this->headerRequired, array_keys($this->header));
        if ($diff) {
            throw new \Exception("Error: Not a valid BLM file, invalid header, missing item(s) '".implode("', '", $diff)."' ");
        }

        $this->selectVersionColumnDefinitions();

        $str = $this->readDefinition();
        $this->validateDefinition($str);
    }

    /**
     * 1019-12-10 14:33 set public
     * @return Array of column definitions
     */
    public function selectVersionColumnDefinitions()
    {
        return $this->columnDefinitions = $this->getAllColumnDefinitions();
    }

    /**
     * read definition section
     */
    protected function readDefinition() : String
    {
        $str = $this->readLine();
        if (! preg_match("#^[A-Z_0-9\\".$this->EOF."]+\\".$this->EOR."$#",$str)) {
            throw new \Exception("Error: Not a valid BLM file, definition incorrect found '{$str}'");
        }

        return $str;
    }

    /**
     * check data section starts on next line
     * @return Void
     */
    protected function checkDataSection()
    {
        $str = $this->readLine();
        if ($this->sectionTags['DATA'] !== trim($str)) {
            throw new \Exception('Error: Not a valid BLM file, definition missing');
        }

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
     * Return next line containg end of record marker
     *  fields may contain new line characters
     * @return String
     */
    protected function readDataLine() : String
    {
        $prev = '';
        $str = '';
        while ('' === $str) {
            $str = trim(fgets($this->resource));

            if ($str === $this->sectionTags['END']) {
                return $str;
            }

            if (false === strpos($str, $this->EOR)) {
                if ($prev) {
                    $prev .= "\n".$str;
                } else {
                    $prev = $str;
                }

                $str = '';
            }

        }
        if ($prev) {
            $str = $prev."\n".$str;
        }

        return $str;
    }

    /**
     * read header item from string
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
    }

    /**
     * check header item is correct
     * 
     * @param String $name name of the header item
     * @param String $value of the header item
     * @return Bool
     */
    protected function validateHeaderItem(String $name, String $value)
    {
        // required
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

        // feed supplier parameters not checked
        return true;
    }

    /**
     * validate string of field names
     * 2019-12-10 14:33 set public
     * 
     * @param String $str
     * @return Void
     */
    public function validateDefinition(String $str)
    {
        $str = trim($str);
        $str = trim($str, $this->EOF.$this->EOR);

        $columnKeys = explode($this->EOF, $str);
        $this->columnKeys = $columnKeys;

        foreach ($columnKeys as $column) {
            $this->validateColumn($column);
        }

        $this->validateDataSeparators();
        $this->validateRequiredColumns();
    }

    /**
     * check the number of columns matches expected 
     * @param Array $values
     * @return Void
     */
    protected function validateColumnCount(Array $values)
    {
        $count_values = count($values);
        $count_columns = count($this->columnKeys); // todo
        if ($count_values !== $count_columns) {
            throw new \Exception("Error: Not a valid BLM file, The number of row fields '{$count_values}' is different to the number expected '{$count_columns}'");
        }
    }

    /**
     * 
     */
    protected function validateColumn(String $columnName)
    {
        $columnName = $this->cannonicalColumnName($columnName);
        if (false === array_key_exists($columnName, $this->columnDefinitions)) {
            throw new \Exception("Error: Not a valid BLM file, Unexpected column name '{$columnName}' ");
        }

    }

    /**
     * check end-of-field, end-of-record characters updated from header section
     * @return Void
     */
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

    /**
     * check definition for has all required columns
     * @return Void
     */
    protected function validateRequiredColumns()
    {
        $required = array_filter($this->columnDefinitions, function($columnDefinition) {
            return $columnDefinition['required'];
        });

        $required = array_keys($required);
        $columns = array_map(function ($column) {
            return $this->cannonicalColumnName($column);
        }, $this->columnKeys);

        $diff = array_diff($required, $columns);
        if ($diff) {
            throw new \Exception("Error: Not a valid BLM file, Required column(s) '".implode("', '", $diff)."' missing");
        }
    }

    /**
     * read next data row
     */
    public function readData()
    {
        $count = 0;
        $str = $this->readDataLine();
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

            $str = $this->readDataLine();
        }

        if ($count != $this->{'Property Count'}) {
            Log::debug("Warning: Expected '".$this->{'Property Count'}."' properties in file, found '{$count}'");
        }

    }

    /**
     * check data row and return array of columns
     *      2019-12-10 14:33 made public
     * @param String $str
     * @return Array of columns
     */
    public function validateData(String $str) : Array
    {
        // The final field should be finished with the EOF delimiter and then EOR delimiter.
        //  Which means that there is a blank slot that needs to be accounted for
        //  using array_pop() to remove it
        $values = $this->extractRowValues($str);
        array_pop($values);
        $this->validateColumnCount($values);

        $keys = array_values($this->columnKeys);
        $row = array_combine($keys, $values);

        return $this->validateDataRow($row);
    }

    /**
     * split string into data row array
     * @param String $str
     * $return Array
     */
    protected function extractRowValues(String $str) : Array
    {
        $str = trim($str, $this->EOR);
        $str = trim($str);

        $values = explode($this->EOF, $str);

        return $values;
    }

    /**
     * check array of data is correct
     * 
     * @param Array $row
     * @return Array
     */
    protected function validateDataRow(Array $row)
    {
        foreach($row as $columnName => $columnValue) {
            $this->validateDataColumn($columnName, $columnValue);
        }

        return $row;
    }

    /**
     * check a field value matches required specification
     * 
     * @param String $columnName
     * @param String $columnValue
     * @return Void
     */
    protected function validateDataColumn(String $columnName, String $columnValue)
    {
        $definition = $this->columnDefinitions[$this->cannonicalColumnName($columnName)];
        $strlen = strlen($columnValue);

        // check if column 'columnName' must have a columnValue
        if (($definition['min'] > 0) && (0 === $strlen)) {
            throw new \Exception("Error: Not a valid BLM file, Data field '{$columnName}' empty, it must have a value");
        }

        // check if column 'columnName' is too large
        if (strlen($columnValue) > $definition['max']) {
            throw new \Exception("Error: Not a valid BLM file, Data field '{$columnName}' is too big, maximum length is '{$definition['max']}', found '{$strlen}' ");
        }

        // check type and size is correct
        if( ! $this->validateDataColumnType($columnName, $columnValue)) {
            throw new \Exception("Error: Not a valid BLM file, Data field '{$columnName}' contains incorrect value, expecting '{$type}', found '{$columnValue}'");
        }

    }

    /**
     * is the column value a date string in the correct format
     * @param String $value
     * @return bool
     */
    protected function isDate(String $value)
    {
        $date = Date($this->formatDate, strtotime($value));
        return Date($this->formatDate, strtotime($value)) === $value;
    }

    /**
     * validate column value against column definition
     * @param String $name
     * @param String $value
     * @return Void
     */
    protected function validateDataColumnType(String $name, String $value)
    {
        $type = 'string';
        $definition = $this->columnDefinitions[$this->cannonicalColumnName($name)];

        if (isset($this->columnDefinitions['type'])) {
            $type = $this->columnDefinitions['type'];
        }

        switch ($type) {
            case 'int':
                return $this->isInt($value, $definition);
            case 'date':
                return $this->isDate($value, $definition);
            case 'num':
                return $this->isNum($value, $definition);
            case 'string':
                return $this->isString($value, $definition);
            default:
                throw new \Exception("Error: Not a valid BLM file, Column '{$name}' is an unknown type, found '{$type}' with the value '{$value}' ");
        }
    }

    /**
     * is the column value an int
     * @param String $value
     * @param Array $definition
     * @return bool
     */    
    protected function isInt(String $value, Array $definition = [])
    {
        $int = strval($value);
        return ctype_digit($int) && ($int >= 0);
    }

    /**
     * is the column value a number, possibly with decimals
     * length if defined includes decimal point // 99.99 = 5 // pic(99.99)
     * 
     * @param String $value
     * @return bool
     */   
    protected function isNum(String $value, Array $definition = [])
    {
        if (preg_match("#^\d*(\.\d*)?$#", $value)) {
            return false;
        }

        if (isset($definition['len'])) {
            $strlen = \strlen($value);
            if ($strlen > $strlen) {
                return false;
            }
        }

        return true;
    }

    /**
     * is the column value a string
     * @param String $value
     * @return bool
     */   
    protected function isString(String $value, Array $definition = [])
    {
        if (0 === count($definition)) {
            return true;
        }

        if (isset($definition['len'])) {
            $strlen = \strlen($value);
            if ($strlen > $strlen) {
                return false;
            }
        }

        return true;
    }

    /**
     * check property sub id is valid for drop down search on rightmove.com
     * 
     * @param Int $propSubId
     * @return Bool
     */
    protected function isPropSubId(Int $propSubId)
    {
        // PROP_SUB_ID | Property Type | Search Criteria Type'],
        static $prop_sub_id = [
            0 => ['Not Specified', 'Not Specified'], //  (ONLY)
            1 => ['Terraced', 'Houses'],
            2 => ['End of Terrace', 'Houses'],
            3 => ['Semi-Detached', 'Houses'],
            4 => ['Detached', 'Houses'],
            5 => ['Mews', 'Houses'],
            6 => ['Cluster House', 'Houses'],
            7 => ['Ground Flat', 'Flats / Apartments'],
            8 => ['Flat', 'Flats / Apartments'],
            9 => ['Studio', 'Flats / Apartments'],
            10 => ['Ground Maisonette', 'Flats / Apartments'],
            11 => ['Maisonette', 'Flats / Apartments'],
            12 => ['Bungalow', 'Bungalows'],
            13 => ['Terraced Bungalow', 'Bungalows'],
            14 => ['Semi-Detached Bungalow', 'Bungalows'],
            15 => ['Detached Bungalow', 'Bungalows'],
            16 => ['Mobile Home', 'Mobile / Park Homes'],
            20 => ['Land', 'Land'],
            21 => ['Link Detached', 'House Houses'],
            22 => ['Town House', 'Houses'],
            23 => ['Cottage', 'Houses'],
            24 => ['Chalet', 'Houses'],
            27 => ['Villa', 'Houses'],
            28 => ['Apartment', 'Flats / Apartments'],
            29 => ['Penthouse', 'Flats / Apartments'],
            30 => ['Finca', 'Houses'],
            43 => ['Barn Conversion', 'Character Property'],
            44 => ['Serviced Apartments', 'Flats / Apartments'],
            45 => ['Parking', 'Garage / Parking'],
            46 => ['Sheltered Housing', 'Retirement Property'],
            47 => ['Retirement Property', 'Retirement Property'],
            48 => ['House Share', 'House / Flat Share'],
            49 => ['Flat Share', 'House / Flat Share'],
            50 => ['Park Home', 'Mobile / Park Homes'],
            51 => ['Garages', 'Garage / Parking'],
            52 => ['Farm House', 'Character Property'],
            53 => ['Equestrian Facility', 'Character Property'],
            56 => ['Duplex', 'Flats / Apartments'],
            59 => ['Triplex', 'Flats / Apartments'],
            62 => ['Longere', 'Character Property'],
            65 => ['Gite', 'Character Property'],
            68 => ['Barn', 'Character Property'],
            71 => ['Trulli', 'Character Property'],
            74 => ['Mill', 'Character Property'],
            77 => ['Ruins', 'Character Property'],
            80 => ['Restaurant', 'Commercial Property'],
            83 => ['Cafe', 'Commercial Property'],
            86 => ['Mill', 'Commercial Property'],
            92 => ['Castle', 'Character Property'],
            95 => ['Village', 'House Houses'],
            101 => ['Cave House', 'Character Property'],
            104 => ['Cortijo', 'Character Property'],
            107 => ['Farm Land', 'Land'],
            110 => ['Plot', 'Land'],
            113 => ['Country House', 'Character Property'],
            116 => ['Stone House', 'Character Property'],
            117 => ['Caravan', 'Mobile / Park Homes'],
            118 => ['Lodge', 'Character Property'],
            119 => ['Log Cabin', 'Character Property'],
            120 => ['Manor House', 'Character Property'],
            121 => ['Stately Home', 'Character Property'],
            125 => ['Off-Plan', 'Land'],
            128 => ['Semi-detached Villa', 'Houses'],
            131 => ['Detached Villa', 'Houses'],
            134 => ['Bar / Nightclub', 'Commercial Property'],
            137 => ['Shop', 'Commercial Property'],
            140 => ['Riad', 'Character Property'],
            141 => ['House Boat', 'Character Property'],
            142 => ['Hotel Room', 'Flats / Apartments'],
            143 => ['Block of Apartments', 'Flats / Apartments'],
            144 => ['Private Halls', 'Flats / Apartments'],
            178 => ['Office', 'Commercial Property'],
            181 => ['Business Park', 'Commercial Property'],
            184 => ['Serviced Office', 'Commercial Property'],
            187 => ['Retail Property (high street)', 'Commercial Property'],
            190 => ['Retail Property (out of town)', 'Commercial Property'],
            193 => ['Convenience Store', 'Commercial Property'],
            196 => ['Garage', 'Commercial Property'],
            199 => ['Hairdresser / Barber Shop', 'Commercial Property'],
            202 => ['Hotel', 'Commercial Property'],
            205 => ['Petrol Station', 'Commercial Property'],
            208 => ['Post Office', 'Commercial Property'],
            211 => ['Pub', 'Commercial Property'],
            214 => ['Workshop', 'Commercial Property'],
            217 => ['Distribution Warehouse', 'Commercial Property'],
            220 => ['Factory', 'Commercial Property'],
            223 => ['Heavy Industrial', 'Commercial Property'],
            226 => ['Industrial Park', 'Commercial Property'],
            229 => ['Light Industrial', 'Commercial Property'],
            232 => ['Storage', 'Commercial Property'],
            235 => ['Showroom', 'Commercial Property'],
            238 => ['Warehouse', 'Commercial Property'],
            241 => ['Land', 'Commercial Property'],
            244 => ['Commercial Development', 'Commercial Property'],
            247 => ['Industrial Development', 'Commercial Property'],
            250 => ['Residential Development', 'Commercial Property'],
            253 => ['Commercial Property', 'Commercial Property'],
            256 => ['Data Centre', 'Commercial Property'],
            259 => ['Farm', 'Commercial Property'],
            262 => ['Healthcare Facility', 'Commercial Property'],
            265 => ['Marine Property', 'Commercial Property'],
            268 => ['Mixed Use', 'Commercial Property'],
            271 => ['Research & Development Facility', 'Commercial Property'],
            274 => ['Science Park', 'Commercial Property'],
            277 => ['Guest House', 'Commercial Property'],
            280 => ['Hospitality', 'Commercial Property'],
            283 => ['Leisure Facility', 'Commercial Property'],
            298 => ['Takeaway', 'Commercial Property'],
            301 => ['Childcare Facility', 'Commercial Property'],
            304 => ['Smallholding', 'Land'],
            307 => ['Place of Worship', 'Commercial Property'],
            310 => ['Trade Counter', 'Commercial Property'],
            511 => ['Coach House', 'Flats / Apartments'],
        ];

        return isset($prop_sub_id[$propSubId]);
    }

}
