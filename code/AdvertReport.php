<?php

class AdvertReport  extends SS_Report {
 
    /*
     *  SS_Report->getCMSFields() uses $description the property directly!
     */
    protected $description = 'Check on how many impressions and click throughs adverts have had';
 
 
    // the report title
    function title() {
        return 'Advert Report';
    }
 
 
    /**
     * Return an array of columns to display in your report.
     */
    function columns() {
        return array(
            'Title' => 'Title',
            'StartDate' => 'Start Date',
            'FinishDate' => 'Finish Time',
            'AdvertSource' => 'Advert Source',
            'Clickthroughs' => 'Click Throughs',
            'Impressions' => 'Impressions'
        );
    }
 
    public function sourceRecords($params = null) {
        return DataList::create('Advert');//->limit(10);
    }


    function parameterFieldsTODO() 
    {
        $params = new FieldList();
         
        //Colour filter
        /*
        $colours = singleton('Page')->dbObject('Colour')->enumValues();
         
        $params->push(new DropdownField(
            "Colour", 
            "Colour", 
            $colours
        ));
        */
 
        //Result Limit
        $filterOptions = array(
            1 => 'Available',

            2 => 'Training',
            3 => 'All Ice Slots'
        );
         
        $df = new DropdownField(
            "IceSlotFilter", 
            "Limit results to", 
            $filterOptions,
            50
        );

       // $df->setSize(100);
        $params->push($df);
                 
        return $params;
    } 

}