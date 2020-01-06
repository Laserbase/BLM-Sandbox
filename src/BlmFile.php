<?php

namespace Src\BlmFile;

use Log;

class BlmFile {
    protected $formatDate = 'Y-m-d H:i:s';
    protected $maxHeaderLines = 25;
    protected $resource = null;
    protected $lastMediaImageIndex = 59;
    protected $lastDocumentIndex = 49;
    protected $letTypeId_StudentLetting = 3;
    protected $letTypeId_CommercialLetting = 4;
    protected $erpHipCaptions = ['HIP', 'EPC'];

    protected $sectionTags = [
        'HEADER' => '#HEADER#',
        'DEFINITION' => '#DEFINITION#',
        'DATA' => '#DATA#',
        'END' => '#END#'
    ];
    protected $header = [
        'Version' => "3",
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
        //      type = date | enum | int | num | string
        // required = column must be in data feed
        // min:0 = value is optional
        //      otherwise a value must be given
        // max:n = maximum length in characters for a column value
        // recursive = field name can be repeated
        //       with an underscore followed by an index number
        // enum = enumerated values (0,1,3), (0-3), (2-10),  ('Y', 'N')

        "AGENT_REF" => 'string|required|min:1|max:20',
        "BRANCH_ID" => 'int|required|min:1|max:10', // provided by Rightmove
        "STATUS_ID" => 'enum|required|min:1|max:1', //

        "CREATE_DATE" => 'date|required|min:0', // YYYY-MM-DD HH:MI:SS
        "UPDATE_DATE" => 'date|required|min:0', // YYYY-MM-DD HH:MI:SS
    ];

    protected $columnDefinitionV3 = [
        "DISPLAY_ADDRESS" => 'string|required|min:1|max:120', // Address of the property that should be displayed on the Live Rightmove site
        "PUBLISHED_FLAG" => 'enum|required|min:1|max:1', // 0 = hidden/invisible 1 = visible

        "LET_DATE_AVAILABLE" => 'date|min:0|min:0', // date|required|min:0
        "LET_BOND" => 'num|min:0', // deposit amount
        "ADMINISTRATION_FEE" => 'string|min:0|max:4096', // all fees applicable to the property added 2014-09-22
        "LET_TYPE_ID" => 'enum|required|min:0|max:1|default:0', // column required, data optional
        "LET_FURN_ID" => 'enum|required|min:0|max:1', // column required, data optional
        "LET_RENT_FREQUENCY" => 'enum|required|min:0|max:1|default:1',

        "TENURE_TYPE_ID" => 'enum|min:0|max:1', // required in spec - not in data
        "TRANS_TYPE_ID" => 'enum|required|min:1|max:1', // 1 = resale, 2 = lettings

        "NEW_HOME_FLAG" => 'enum|required|min:0|max:1|default:N', // Y / N or empty

        "BEDROOMS" => 'int|required|min:1|max:10',
        "PRICE" => 'num|required|min:1',
        "PRICE_QUALIFIER" => 'enum|required|min:0|max:2|default:0',
        
        "PROP_SUB_ID" => 'enum|required|min:1|max:3|default:0', // One of the valid property types. Ref. Property Type table

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

        "SUMMARY" => 'string|required|min:1|max:1024', 
            /* ALL HTML will be stripped
                Only 300 characters will be displayed on site. 
            */

        "DESCRIPTION" => 'string|required|min:1|max:32768', 
            /* Basic HTML tags can be used for bold, underlining, italicising
                <b></b> <u></u> <i></i>
                ??? <b></b> vs <strong></strong> vs both ???
            */
    ];

