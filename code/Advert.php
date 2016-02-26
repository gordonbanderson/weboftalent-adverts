<?php

/**
 * Only show a page with login when not logged in
 */
class Advert extends DataObject {


  static $searchable_fields = array(
    'WebsiteLink',
 );


 


  static $db = array(
    // title to show in model admin
    'Title' => 'Varchar(255)',

    // for non adbroker ads, link to this URL
    'WebsiteLink' => 'Varchar(255)',

    // digital signature of some of the fields used as a means of uniquely identifying clicks
    // See http://www.sitepoint.com/forums/showthread.php?477567-SHA512-hash-in-php for reason of lenght 128
    'DigitalSignature' => 'Varchar(128)',

    // for adbroker ads
    'AdbrokerJavascript' => 'Text',

    // the source of the advert, either an image or adbroker JS
    "AdvertSource" => "Enum('UploadedImage,AdbrokerJavascript')",


    // date range valid
    'StartDate' => 'Datetime',
    'FinishDate' => 'Datetime',

    // stats
    'Impressions' => 'Int',
    'Clickthroughs' => 'Int',
 );




  static $has_one = array(
    'AdvertImage' => 'Image',
    'AdvertCategory' => "AdvertCategory",
    "AdvertLayoutType" => "AdvertLayoutType"
 );


  public static $summary_fields = array(
    'Title' => 'Title',
    'AdvertCategory.Title',
    'AdvertLayoutType.Title',
    'StartDate' => 'StartDate',
    'FinishDate' => 'FinishDate'
 );


  // add an index in the db for the digital signature and date ranges
    static $indexes = array(
        'DigitalSignature' => true,
        'StartDate' => '(StartDate)',
        'FinishDate' => '(FinishDate)',
        'AdvertCategories' => '(AdvertCategoryID)'
    );





  function getCMSFields() {

    Requirements::javascript('weboftalent-adverts/javascript/advertedit.js');
    
     // throw away the scaffolding and start afresh
    $fields = new FieldList();

    // add a main tab
    $fields->push(new TabSet("Root", $mainTab = new Tab("Main")));
    $mainTab->setTitle(_t('SiteTree.TABMAIN', "Main"));


    // human readable title
    $fields->addFieldToTab('Root.Main',  new TextField('Title', 
                                'Human readable title for the advert'));

    // a Javascript toggle on this field displays either the adbroker text field, or an image with URL
    $fields->addFieldToTab('Root.Main', new DropdownField('AdvertSource', 'The type of advert',
        singleton('Advert')->dbObject('AdvertSource')->enumValues()
   ));

    if ($this->ID == 0) {
        $html = '<div class="field text">An image can be uploaded after the advert is saved for the first time</div>';
        $fields->addFieldToTab('Root.Main', new LiteralField('ImageInfo', $html));
    } else {
       $fields->addFieldToTab('Root.Main', $imageUploader = new UploadField('AdvertImage', 
        'Image that will be shown as the actual advert'));
        Folder::find_or_make('ads');
        $imageUploader->setFolderName('ads'
        ); 
    }
    


    // quick tags, faster than the grid editor - these are processed prior to save to create/assign tags
    $fields->addFieldToTab('Root.Main',  new TextField('WebsiteLink', 
                                'The URL that will be shown when the advert image is clicked'));

    $fields->addFieldToTab('Root.Main',  new TextareaField('AdbrokerJavascript', 
                                'JavaScript provided by the adbroker'));

    $fields->addFieldToTab('Root.Main', $sdf = new DateField('StartDate', 'The date the advert becomes active'));
    $fields->addFieldToTab('Root.Main', $fdf = new DateField('FinishDate', 'The date the advert becomes inactive'));
    $sdf->setConfig('showcalendar', true);
    $fdf->setConfig('showcalendar', true);


    $categoryfield = new DropdownField('AdvertCategoryID', 'Category', AdvertCategory::get()->sort('Title')->map('ID', 'Title'));
    $categoryfield->setEmptyString('(Select one)');

    $layoutfield = new DropdownField('AdvertLayoutTypeID', 'Layout Type', AdvertLayoutType::get()->sort('Title')->map('ID', 'Title'));
    $layoutfield->setEmptyString('(Select one)');

    $fields->addFieldToTab('Root.Main', $categoryfield);
    $fields->addFieldToTab('Root.Main', $layoutfield);

    return $fields;
  }



  public function onBeforeWrite() {
    $this->DigitalSignature = $this->CalculateDigitalSignature();
    //error_log("DIG SIG:".$this->DigitalSignature);
    parent::onBeforeWrite();
  }


  /*
    Calculate a digital signature from several of the fields
  */
  public function CalculateDigitalSignature() {
    //error_log("Calculating dig sig");
    /* because we save the impression counter on every rendition this cannot include
    - number of impressions
    - last edited
    Otherwise the clickthrough will fail
    */
    $data = $this->ID.'_'.$this->AdvertCategory()->Title.'_'.$this->AdvertLayoutType()->Title.'_'.$this->AdbrokerJavascript;
    $data .= '_'.$this->StartDate.'_'.$this->FinishDate.'_'.$this->ClickThroughs.'_advert';
    $hashed = hash('sha512', $data);
    //error_log("HASH CREATED:".$hashed);
    return $hashed;
  }




}

?>