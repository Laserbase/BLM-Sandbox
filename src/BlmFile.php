<?php

namespace Src\BlmFile;

use Log;

class BlmFile {
    protected $resource = null;

    protected $header = [
        'HEADER' => '#HEADER#',
        'Version' => "",
        'EOF' => '^',
        'EOR' => '~',
        'Property Count' => 0,
        'Generated Date' => '',
        'DEFINITION' => '#DEFINITION#',
        'DATA' => '#DATA#',
        'END' => '#END#'
    ];
    protected $columns = [];
    protected $columnDefinition = [
        "AGENT_REF" => 'string:mandatory:nullable',
        // "xAGENT_REF" => 'string:mandatory:nullable',
        "BRANCH_ID" => 'string:mandatory:nullable',
        "STATUS_ID" => 'string:mandatory:nullable',
        "BEDROOMS" => 'string:mandatory:nullable',
        "PRICE" => 'string:mandatory:nullable',
        "PRICE_QUALIFIER" => 'string:mandatory:nullable',
        "LET_RENT_FREQUENCY" => 'string:mandatory:nullable',
        "LET_FURN_ID" => 'string:mandatory:nullable',
        "ADMINISTRATION_FEE" => 'string:mandatory:nullable',
        "ADDRESS_1" => 'string:mandatory:nullable',
        "ADDRESS_2" => 'string:mandatory:nullable',
        "ADDRESS_3" => 'string:mandatory:nullable',
        "TOWN" => 'string:mandatory:nullable',
        "POSTCODE1" => 'string:mandatory:nullable',
        "POSTCODE2" => 'string:mandatory:nullable',
        "FEATURE1" => 'string:mandatory:nullable',
        "FEATURE2" => 'string:mandatory:nullable',
        "FEATURE3" => 'string:mandatory:nullable',
        "FEATURE4" => 'string:mandatory:nullable',
        "FEATURE5" => 'string:mandatory:nullable',
        "FEATURE6" => 'string:mandatory:nullable',
        "FEATURE7" => 'string:mandatory:nullable',
        "FEATURE8" => 'string:mandatory:nullable',
        "FEATURE9" => 'string:mandatory:nullable',
        "FEATURE10" => 'string:mandatory:nullable',
        "SUMMARY" => 'string:mandatory:nullable',
        "DESCRIPTION" => 'string:mandatory:nullable',
        "PROP_SUB_ID" => 'string:mandatory:nullable',
        "CREATE_DATE" => 'date:mandatory:nullable',
        "UPDATE_DATE" => 'date:mandatory:nullable',
        "DISPLAY_ADDRESS" => 'string:mandatory:nullable',
        "PUBLISHED_FLAG" => 'string:mandatory:nullable',
        "TRANS_TYPE_ID" => 'string:mandatory:nullable',
        "NEW_HOME_FLAG" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_00" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_01" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_02" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_03" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_04" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_05" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_06" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_07" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_08" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_09" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_10" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_11" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_12" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_13" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_14" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_15" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_16" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_17" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_18" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_19" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_20" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_21" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_22" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_23" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_24" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_25" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_26" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_27" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_28" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_29" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_30" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_31" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_32" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_33" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_34" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_35" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_36" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_37" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_38" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_39" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_40" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_41" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_42" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_43" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_44" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_45" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_46" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_47" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_48" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_49" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_60" => 'string:mandatory:nullable',
        "MEDIA_IMAGE_TEXT_60" => 'string:mandatory:nullable',
        "MEDIA_FLOOR_PLAN_00" => 'string:mandatory:nullable',
        "MEDIA_FLOOR_PLAN_01" => 'string:mandatory:nullable',
        "MEDIA_FLOOR_PLAN_02" => 'string:mandatory:nullable',
        "MEDIA_FLOOR_PLAN_03" => 'string:mandatory:nullable',
        "MEDIA_FLOOR_PLAN_04" => 'string:mandatory:nullable',
        "MEDIA_FLOOR_PLAN_05" => 'string:mandatory:nullable',
        "MEDIA_FLOOR_PLAN_06" => 'string:mandatory:nullable',
        "MEDIA_FLOOR_PLAN_07" => 'string:mandatory:nullable',
        "MEDIA_DOCUMENT_00" => 'string:mandatory:nullable',
        "MEDIA_DOCUMENT_01" => 'string:mandatory:nullable',
        "MEDIA_DOCUMENT_02" => 'string:mandatory:nullable',
        "MEDIA_DOCUMENT_03" => 'string:mandatory:nullable',
        "MEDIA_DOCUMENT_50" => 'string:mandatory:nullable',
        "MEDIA_DOCUMENT_TEXT_50" => 'string:mandatory:nullable',
        "MEDIA_VIRTUAL_TOUR_00" => 'string:mandatory:nullable',
        "MEDIA_VIRTUAL_TOUR_01" => 'string:mandatory:nullable',
        "MEDIA_VIRTUAL_TOUR_02" => 'string:mandatory:nullable',
        "MEDIA_VIRTUAL_TOUR_03" => 'string:mandatory:nullable'        
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
        $this->{'Generated Date'} = Date('Y-m-d H:i:s"');

        $this->resource = $resource;

        $this->readHeader();

        return $this;
    }

    protected function readHeader()
    {
        $str = $this->readLine();

        if ($this->HEADER !== trim($str)) {
            throw new \Exception('Not a valid BLM file, Header missing');
        }

        $this->readHeaderItem('Version');
        $this->readHeaderItem('EOF');
        $this->readHeaderItem('EOR');
        $this->readHeaderItem('Property Count');
        $this->readHeaderItem('Generated Date');
        $this->skip();

        $this->readDefinition();
    }

    protected function readDefinition()
    {
        $str = $this->readLine();
        if ($this->DEFINITION !== $str) {
            throw new \Exception('Error: Not a valid BLM file, definition missing');
        }

        $str = $this->readLine();
        if (! preg_match("#^[A-Z_0-9\\".$this->EOF."]+\\".$this->EOR."$#",$str)) {
            throw new \Exception("Error: Not a valid BLM file, definition incorrect found '{$str}'");
        }

        $this->validateDefinition($str);



    }

    public function readData() : Array
    {
        throw new \Exception('Error: Not Implemented yet');

        $str = $this->readLine();
        if ($this->DATA !== trim($str)) {
            throw new \Exception('Eror: Not a valid BLM file, definition missing');
        }
        while ($str = $this->readDataLine()) {

        }
        return [];
    }

    /**
     * read a line from HEADER/ DEFINITION
     * @return string
     */
    protected function readLine()
    {
        return trim(fgets($this->resource));
    }

    protected function skip()
    {
        $str = $this->readLine();
        if ($str !== '') {
            throw new \Exception("Error: unable to skip blank line, found '{$str}'");
        }
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
        // \Log::debug("{$name}=({$value}) ");

        if (! $this->validateHeaderItem($name, $value)) {
            throw new \Exception("Error: Not a valid BLM file, invalid header item '{$name}' failed with value '{$value}'");
        }

        $this->{$name} = $value;
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

    protected function isDate(String $value)
    {
        return Date('Y-m-d H:i:s', strtotime($value)) === $value;
    }
    protected function isInt(String $value)
    {
        $int = strval($value);
        return ctype_digit($int) && ($int >= 0);
    }

}