    protected $columnDefinitionV3_StudentLettings = [
        // LET_TYPE_ID === 3 // (Student Lettings Only) ---------------------
        "LET_CONTRACT_IN_MONTHS" => 'int|min:0|max:2|default:N', // student
        "LET_WASHING_MACHINE_FLAG" => 'enum|min:0|max:1|default:N', // Y/N student
        "LET_DISHWASHER_FLAG" => 'enum|min:0|max:1|default:N', // Y/N student
        "LET_BURGLAR_ALARM_FLAG" => 'enum|min:0|max:1|default:N', // Y/N student
        "LET_BILL_INC_WATER" => 'enum|min:0|max:1|default:N', // Y/N student
        "LET_BILL_INC_GAS" => 'enum|min:0|max:1|default:N', // Y/N student
        "LET_BILL_INC_ELECTRICITY" => 'enum|min:0|max:1|default:N', // Y/N student
        "LET_BILL_INC_TV_LICIENCE" => 'enum|min:0|max:1|default:N', // Y/N student
        "LET_BILL_INC_TV_SUBSCRIPTION" => 'enum|min:0|max:1|default:N', // Y/N student
        "LET_BILL_INC_INTERNET" => 'enum|min:0|max:1|default:N', // Y/N student
        //-------------------------------------------------------------------
    ];
    protected $columnDefinitionV3_CommercialLettings = [
        // LET_TYPE_ID === 4 // (Commercial only) ---------------------------
        "MIN_SIZE_ENTERED" => 'num|min:0|max:15', // (Commercial only)
        "MAX_SIZE_ENTERED" => 'num|min:0|max:15', // (Commercial only)

        "AREA_SIZE_UNIT_ID" => 'enum|min:0|max:1', // (Commercial only)

        "BUSINESS_FOR_SALE_FLAG" => 'enum|min:0|max:1', // (Commercial only)
        "PRICE_PER_UNIT" => 'num|min:0|max:15', // (Commercial only)

        /* COMM_CLASS_ORDER (Commercial only)
            Specifies the Use Class Order from this: http://www.gvagrimley.co.uk/PreBuilt/PDR/other/GVAGUseClassOrder.pdf
            Options are: 
                A1, Shops
                A2, Professional and Financial Services
                A3, Restaurants and Cafes
                A4, Drinking Establishments
                A5, Hot Food Takeaways
                B1, Business
                B2, General Industrial
                B8, Storage and Distribution
                C1, Hotels
                C2, Residential Institutions
                C2A, 
                C3, Dwellinghouses
                D1, Non-residential Institutions
                D2, Assembly and Leisure
                sui_generis_1, // Casinos and Amusement Arcades / Centres
                sui_generis_2  // Betting Offices and Pay Day Loan Shops
            */
        "COMM_CLASS_ORDER_1" => 'string|min:0|max:100', // (Commercial only)
        "COMM_CLASS_ORDER_2" => 'string|min:0|max:100', // (Commercial only)
        "COMM_CLASS_ORDER_3" => 'string|min:0|max:100', // (Commercial only)
        "COMM_CLASS_ORDER_4" => 'string|min:0|max:100', // (Commercial only)
        "COMM_CLASS_ORDER_5" => 'string|min:0|max:100', // (Commercial only)
        "COMM_CLASS_ORDER_6" => 'string|min:0|max:100', // (Commercial only)
        //-------------------------------------------------------------------
    ];
    protected $columnDefinitionV3_Media = [
        //-------------------------------------------------------------------
        // All links to Floor plans, Brochures and Virtual Tours must only link to the physical media 
        //  and not to a webpage consisting of the media and external links.
        "MEDIA_IMAGE_00" => 'string|required|min:1|max:100|media:img',
        "MEDIA_IMAGE" => 'string|min:0|max:100|recursive|media:img',
        "MEDIA_IMAGE_TEXT" => 'string|min:0|max:20|recursive|media:text',

        // in spec, but not in test file, comment out for now
        // "MEDIA_IMAGE_60" => 'string|required|min:0|max:20|recursive|media:img', // Name of the property EPC graphic. MEDIA_IMAGE_60 is for EPC Graphics that would be shown on site.
        // "MEDIA_IMAGE_TEXT_60" => 'string|required|min:0|max:3|recursive|media:text', // Caption to go with the EPC of MEDIA_IMAGE_60, this MUST READ “EPC”.

        "MEDIA_FLOOR_PLAN" => 'string|min:0|max:100|recursive|media:flp|url:optional',
        "MEDIA_FLOOR_PLAN_TEXT" => 'string|min:0|max:20|recursive|media:text',

        "MEDIA_DOCUMENT" => 'string|min:0|max:200|recursive|media:doc|url:optional',
        "MEDIA_DOCUMENT_TEXT" => 'string|min:0|max:20|recursive|media:text',

        "MEDIA_VIRTUAL_TOUR" => 'string|min:0|max:200|recursive|media:tour|url:required',
        "MEDIA_VIRTUAL_TOUR_TEXT" => 'string|min:0|max:20|recursive|media:text',
        //-------------------------------------------------------------------
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

    protected $enums = [
        'STATUS_ID' => [
            0 => 'Available',
            1 => 'SSTC - Sold Subject To Completion',
            2 => 'SSTCM - Scotland - Sold Subject To Concluded Missives',
            3 => 'under offer - sales',
            4 => 'reserved - sales',
            5 => 'let agreed - letting',
        ],
        "TENURE_TYPE_ID" => [//] 'enum|min:0|max:1', // required in spec - not in data
            1 => 'Freehold',
            2 => 'Leasehold',
            3 => 'Feudal',
            4 => 'Commonhold',
            5 => 'Share of Freehold',
        ],
        "PROP_SUB_ID" => [
            0 => 'Not Specified',
            1 => 'Terraced',
            2 => 'End of Terrace',
            3 => 'Semi-Detached',
            4 => 'Detached',
            5 => 'Mews',
            6 => 'Cluster House',
            7 => 'Ground Flat',
            8 => 'Flat',
            9 => 'Studio',
            10 => 'Ground Maisonette',
            11 => 'Maisonette',
            12 => 'Bungalow',
            13 => 'Terraced Bungalow',
            14 => 'Semi-Detached Bungalow',
            15 => 'Detached Bungalow',
            16 => 'Mobile Home',
            20 => 'Land',
            21 => 'Link Detached',
            22 => 'Town House',
            23 => 'Cottage',
            24 => 'Chalet',
            27 => 'Villa',
            28 => 'Apartment',
            29 => 'Penthouse',
            30 => 'Finca',
            43 => 'Barn Conversion',
            44 => 'Serviced Apartments',
            45 => 'Parking',
            46 => 'Sheltered Housing',
            47 => 'Retirement Property',
            48 => 'House Share',
            49 => 'Flat Share',
            50 => 'Park Home',
            51 => 'Garages',
            52 => 'Farm House',
            53 => 'Equestrian Facility',
            56 => 'Duplex',
            59 => 'Triplex',
            62 => 'Longere',
            65 => 'Gite',
            68 => 'Barn',
            71 => 'Trulli',
            74 => 'Mill',
            77 => 'Ruins',
            80 => 'Restaurant',
            83 => 'Cafe',
            86 => 'Mill',
            92 => 'Castle',
            95 => 'Village',
            101 => 'Cave House',
            104 => 'Cortijo',
            107 => 'Farm Land',
            110 => 'Plot',
            113 => 'Country House',
            116 => 'Stone House',
            117 => 'Caravan',
            118 => 'Lodge',
            119 => 'Log Cabin',
            120 => 'Manor House',
            121 => 'Stately Home',
            125 => 'Off-Plan',
            128 => 'Semi-detached Villa',
            131 => 'Detached Villa',
            134 => 'Bar / Nightclub',
            137 => 'Shop',
            140 => 'Riad',
            141 => 'House Boat',
            142 => 'Hotel Room',
            143 => 'Block of Apartments',
            144 => 'Private Halls',
            178 => 'Office',
            181 => 'Business Park',
            184 => 'Serviced Office',
            187 => 'Retail Property (high street)',
            190 => 'Retail Property (out of town)',
            193 => 'Convenience Store',
            196 => 'Garage',
            199 => 'Hairdresser / Barber Shop',
            202 => 'Hotel',
            205 => 'Petrol Station',
            208 => 'Post Office',
            211 => 'Pub',
            214 => 'Workshop',
            217 => 'Distribution Warehouse',
            220 => 'Factory',
            223 => 'Heavy Industrial',
            226 => 'Industrial Park',
            229 => 'Light Industrial',
            232 => 'Storage',
            235 => 'Showroom',
            238 => 'Warehouse',
            241 => 'Land',
            244 => 'Commercial Development',
            247 => 'Industrial Development',
            250 => 'Residential Development',
            253 => 'Commercial Property',
            256 => 'Data Centre',
            259 => 'Farm',
            262 => 'Healthcare Facility',
            265 => 'Marine Property',
            268 => 'Mixed Use',
            271 => 'Research & Development Facility',
            274 => 'Science Park',
            277 => 'Guest House',
            280 => 'Hospitality',
            283 => 'Leisure Facility',
            298 => 'Takeaway',
            301 => 'Childcare Facility',
            304 => 'Smallholding',
            307 => 'Place of Worship',
            310 => 'Trade Counter',
            511 => 'Coach House',
        ],
        "LET_TYPE_ID" => [
            0 => 'Not specified', // DEFAULT
            1 => 'Long term',
            2 => 'Short term',
            3 => 'Student', // columnDefinitionV3_StudentLettings
            4 => 'Commercial' // columnDefinitionV3_CommercialLettings
        ],
        "LET_FURN_ID" => [
            0 => 'Furnished',
            1 => 'Part furnished',
            2 => 'Unfurnished',
            3 => 'Not specified', // DEFAULT
            4 => 'Furnished', // / unfurnished ??? @todo resolve conflict
        ],
        "LET_RENT_FREQUENCY" => [
            0 => 'Weekly',
            1 => 'Monthly', // - DEFAULT if null
            2 => 'Quarterly',
            3 => 'Annual',
            // 4 = UNKNOWN
            5 => 'Per-person per-week - students',
        ],
        "TRANS_TYPE_ID" => [
            1 => 'Resale',
            2 => 'Lettings',
        ],
        "NEW_HOME_FLAG" => [
            'Y' => 'New Home',
            'N' => 'Not A New Home', // or empty
        ],
        "PRICE_QUALIFIER" => [
            0 => 'Default',
            1 => 'POA',
            2 => 'Guide Price',
            3 => 'Fixed Price',
            4 => 'Offers in Excess of',
            5 => 'OIRO, Offers In The Region Of',
            6 => 'Sale by Tender',
            7 => 'From (new homes and commercial only)',
            // 8 => UNKNOWN
            9 => 'Shared Ownership',
            10 => 'Offers Over',
            11 => 'Part Buy Part Rent',
            12 => 'Shared Equity',
            // 13 UNKNOWN
            14 => 'Equity Loan',
            15 => 'Offers Invited',
        ],
        "PUBLISHED_FLAG" => [
            0 => 'Hidden/invisible',
            1 => 'Visible',
        ],
        "LET_WASHING_MACHINE_FLAG" => ['Y' => 'Yes', 'N' => 'No', ], // student
        "LET_DISHWASHER_FLAG" =>  ['Y' => 'Yes', 'N' => 'No', ], // student
        "LET_BURGLAR_ALARM_FLAG" =>  ['Y' => 'Yes', 'N' => 'No', ], // student
        "LET_BILL_INC_WATER" =>  ['Y' => 'Yes', 'N' => 'No', ], // student
        "LET_BILL_INC_GAS" =>  ['Y' => 'Yes', 'N' => 'No', ], // student
        "LET_BILL_INC_ELECTRICITY" =>  ['Y' => 'Yes', 'N' => 'No', ], // student
        "LET_BILL_INC_TV_LICIENCE" =>  ['Y' => 'Yes', 'N' => 'No', ], // student
        "LET_BILL_INC_TV_SUBSCRIPTION" =>  ['Y' => 'Yes', 'N' => 'No', ], // student
        "LET_BILL_INC_INTERNET" =>  ['Y' => 'Yes', 'N' => 'No', ], // student

        "AREA_SIZE_UNIT_ID" => [
            1 => 'Square feet',
            2 => 'Square meters', 
            3 => 'Acres', 
            4 => 'Hectares',
        ],
        "BUSINESS_FOR_SALE_FLAG" => [
            0 => 'Not a business for sale',
            1 => 'Business for sale',
        ],
    ];

    protected $columnTypes = [ // zx
        'date', 'enum', 'int', 'num', 'string'
    ];
    protected $mediaTypes = [ // media code => [ filename extensions ]
        'img' => ['.jpg', '.gif', '.png'],
        'flp' => ['.jpg', '.gif', '.png'],
        'doc' => ['.pdf']
    ];

    public function __construct()
    {
        $this->setupDefinitions();
        $this->setupHeaderDefaults();
    }

    /**
     * set column definitions from hard coded string
     */   
    protected function setupDefinitions()
    {
        foreach($this->columnDefinitionMaster as $name => $definitionString) {
            $this->columnDefinitionMaster[$name] = $this->stringToDefinition($name, $definitionString);
        }
        //---------------------------------------------------------------------
        foreach($this->columnDefinitionV3 as $name => $definitionString) {
            $this->columnDefinitionV3[$name] = $this->stringToDefinition($name, $definitionString);
        }
        foreach($this->columnDefinitionV3_StudentLettings as $name => $definitionString) {
            $this->columnDefinitionV3[$name] = $this->stringToDefinition($name, $definitionString);
        }
        foreach($this->columnDefinitionV3_CommercialLettings as $name => $definitionString) {
            $this->columnDefinitionV3[$name] = $this->stringToDefinition($name, $definitionString);
        }
        foreach($this->columnDefinitionV3_Media as $name => $definitionString) {
            $this->columnDefinitionV3[$name] = $this->stringToDefinition($name, $definitionString);
        }
        //---------------------------------------------------------------------
        foreach($this->columnDefinitionV3i as $name => $definitionString) {
            $this->columnDefinitionV3i[$name] = $this->stringToDefinition($name, $definitionString);
        }        
    }
    /**
     * setup Header Defaults
     */
    protected function setupHeaderDefaults()
    {
        $this->Version = "3";
        $this->EOF = '^';
        $this->EOR = '~';
        $this->{'Property Count'} = 0;
        $this->{'Generated Date'} = Date($this->formatDate);

    }

    /**
     * transform $definitionString into an associated array
     * 
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

        $definitions = explode('|', $definitionString);

        $result = [
            'type' => array_shift($definitions),
            'required' => false,
            'recursive' => false,
            'min' => 0,
            'max' => 4096,
            'media' => '',
            'default' => '',
            'url' => 'deny',
        ];

        foreach($definitions as $definition) {
            if ('required' == $definition ) {
                $result['required'] = true;
                continue;
            }
            if ('recursive' == $definition ) {
                $result['recursive'] = true;
                continue;
            }

            $key = substr($definition, 0, strpos($definition, ':'));
            $value = substr($definition, strpos($definition, ':')+1);
            if ('' === $key) {
                $key = $definition;
                $value = $definition;
            }

            switch ($key) {
                case 'min':
                case 'max':
                    $result[$key] = (int) $value;
                break;

                default:
                    $result[$key] = $value;
                break;

            }
        
        }

        return $result;
    }

    /**
     * return column name, strip suffix if column is recursive
     * 
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
     * 
     * @return void
     */
    public function __set($name, $value)
    {
        switch ($name) {
            case 'EOF':
            case 'EOR': $this->header[$name] = trim($value, "'");
                break;
            default: $this->header[$name] = $value;
        }
    }

    /**
     * get header parameter
     * 
     * @param String $name 
     * @return String header value
     * @throws Exception on name parameter not part of headers
     */
    public function __get(String $name)
    {
        if (! isset($this->header[$name]) ) {
            throw new \Exception("Error: Unknown Blm variable '{$name}'");
        }

        return $this->header[$name];
    }

    /**
     * return column definitions for the specified version
     * 
     * @param String $version default use the current version, initially '3'
     * @return Array of column definitions
     */
    protected function getAllColumnDefinitions(String $version = '')
    {
        $result = [];
        if ('' === $version) {
            $version = $this->Version;
        }

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

            default:
                throw new \Exception("Error: Not a valid BLM file, File Version missing");
        }

        return $result;
    }

