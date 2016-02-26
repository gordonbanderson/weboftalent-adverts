/*jslint white: true */
(function($) {
	$(document).ready(function() {
		
		$('#Form_ItemEditForm_AdvertSource').entwine({
			onchange: function(e) {
				var sel = $(e.target); // the selection dropdown
				if(sel.val() == 'UploadedImage') {
					$('#WebsiteLink').attr('style', 'display:block');
					$('#AdvertImage').attr('style', 'display:block');
					$('#AdbrokerJavascript').attr('style', 'display:none');
				} else {
					$('#WebsiteLink').attr('style', 'display:none');
					$('#AdvertImage').attr('style', 'display:none');
					$('#AdbrokerJavascript').attr('style', 'display:block');
				}
			},

			// FIXME - what is the right event here?
			onmatch: function(e) {
				var sel = $('#Form_ItemEditForm_AdvertSource');
	
				// hide either the internal or external link editing box depending on which link type the link is
				// (internal or external)
				if(sel.val() == 'UploadedImage') {
					$('#AdbrokerJavascript').attr('style', 'display:none');
				} else if (sel.val() == 'AdbrokerJavascript') {
					$('#WebsiteLink').attr('style', 'display:none');
					$('#AdvertImage').attr('style', 'display:none');
				}
			}
		});


	});
})(jQuery);