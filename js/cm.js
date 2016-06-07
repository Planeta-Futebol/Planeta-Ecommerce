document.observe("dom:loaded", function() {  
	if($$('#creditmemo_item_container .data.order-tables .update-button')[0]){
		YourName.init();
	};
});

var YourName = {
    init : function(){
        var my_div = document.createElement('div');
        Element.extend(my_div);
        my_div.addClassName('form-button');
        my_div.setStyle({
           display:'inline-block',
           marginLeft: '3px',
           padding: '2px 7px 3px 7px'
        });
        my_div.innerHTML ='Set Qtys to 0';
        my_div.observe('click', YourName.refresh);
        $$('#creditmemo_item_container .data.order-tables .update-button')[0].insert({after:my_div});
    },
    refresh : function(){
        $$('input.input-text.qty-input').each(function(item) {      
			item.value='0';
			});
        $('shipping_amount').value='0';
		$$('#creditmemo_item_container .data.order-tables .update-button').each(function (elem) {elem.disabled=false;elem.removeClassName('disabled');});
        $$('#creditmemo_item_container .data.order-tables .update-button')[0].click();
    }
};