    /**
     * given a php resource, read setup info from data file then set and check corresponding definitions
     * 
     * @param $resource
     * @return $this
     */
    public function setup($resource)
    {
        if (! is_resource($resource)) {
            throw new \Exception('Error: Not A file Resource');
        }
        $this->setupHeaderDefaults();

        $this->resource = $resource;

        $this->readHeader();
        $this->checkHeader();

        $str = $this->readDefinition(); // @todo move to checking definition
        $this->validateDefinition($str);
        
        $this->checkDataSection();
    }

    /**
     * read header section
     * 
     * @throws Exception on any error found
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
     * check ##HEADER## section
     * 
     * @throws Exception on header missing required items
     */
    protected function checkHeader()
    {
        $diff = array_diff($this->headerRequired, array_keys($this->header));
        if ($diff) {
            throw new \Exception("Error: Not a valid BLM file, invalid header, missing item(s) '".implode("', '", $diff)."' ");
        }

        $this->selectVersionColumnDefinitions();
    }

    /**
     * 2019-12-10 14:33 set public
     *      set and return array of $this->columnDefinitions
     * 
     * @return Array of column definitions
     */
    public function selectVersionColumnDefinitions()
    {
        return $this->columnDefinitions = $this->getAllColumnDefinitions();
    }

    /**
     * read ##DEFINITION## section
     * 
     * @return String of column names
     * @throws Exception on line format error
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
     * 
     * @return Void
     * @throws Exception on missing ##DATA## section line
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
     *      contrast with readDataLine() which allows fields to have carriage returns
     * 
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
     *      contrast with readLine() which expects to read a single line
     * 
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

//    protected function readHeaderItem(String $name)

    /**
     * check header item is correct
     * 
     * @param String $name name of the header item
     * @param String $value of the header item
     * @return Bool true if ok
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
            case 'Property Count': 
                $this->isInt('Property Count', $value);
            break;
            
            case 'Generated Date': 
                $this->isDate('Generated Date', $value);
            break;
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
        $this->validateMediaColumnsMustAppearAfterAllOtherFields();
    }

    /**
     * check the number of columns matches expected 
     * 
     * @param Array $values
     * @return Void
     * @throws Exception on number of columns in data mismatches the number of columns in the ##DEFINITION## section
     */
    protected function validateColumnCount(Array $values)
    {
        $countValues = count($values);
        $countColumns = count($this->columnKeys);
        if ($countValues !== $countColumns) {
            throw new \Exception("Error: Not a valid BLM file, The number of row fields '{$countValues}' is different to the number expected '{$countColumns}'");
        }
    }

