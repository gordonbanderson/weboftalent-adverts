<?php
 
class AdvertSiteConfig extends DataExtension {     

		
	 public static $db = array(			
		'MpuAboveFoldPosition' => 'Int',
		'MpuBelowFoldPosition' => 'Int'				
	  );


	public static $defaults = array(
		'MpuAboveFoldPosition' => '4',
		'MpuBelowFoldPosition' => '11'	
	);
 
    public function updateCMSFields(FieldList $fields) {
	   $fields->addFieldToTab("Root.Advert", new NumericField("MpuAboveFoldPosition", 
	   									"Position the list for the MPU advert above the fold"));
	   $fields->addFieldToTab("Root.Advert", new NumericField("MpuBelowFoldPosition", 
	   									"Position the list for the MPU advert below the fold"));
	  
    }
}
