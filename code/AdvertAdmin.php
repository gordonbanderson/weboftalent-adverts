<?php

class AdvertAdmin extends ModelAdmin
{
    public static $managed_models = array(   //since 2.3.2
        'Advert', 'AdvertCategory', 'AdvertLayoutType',
    );

    public static $url_segment = 'adverts'; // will be linked as /admin/adverts

    public static $menu_title = 'Adverts';
}