    /**
     * validateColumn()
     * Is columnName in the list of allowed columns
     * 
     * @throws Exception on column name not in the specification
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
     * 
     * @return Void
     * @throws Exception on incorrect EOF (End-Of-Field) and EOR (End-Of-Record) characters
     * @todo check for regex characters handled as separators
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
     * check ##DEFINITION## section
     *      has all required columns
     * 
     * @return Void
     * @throws Exception on missing a required column
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
     * validateMediaColumnsMustAppearAfterAllOtherFields()
     * 
     * @throws Exception on finding a non media column after a media column
     */
    protected function validateMediaColumnsMustAppearAfterAllOtherFields()
    {
        $mediaFound = false;
        foreach ($this->columnKeys as $index => $name) {
            if (0 === strpos($name, 'MEDIA_')) {
                $mediaFound = true;
                continue;
            }

            if ($mediaFound) {
                throw new \Exception("Error: Not a valid BLM file, Column '{$name}' found after media columns, media Columns Must Appear After All Other Fields");                
            }
        }
    }

    /**
     * read and yield next data row
     * Logs message if property count is different from expected value
     * 
     * @return Void
     * @throws Exception on incorrect data termination
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
     * 
     * @param String $str
     * @return Array of [columnName => columnValue]
     * @throws Exception on data found in data termination sequence
     */
    public function validateData(String $str) : Array
    {
        // The final field should be finished with the EOF delimiter and then EOR delimiter.
        //  Which means that there is a blank column that needs to be accounted for
        //  using array_pop() to remove it
        $values = $this->extractRowValues($str);
        $tmp = array_pop($values);
        if ('' !== $tmp) {
            $strlen = strlen($tmp);
            throw new \Exception("Error: Not a valid BLM file, Data between final End-Of-Field and End-Of-Record, found '{$strlen}' characters, must be blank");
        }
        $this->validateColumnCount($values);

        $keys = array_values($this->columnKeys);
        $row = array_combine($keys, $values);

        return $this->validateDataRow($row);
    }

