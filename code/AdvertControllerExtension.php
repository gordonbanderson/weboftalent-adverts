<?php

class AdvertControllerExtension extends DataExtension
{
    /*
    Store id to advert category to avoid multiple same queries
    */
    private static $cachedcategories = array();

    /*
    In order to prevent duplicate image adverts, stores those already served this request/response cycle
    */
    private static $advertsalreadyserved = array();

    public static $ctr = 1;

    /*
    Work out the appropriate category and render a random advert from that category
    @param #adverttype The type of advert, e.g. MPU or Skyscraper
    @param $prefix HTML prefix to the advert, e.g an li wrapper
    @param $suffix HTML suffix to the advert
    @param $numberofads - the number of adverts to search for.  Normally 1 but skyscraper needs 2
    @param $showonajax - set this to false to hide adverts on ajax
    */
    public function RenderAdvert($cachekey, $adverttype,
        $template = 'InlineAdvert', $numberofads = 1, $showonajax = true)
    {
        // If we are using ajax and showonajax is set to false, return no ad
        if (Director::is_ajax()) {
            if ($showonajax !== true) {
                return '';
            }
        }

        // this is from the advert model extension so will be in place
        $advertcategoryid = $this->owner->CalculateAdvertCategoryID();

        if (isset($cachedcategories[$advertcategoryid])) {
            $advertcategory = $cachedcategories[$advertcategoryid];
        }

        if (!isset($advertcategory)) {
            $advertcategory = AdvertCategory::get()->byID($advertcategoryid);
            $cachedcategories[$advertcategoryid] = $advertcategory;
        }

        // check if the category is enabled, if not return a blank
        if (isset($advertcategory) && $advertcategory->Enabled) {
            $where = "AdvertLayoutType.Title = '$adverttype'";

            if (isset($advertcategory)) {
                $where .= ' AND Advert.AdvertCategoryID = '.$advertcategoryid;
                $where .= ' AND (StartDate IS NULL OR !StartDate OR StartDate < NOW()) AND (FinishDate IS NULL OR !FinishDate OR NOW() < FinishDate)';

                if (count(self::$advertsalreadyserved) > 0) {
                    $csv = implode(',', array_keys(self::$advertsalreadyserved));
                    $where .= " and Advert.ID not in ($csv)";
                }
            }

            $adverts = Advert::get()->
                    innerJoin('AdvertLayoutType', 'Advert.AdvertLayoutTypeID = AdvertLayoutType.ID')
                    ->innerJoin('AdvertCategory', 'Advert.AdvertCategoryID = AdvertCategory.ID')

                    // filter does not work here, use where instead
                    ->where($where)
                    ->sort('RAND()')->limit($numberofads);

            $firstad = null;

            foreach ($adverts->getIterator() as $advert) {
                self::$advertsalreadyserved[$advert->ID] = $advert->ID;
                if ($firstad === null) {
                    $firstad = $advert;
                }
            }

            $forTemplate = new ArrayData(array(
                'Adverts' => $adverts,
                'Advert' => $firstad,
                'CacheKey' => $cachekey,
            ));

            return $forTemplate->renderWith($template);
        } else {
            // return a blank if the cateogry is not enabled
            return '';
        }
    }
}
