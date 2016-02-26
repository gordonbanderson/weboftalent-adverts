<?php

/**
 * Each advert belongs to a category.  The category of a page, or one an ancestor page (e.g. parent, grandparent etc)
 * determines the advert category selected for rendering.
 */
class AdvertCategory extends DataObject
{
    public static $searchable_fields = array(
    'Title',
  );

    public static $has_many = array(
    'Adverts' => 'Advert',
  );

    public static $db = array(
    // name of the category
    'Title' => 'Varchar(255)',

    // whether or not ads are enabled
    'Enabled' => 'Boolean',

  );

    public static $summary_fields = array(
    'Title' => 'Title',
    'Enabled' => 'Enabled',
  );

    public function getCMSFields()
    {
        $fields = new FieldList();

    // add a tab
    $fields->push(new TabSet('Root', $mainTab = new Tab('Main')));
        $mainTab->setTitle(_t('SiteTree.TABMAIN', 'Main'));

        $fields->addFieldToTab('Root.Main',  new TextField('Title', 'The name of the category'));
        $fields->addFieldToTab('Root.Main', new CheckboxField('Enabled', 'Are adverts for this category enabled?'));

        return $fields;
    }
}