    /**
     * split string into an array of row data
     * 
     * @param String $str
     * @return Array of data column values
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

        $this->validateRowRules($row);
    
        return $row;
    }

    /**
     * check a field value matches required specification
     * 
     * @param String $columnName
     * @param String $columnValue
     * @return Void
     * @throws Exception on data value not in specified size
     */
    protected function validateDataColumn(String $columnName, String $columnValue)
    {
        $definition = $this->columnDefinitions[$this->cannonicalColumnName($columnName)];
        $type = $definition['type'];
        $min = $definition['min'];
        $max = $definition['max'];
        $strlen = strlen($columnValue);

        // check if column 'columnName' is optional
        if ((0 === $min) && (0 === $strlen) ) {
            return;
        }

        // check if column 'columnName' must have a columnValue
        if ($strlen < $min) {
            throw new \Exception("Error: Not a valid BLM file, Data field '{$columnName}' is too small, minimum length is '{$min}', found '{$strlen}'");
        }

        // check if column 'columnName' is too large
        if ($strlen > $max) {
            throw new \Exception("Error: Not a valid BLM file, Data field '{$columnName}' is too big, maximum length is '{$max}', found '{$strlen}' ");
        }

        // check type and size is correct
        $this->validateDataColumnType($columnName, $columnValue, $definition);

        // check media definitions IMG, FLP, DOC
        $this->validateMediaType($columnName, $columnValue, $definition);

    }

