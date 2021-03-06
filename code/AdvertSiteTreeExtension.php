<?php

class AdvertSiteTreeExtension extends DataExtension
{
    /*
    Any page in the site tree can have an advert category.  This is used to determine the category
    of adverts served in the tree of pages below it.  THe cached value is used to avoid having to
    recalculate it by traversing the tree each time.
    */
    public static $has_one = array(
        'AdvertCategory' => 'AdvertCategory',
        'CachedAdvertCategory' => 'AdvertCategory',

        // it is possible that a cached value may be null, so in order to avoid traversing the tree
        // each page load, use this flag first
        'IsCached' => 'Boolean',
    );

    /*
    Add a Location tab containing the map
    */
    public function updateCMSFields(FieldList $fields)
    {
        $categoryfield = new DropdownField('AdvertCategoryID', 'Category', AdvertCategory::get()->sort('Title')->map('ID', 'Title'));
        $categoryfield->setEmptyString('(Select one)');
        $fields->addFieldToTab('Root.AdvertCategory', $categoryfield);
    }

    /*
    Prior to an item being saved, check for the category having being changed.  If so we need to clear the category cache for all items in the database, and cache this one.
    */
    public function onBeforeWrite()
    {
        $savedpage = SiteTree::get()->byID($this->owner->ID);
        if ($savedpage && $savedpage->AdvertCategoryID != $this->owner->AdvertCategoryID) {
            // clear caching for live and stage subtree
            DB::query('update SiteTree_Live set IsCachedID = 0, CachedAdvertCategoryID = 0;');
            DB::query('update SiteTree set IsCachedID = 0, CachedAdvertCategoryID = 0;');
        }
        parent::onBeforeWrite();
    }

  /*
    Get the advert category.  Either use the cached advert category, or traverse the tree towards the root looking for it
  */
    public function CalculateAdvertCategoryID()
    {
        // use the cached value
        $result = $this->owner->CachedAdvertCategoryID;

        // if the record is not marked as cached, see if it has a category
        if (!$this->owner->IsCached) {
            if ($this->owner->AdvertCategoryID > 0) {
                $this->owner->CachedAdvertCategoryID = $this->owner->AdvertCategoryID;
                $this->owner->IsCached = true;
                $this->owner->write;
                $result = $this->owner->CachedAdvertCategoryID;
            } else {
                // otherwise traverse each parent in turn until reaching the root of the site, i.e. no parent

                $parent = $this->owner->Parent();
                if ($parent) {
                    while (true) {
                        if ($parent->IsCached || $parent->AdvertCategoryID > 0) {
                            $this->owner->AdvertCategoryID = $parent->AdvertCategoryID;
                            $this->owner->IsCached = true;
                            $this->owner->write();
                            $result = $this->owner->AdvertCategoryID;
                            break;
                        }
                        $parent = SiteTree::get()->byId($parent->ParentID);
                        if (!$parent) {
                            break;
                        }
                    }
                }
            }
        }
        return $result;
    }
}
