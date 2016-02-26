<?php

class AdvertAdmin extends ModelAdmin {
    
  public static $managed_models = array(   //since 2.3.2
      'Advert','AdvertCategory','AdvertLayoutType'
   );
 
  static $url_segment = 'adverts'; // will be linked as /admin/adverts
  static $menu_title = 'Adverts';
 
}

?>