    /**
     * check that inter column relationships are correct
     * 
     * @return void
     */
    protected function validateRowRules(Array $row)
    {
        // MEDIA_IMAGE_00               - IMG main image
        // MEDIA_IMAGE_01..59           - IMG property images
        // MEDIA_IMAGE_60..99           - IMG EPC, HIP images
        // MEDIA_IMAGE_TEXT_60..99      - must contain EPC, HIP only
        // MEDIA_IMAGE_TEXT_00..99      - must have an entry in MEDIA_IMAGE_01..99
        //
        // MEDIA_<type>_..              - must be valid filename <BRANCH_ID>_<AGENT_REF>_<type>_##.ext
        //
        // MEDIA_DOCUMENT_00..49        - DOC, filename of document
        // MEDIA_DOCUMENT_50..99        - filename of EPC
        //
        // MEDIA_DOCUMENT_TEXT_50       - must read 'EPC'
        // MEDIA_DOCUMENT_TEXT_51..99   - must read 'EPC' or 'HIP'
        //
        // MEDIA_FLOOR_PLAN_01..99      - FLP floor plans
        // MEDIA_FLOOR_PLAN_TEXT_01..99 -

        $this->checkAllMediaTextColumnsForOrphans($row);
        $this->checkAllMediaFilenameFormat($row);
        $this->checkImageCertificatesCaption($row);
        $this->checkDocumentCertificatesCaption($row);
        $this->checkLettingDependantFields($row);
    }

    /**
     * checkAllMediaTextColumnsForOrphans
     * 
     * @throws Exception if a media caption is found without a media column or the size is wrong
     */
    protected function checkAllMediaTextColumnsForOrphans($row)
    {
        $textColumns = [];
        foreach ($row as $name => $value) {
            if (! preg_match("#^MEDIA_.*_TEXT_.*$#", $name)) {
                continue;
            }

            $mediaColumn = str_replace('TEXT_', '', $name);
            if (! isset($row[$mediaColumn])) {
                throw new \Exception("Error: Not a valid BLM file, Media caption column '{$name}' missing media column '{$mediaColumn}', caption passed '{$value}'");
            }

            $mediaValue = $row[$mediaColumn];
            if (('' === $mediaValue) && ('' !== $value)) {
                throw new \Exception("Error: Not a valid BLM file, Media caption column '{$name}' must be empty because media column '{$mediaColumn}' is empty, caption passed '{$value}'");
            }
        }
    }

    /**
     * checkAllMediaFilenameFormat
     * 
     * @param Array $row of columnName => columnValue pairs
     * @return void 
     */
    protected function checkAllMediaFilenameFormat(Array $row)
    {
        $definition = [];

        $branchId = '';
        $agentRef = '';
        $mediaType = '';
        $extension = '';
        
        $mediaColumns = [];
        foreach ($row as $name => $value) {
            $definition = $this->columnDefinitions[$this->cannonicalColumnName($name)];

            $definitionMedia = $definition['media'];
            if ('' === $definitionMedia) {
                continue;
            }
            if ('text' === $definitionMedia) {
                continue;
            }

            if (('' === $value) && (0 === $definition['min'])) {
                // value optional
                continue;
            }

            $isUrl = preg_match('#^([a-z]+)://#i', $value, $matches);
            if ($isUrl) {
                $this->checkUrlFormat($name, $value, $matches, $definition);
                
                return;
            }

            $this->checkFilenameFormat($name, $value, $row, $definition);

        }

    }
    
    /**
     * checkFilenameFormat()
     *
     * @param String $name
     * @param String $value
     * @param Array $matches
     * @return void 
     * @throws Exception if url schema is not allowed or urls are denied by specification
     */
   protected function checkUrlFormat(String $name, String $value, Array $matches, Array $definition)
   {
        $isUrlDenied = !in_array($definition['url'], ['optional', 'required']);
        if ($isUrlDenied) {
            // @todo add test
            throw new \Exception("Error: Not a valid BLM file, Media column '{$name}' must be a filename, found a url '{$value}'");
        }

        $scheme = $matches[1];
        if (! in_array($scheme, ['http', 'https']) ) {
            throw new \Exception("Error: Not a valid BLM file, Media column '{$name}' wrong format, '{$scheme}' is not a valid url scheme, found '{$value}");
        }

   }

