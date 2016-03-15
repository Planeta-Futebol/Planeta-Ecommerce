function dispatchLiveUpdates(type, element){
	if (type=='list_item') {
		if (jQueryAC(element).find('.hover .ajaxcartx-qty').length==0) {
			jQueryAC(element).find('.regular .ajaxcartx-qty').insertAfter(".hover .button-container");
		}
		jQueryAC('.regular .ajaxcartx-qty').remove();
		Event.observe(window, 'load', function() { 
			if (jQueryAC(element).find('.hover .ajaxcartx-qty').length==0) {
				jQueryAC(element).find('.regular .ajaxcartx-qty').insertAfter(".hover .button-container");
			}
			jQueryAC('.regular .ajaxcartx-qty').remove();
		});
	}
}

function dispatchJump(imageContainerClone){
	jQueryAC("#ac-popup-top-bkg").css({'display':'none'})
}

