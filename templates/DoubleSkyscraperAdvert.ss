<% if $Adverts %>
<div class="advert-container advert-dualskyscrapers">
<h3 class="legend advert-legend">Advertisement</h3>
<% loop Adverts %>
<div class="advert advert-$Pos">
<% if $AdvertSource == 'UploadedImage' %>
<a href="/advert/click/$DigitalSignature" target="_blank"><% if $AdvertImage %><img style="width:{$AdvertLayoutType.Width}px; height:{$AdvertLayoutType.Height}px;"
 src="/advert/image/$DigitalSignature" alt="$Title"/><% else %>
<img src="http://placehold.it/{$AdvertLayoutType.Width}x{$AdvertLayoutType.Height}" alt="$Title"/><% end_if %></a>
<% else %>
$AdbrokerJavascript.RAW
<% end_if %>
</div>		
<% end_loop %>
</div>
<% end_if %>