    /**
     * checkFilenameFormat()
     * 
     * @param String $name
     * @param String $value
     * @param Array $row
     * @param Array $definition
     * @return void 
     * @throws Exception if file name format is not correct
     */
    protected function checkFilenameFormat(String $name, String $value, Array $row, Array $definition)
    {
        $regex = "#^(.*)_(.*)_([0-9]{2,2})(\.[A-Za-z]{3,3})$#";
        $expectedFileNameFormat = '<BRANCH>_<AGENT_REF>_<MEDIATYPE>_<INDEX>.<FILE EXTENSION>';
        
        $definitionMedia = $definition['media'];
        $mediaTypes = array_keys($this->mediaTypes);
        if (! in_array($definitionMedia, $mediaTypes)) {// zx
            throw new \Exception("Error: Not a valid BLM file, (1) Media column '{$name}' file name using unknown media type '{$definitionMedia}', expecting one of '".implode("', '", $mediaTypes)."', found '{$value}'");
        }

        if (! preg_match($regex, $value, $matches)) {
            throw new \Exception("Error: Not a valid BLM file, Media text column '{$name}' wrong format, found '{$value}', expected format is '{$expectedFileNameFormat}'");
        }

        $agentRefFound = $matches[1];
        $mediaFound = $matches[2];
        $indexFound = $matches[3];
        $extensionFound = $matches[4];

        $agentRefExpected = $row['AGENT_REF'];
        $mediaExpected = strtoupper($definitionMedia);
        $indexExpected = substr($name, -2);

        $extensionExpected = strtolower($extensionFound);
        $extension = strtolower(substr($value, -4));
        $extensionsAllowed = $this->mediaTypes[$definitionMedia] ?? [];

        if (! in_array($extension, $extensionsAllowed)) {
            throw new \Exception("Error: Not a valid BLM file, media column '{$name}', value '{$value}' must end in one of '".implode("', '", $extensionsAllowed)."'");
        }

        if ($agentRefFound !== $agentRefExpected) {
            throw new \Exception("Error: Not a valid BLM file, (2) Media file name not in correct format, must begin with '{$agentRefExpected}', found '{$agentRefFound}', value '{$value}'");
        }

        if ($mediaFound !== $mediaExpected) {
            throw new \Exception("Error: Not a valid BLM file, (3) Media column '{$name}' file name not in correct format, must begin with '{$agentRefExpected}_{$mediaExpected}_', found '{$value}'");
        }

        if ($indexFound !== $indexExpected) {
            throw new \Exception("Error: Not a valid BLM file, (4) Media column '{$name}' file name not in correct format, must begin with '{$agentRefExpected}_{$mediaExpected}_{$indexExpected}', found '{$value}'");
        }
}

    /**
     * checkImageCertificatesCaption($row)
     * Check image certificate caption HIP/EPC
     *
     * @param Array $row
     * @return void 
     */
    protected function checkImageCertificatesCaption($row)
    {
        $mediaColumns = [];
        foreach ($row as $name => $value) {
            if (0 !== strpos($name, "MEDIA_IMAGE_TEXT")) {
                // not an image caption column
                continue;
            }

            $index = substr($name, -2);
            $imageName = "MEDIA_IMAGE_{$index}";
            
            if ( ! isset($row[$imageName])) {
                throw new \Exception("Error: Not a valid BLM file, Certificate Image caption '{$name}' missing data field '{$imageName}', caption passed '{$value}' ");
            }
            $imageValue = $row[$imageName];

            // ---------------------------------------------------------
            // Extra variables added to aid comprehension --------------
            $certificateImage = ($index > $this->lastMediaImageIndex);
            $propertyImage = ! $certificateImage;
            $captionMustBeEmpty = ('' === $imageValue);
            $captionMissing = ('' === $value);
            // ---------------------------------------------------------

            if ($captionMustBeEmpty && $captionMissing) {
                // image is empty and caption is empty - nothing to do
                continue;
            }

            $found = in_array($value, $this->erpHipCaptions);
            $range = "('".implode("', '", $this->erpHipCaptions)."')";

            if ($propertyImage) {
                if ($found) {
                    throw new \Exception("Error: Not a valid BLM file, Property image caption '{$name}' must not be in {$range}, found '{$value}'");
                }

                continue;
            }

            if (! $found) {
                throw new \Exception("Error: Not a valid BLM file, Certificate image caption '{$name}' must be in {$range}, found '{$value}'");
            }

        }
    }

    /**
     * checkDocumentCertificatesCaption($row)
     * 
     * @param Array $row of property data
     * @throws Exception on error
     */
    protected function checkDocumentCertificatesCaption($row)
    {
        foreach ($row as $name => $value) {
            if (0 !== strpos($name, "MEDIA_DOCUMENT_TEXT")) {
                continue;
            }

            $index = (Int) substr($name, -2);
            if ($index <= $this->lastDocumentIndex) {
                continue;
            }

            $documentName = "MEDIA_DOCUMENT_{$index}";

            // ---------------------------------------------------------
            // Extra variables added to aid comprehension --------------
            $captionRequired = ('' !== $row[$documentName]);
            $captionMustBeEmpty = ! $captionRequired;

            $captionMissing = ('' === $value);
            $captionFound = $captionMissing;
            // ---------------------------------------------------------

            if ($captionMustBeEmpty && $captionMissing) {
                // nothing to see here, move along, all ok
                continue;
            }
            $erpHipCaptions = "('".implode("', '", $this->erpHipCaptions)."')";

            if ($captionRequired && $captionMissing) {
                throw new \Exception("Error: Not a valid BLM file, Data field 'MEDIA_DOCUMENT_{$index}' HIP/EPC Certificate caption '{$name}' must be in {$erpHipCaptions}");
            }
            if ($captionMustBeEmpty && $captionFound) {
                throw new \Exception("Error: Not a valid BLM file, Data field caption '{$name}' must be empty when 'MEDIA_DOCUMENT_{$index}' is empty, found '{$value}'");
            }

            if (! in_array($value, $this->erpHipCaptions)) {
                throw new \Exception("Error: Not a valid BLM file, HIP/EPC Certificate caption '{$name}' must be in {$erpHipCaptions}, found '{$value}'");
            }

        }
    }

