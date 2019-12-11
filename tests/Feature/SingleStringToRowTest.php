<?php

namespace Tests\Feature;

// ini_set('memory_limit', 21000000);

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Log;
use Src\BlmFile\BlmFile;

class SingleStringToRowTest extends TestCase
{
    protected $columnKeys = 'AGENT_REF^BRANCH_ID^STATUS_ID^BEDROOMS^PRICE^PRICE_QUALIFIER^LET_DATE_AVAILABLE^LET_RENT_FREQUENCY^LET_FURN_ID^ADMINISTRATION_FEE^ADDRESS_1^ADDRESS_2^ADDRESS_3^TOWN^POSTCODE1^POSTCODE2^FEATURE1^FEATURE2^FEATURE3^FEATURE4^FEATURE5^FEATURE6^FEATURE7^FEATURE8^FEATURE9^FEATURE10^SUMMARY^DESCRIPTION^PROP_SUB_ID^CREATE_DATE^UPDATE_DATE^DISPLAY_ADDRESS^PUBLISHED_FLAG^TRANS_TYPE_ID^NEW_HOME_FLAG^MEDIA_IMAGE_00^MEDIA_IMAGE_01^MEDIA_IMAGE_02^MEDIA_IMAGE_03^MEDIA_IMAGE_04^MEDIA_IMAGE_05^MEDIA_IMAGE_06^MEDIA_IMAGE_07^MEDIA_IMAGE_08^MEDIA_IMAGE_09^MEDIA_IMAGE_10^MEDIA_IMAGE_11^MEDIA_IMAGE_12^MEDIA_IMAGE_13^MEDIA_IMAGE_14^MEDIA_IMAGE_15^MEDIA_IMAGE_16^MEDIA_IMAGE_17^MEDIA_IMAGE_18^MEDIA_IMAGE_19^MEDIA_IMAGE_20^MEDIA_IMAGE_21^MEDIA_IMAGE_22^MEDIA_IMAGE_23^MEDIA_IMAGE_24^MEDIA_IMAGE_25^MEDIA_IMAGE_26^MEDIA_IMAGE_27^MEDIA_IMAGE_28^MEDIA_IMAGE_29^MEDIA_IMAGE_30^MEDIA_IMAGE_31^MEDIA_IMAGE_32^MEDIA_IMAGE_33^MEDIA_IMAGE_34^MEDIA_IMAGE_35^MEDIA_IMAGE_36^MEDIA_IMAGE_37^MEDIA_IMAGE_38^MEDIA_IMAGE_39^MEDIA_IMAGE_40^MEDIA_IMAGE_41^MEDIA_IMAGE_42^MEDIA_IMAGE_43^MEDIA_IMAGE_44^MEDIA_IMAGE_45^MEDIA_IMAGE_46^MEDIA_IMAGE_47^MEDIA_IMAGE_48^MEDIA_IMAGE_49^MEDIA_IMAGE_60^MEDIA_IMAGE_TEXT_60^MEDIA_FLOOR_PLAN_00^MEDIA_FLOOR_PLAN_01^MEDIA_FLOOR_PLAN_02^MEDIA_FLOOR_PLAN_03^MEDIA_FLOOR_PLAN_04^MEDIA_FLOOR_PLAN_05^MEDIA_FLOOR_PLAN_06^MEDIA_FLOOR_PLAN_07^MEDIA_DOCUMENT_00^MEDIA_DOCUMENT_01^MEDIA_DOCUMENT_02^MEDIA_DOCUMENT_03^MEDIA_DOCUMENT_50^MEDIA_DOCUMENT_TEXT_50^MEDIA_VIRTUAL_TOUR_00^MEDIA_VIRTUAL_TOUR_01^MEDIA_VIRTUAL_TOUR_02^MEDIA_VIRTUAL_TOUR_03^~';
    protected $columnData = 'FBM_FBM2766^FBM^1^3^135000^0^^3^^^57^ADDRESS_1^ADDRESS_2^ADDRESS_3^PC01^2PC^f1^f2^f3^^^^^^^^Brand new three bedroom second floor apartment (763 sq ft)^Brand new three bedroom second floor apartment (763 sq ft) with investor special package (see separate list)^8^2010-10-19 00:00:00^2014-10-20 10:27:30^ADDRESS_1, ADDRESS_2^1^1^Y^FBM_FBM2766_IMG_00.JPG^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^~';

    public function test_multiLineHandling()
    {
        $blm = new BlmFile();
        $blm->Version = '3';

        $blm->selectVersionColumnDefinitions();

        $blm->validateDefinition($this->columnKeys);
        $row = $blm->validateData($this->columnData);

        $this->assertEquals($row['AGENT_REF'], 'FBM_FBM2766');
    }
}
