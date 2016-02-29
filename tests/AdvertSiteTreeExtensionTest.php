<?php

class AdvertSiteTreeExtensionTest extends SapphireTest
{
    public static $fixture_file = 'adverts/tests/adverts.yml';

    /*
    Test child of a parent with a set advert category ID
     */
    public function testCalculateAdvertCategoryIDChild() {
        $sectionPage = $this->objFromFixture('Page', 'Section');
        $page = $this->objFromFixture('Page', 'SectionChild');
        $categoryID = $page->CalculateAdvertCategoryID();
        $this->assertEquals($sectionPage->AdvertCategoryID, $categoryID);
    }
}
