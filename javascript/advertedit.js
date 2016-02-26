/*jslint white: true */
(function($) {
	$(document).ready(function() {

		$('#Form_ItemEditForm_AdvertSource').entwine({
			onchange: function(e) {
				var sel = $(e.target); // the selection dropdown
				if(sel.val() == 'UploadedImage') {
					$('#Form_ItemEditForm_WebsiteLink_Holder').attr('style', 'display:block');
					$('#Form_ItemEditForm_AdvertImage_Holder').attr('style', 'display:block');
					$('#Form_ItemEditForm_AdbrokerJavascript_Holder').attr('style', 'display:none');
				} else {
					$('#Form_ItemEditForm_WebsiteLink_Holder').attr('style', 'display:none');
					$('#Form_ItemEditForm_AdvertImage_Holder').attr('style', 'display:none');
					$('#Form_ItemEditForm_AdbrokerJavascript_Holder').attr('style', 'display:block');
				}
			},

			// FIXME - what is the right event here?
			onmatch: function(e) {
				var sel = $('#Form_ItemEditForm_AdvertSource');

				// hide either the internal or external link editing box depending on which link type the link is
				// (internal or external)
				if(sel.val() == 'UploadedImage') {
					$('#Form_ItemEditForm_AdbrokerJavascript_Holder').attr('style', 'display:none');
				} else if (sel.val() == 'AdbrokerJavascript') {
					$('#Form_ItemEditForm_WebsiteLink_Holder').attr('style', 'display:none');
					$('#Form_ItemEditForm_AdvertImage_Holder').attr('style', 'display:none');
				}
			}
		});


	});
})(jQuery);
