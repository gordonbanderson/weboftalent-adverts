<% if $Advert %>
<div class="advert advert-single advert-mpu">
	<h3 class="legend advert-legend">Advertisement</h3>
<% if $Advert.AdvertSource == 'UploadedImage' %>
	<div class="{$Advert.AdvertLayoutType.Title}Advert"><a href="/advert/click/$Advert.DigitalSignature" target="_blank"><% if $Advert.AdvertImage %><img style="width:{$Advert.AdvertLayoutType.Width}px; height:{$Advert.AdvertLayoutType.Height}px;" src="/advert/image/$Advert.DigitalSignature" alt="$Advert.Title"/><% else %>
	<img src="http://placehold.it/{$Advert.AdvertLayoutType.Width}x{$Advert.AdvertLayoutType.Height}" alt="$Advert.Title"/><% end_if %></a>
	</div>
<% else %>
<div class="{$Advert.AdvertLayoutType.Title}Advert-broker">
$Advert.AdbrokerJavascript.RAW
</div>
<% end_if %>
</div>
<% end_if %>