    /**
     * checkLettingDependantFields
     * 
     * @param Array $row of data
     * @return void 
     * @throws Exception on Letting field used on non letting data
     */
    protected function checkLettingDependantFields($row)
    {
        $definition = $this->columnDefinitions[$this->cannonicalColumnName('LET_TYPE_ID')];
        $letTypeId = $row['LET_TYPE_ID'] ?? $definition['default'];

        $studentLettings = array_keys($this->columnDefinitionV3_StudentLettings);
        $commercialLettings = array_keys($this->columnDefinitionV3_CommercialLettings);

        foreach ($row as $name => $value) {
            if (! $this->checkLettingDependantField($name, $value, $letTypeId, $this->letTypeId_StudentLetting, $studentLettings)) {
                throw new \Exception("Error: Not a valid BLM file, Column '{$name}' is for Student Letting only");
            }
            if (! $this->checkLettingDependantField($name, $value, $letTypeId, $this->letTypeId_CommercialLetting, $commercialLettings)) {
                throw new \Exception("Error: Not a valid BLM file, Column '{$name}' is for Commercial Letting only");
            }
            
        }
        
    }

    /**
     * checkLettingDependantField
     * 
     * @param String $name of column 
     * @param String $value of column, ignore empty columns
     * @param String $letTypeId of the row
     * @param Int $expectedLetTypeId of the letting type (Student vs Commercial)
     * @param Array $lettingColumns of the letting type (Student vs Commercial)
     * @return Boolean false if the letting type of the row dosn't match the letting type of the column name
     */
    protected function checkLettingDependantField(String $name, String $value, String $letTypeId, Int $expectedLetTypeId, Array $lettingColumns)
    {
        if ('' === $value) {
            return true; // allow rows of different let type id to appear in data
        }
        if ($letTypeId == $expectedLetTypeId) {
            return true; // skip expected letting type
        }
        if (false === in_array($name, $lettingColumns)) {
            return true; // skip non letting columns
        }

        return false;
    }

    /**
     * validate column value against column definition
     * 
     * @param String $name
     * @param String $value
     * @return Void
     * @throws Exception on column type not defined
     */
    protected function validateDataColumnType(String $name, String $value, Array $definition)
    {
        $type = $definition['type'];
        switch ($type) {
            case 'date':
                $this->isDate($name, $value);
            break;

            case 'enum':
                $this->isEnum($name, $value);
            break;

            case 'int':
                $this->isInt($name, $value);
            break;
            
            case 'num':
                $this->isNum($name, $value);
            break;

            case 'string':
                $this->isString($name, $value);
            break;

            default:
                throw new \Exception("Error: Not a valid BLM file, Column '{$name}' is an unknown type, found '{$type}' with the value '{$value}' ");
        }
    }

    /**
     * is the column value a date string in the correct format
     * 
     * @param String $value
     * @throws Exception if date is in the wrong format
     */
    protected function isDate(String $name, String $value)
    {
        if (Date($this->formatDate, strtotime($value)) !== $value) {
            throw new \Exception("Error: Not a valid BLM file, Data field '{$name}', value '{$value}', is not in the correct format '{$this->formatDate}'");
        }
    }

    /**
     * is the column value an integer mapped to a value
     * 
     * @param String $name of column
     * @param String $value of data
     * @return void 
     * @throws Exception the value is not in the set of enums
     */   
    protected function isEnum(String $name, String $value)
    {
        if (ctype_digit($value)) {
            $value = ltrim($value, '0');
            if ('' === $value) {
                $value = '0';
            }
        }

        if (! isset($this->enums[$name][$value])) {
            throw new \Exception("Error: Not a valid BLM file, Data field '{$name}', value '{$value}' is not in the allowed list of values");
        }

    }

    /**
     * is the column value an integer
     * 
     * @param String $name
     * @param String $value
     * @return void 
     * @throws Exception if the value is not an integer
     */    
    protected function isInt(String $name, String $value)
    {
        if (! ctype_digit($value)) {
            throw new \Exception("Error: Not a valid BLM file, Data field '{$name}', value '{$value}', is not an int");
        }
    }

    /**
     * is the column value a number, possibly with decimals
     * max length if defined includes decimal point // 99.99 = 5 // pic(99.99)
     * 
     * @param String $name
     * @param String $value
     * @return void 
     * @throws Exception if value is not a decimal number
     */   
    protected function isNum(String $name, String $value)
    {
        if (! preg_match("#^\d*(\.\d*)?$#", $value)) {
            throw new \Exception("Error: Not a valid BLM file, Data field '{$name}', value '{$value}', is not a decimal number");
        }
    }

    /**
     * is the column value a string
     * 
     * @param String $name
     * @param String $value
     * @return Boolean true
     */   
    protected function isString(String $name, String $value)
    {
        return true;
    }

    /**
     * validateMedia
     * 
     * @param String $name used for reporting
     * @param String $value
     * @param Array $definition = []
     * @return void 
     */
    protected function validateMediaType(String $name, String $value, Array $definition = [])
    {
        if ([] === $definition) {
            // not passed
            return;
        }
        if (! isset($definition['media'])) {
            // not a media column
            return;
        }

        $media = $definition['media'];
        if ('' === $media) {
            // media column without special handling
            return;
        } // zx

        if (! in_array($media, ['img', 'doc', 'flp', 'tour', 'text'])) {
            throw new \Exception("Error: Not a valid BLM file, Column '{$name}' is an unknown media type, found '{$media}' ");
        }

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
