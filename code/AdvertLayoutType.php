<?php

/**
 * Each advert belongs to a category.  The category of a page, or one an ancestor page (e.g. parent, grandparent etc)
 * determines the advert category selected for rendering
 */
class AdvertLayoutType extends DataObject {


  static $searchable_fields = array(
    'Title',
  );


 


  static $db = array(
    // name of the layout type, e.g. 'main banner' or 'skyscraper'
    'Title' => 'Varchar(255)',

    // whether or not ads are enabled
    'Width' => 'Int',
    'Height' => 'Int'
  );



  public static $summary_fields = array(
    'Title' => 'Title',
    'Width' => 'Width',
    'Height' => 'Height'
  );

  static $has_many = array(
    'Adverts' => 'Advert'
  );




  function getCMSFields() {
    $fields = new FieldList();

    // add a tab
    $fields->push( new TabSet( "Root", $mainTab = new Tab( "Main" ) ) );
    $mainTab->setTitle( _t( 'SiteTree.TABMAIN', "Main" ) );


    $fields->addFieldToTab( 'Root.Main',  new TextField( 'Title', 'The name of the category') );
    $fields->addFieldToTab( 'Root.Main', new NumericField( 'Width', 'The width of the advert in pixels' )  );
    $fields->addFieldToTab( 'Root.Main', new NumericField( 'Height', 'The height of the advert in pixels' )  );

    return $fields;
  }

}

?>