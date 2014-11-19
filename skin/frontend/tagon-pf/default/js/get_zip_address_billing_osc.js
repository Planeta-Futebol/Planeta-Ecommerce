var getZipAddressBilling = function(url, zip)
{
	if (zip.length != 9) {
		jQuery('#billing\\:postcode').val("");
		jQuery('#billing\\:street1').val("");
		jQuery('#billing\\:street2').val("");
		jQuery('#billing\\:street3').val("");
		jQuery('#billing\\:street4').val("");
		jQuery('#billing\\:city').val("");
		//jQuery("#billing\\:region_id").selectBox('value', 0);
		jQuery("#billing\\:region_id").val(""); 
		return false;
	}
	
	url = url+'republicavirtual/ajax/endereco';

    var request = new Ajax.Request(url,
		{
		    method: 'post',
			onCreate: function(){ 
				jQuery('#loading-postcode-billing').show();
				
				jQuery('#msg-cep-billing').remove();
				
				jQuery('#billing\\:street1').val('');
				jQuery('#billing\\:street1').attr('readonly', true);
				jQuery('#billing\\:street1').attr('disabled', true);
				jQuery('#billing\\:street1').css('background-color', '#cccccc');

				jQuery('#billing\\:street4').val('');
				jQuery('#billing\\:street4').attr('readonly', true);
				jQuery('#billing\\:street4').attr('disabled', true);
				jQuery('#billing\\:street4').css('background-color', '#cccccc');
				
				jQuery('#billing\\:city').val('');
				jQuery('#billing\\:city').attr('readonly', true);
				jQuery('#billing\\:city').attr('disabled', true);
				jQuery('#billing\\:city').css('background-color', '#cccccc');
				
				jQuery('#billing\\:region_id').val('');
				jQuery('#billing\\:region_id').attr('readonly', true);
				jQuery('#billing\\:region_id').attr('disabled', true);
				jQuery('#billing\\:region_id').css('background-color', '#cccccc');
			},
		    onSuccess: function(res){
				jQuery('#loading-postcode-billing').hide();
				
				jQuery('#billing\\:street1').removeAttr('readonly');
				jQuery('#billing\\:street1').removeAttr('disabled');
				jQuery('#billing\\:street1').css('background-color', '#FFFFFF');
				
				jQuery('#billing\\:street4').removeAttr('readonly');
				jQuery('#billing\\:street4').removeAttr('disabled');
				jQuery('#billing\\:street4').css('background-color', '#FFFFFF');
				
				jQuery('#billing\\:city').removeAttr('readonly');
				jQuery('#billing\\:city').removeAttr('disabled');
				jQuery('#billing\\:city').css('background-color', '#FFFFFF');
				
				jQuery('#billing\\:region_id').removeAttr('readonly');
				jQuery('#billing\\:region_id').removeAttr('disabled');
				jQuery('#billing\\:region_id').css('background-color', '#FFFFFF');
				
		    	var data = res.responseText.evalJSON();
		    	if(data.resultado == 1)
		    	{
		    		if(typeof(data.tipo_logradouro) == 'string' && typeof(data.logradouro) == 'string')jQuery('#billing\\:street1').val(data.tipo_logradouro + ' ' + data.logradouro);
		    		if(typeof(data.cidade) == 'string')jQuery('#billing\\:city').val(data.cidade);
		    		if(typeof(data.bairro) == 'string')jQuery('#billing\\:street4').val(data.bairro);
		    		//if(typeof(data.uf_id) == 'string')jQuery("#billing\\:region_id").selectBox('value', data.uf_id); 
		    		if(typeof(data.uf_id) == 'string')jQuery('#billing\\:region_id').val(data.uf_id);
					jQuery('#billing\\:street2').focus();
					
					get_save_billing_function(URL_OSC+'onestepcheckout/ajax/save_billing/', URL_OSC+'onestepcheckout/ajax/set_methods_separate/', true)();
		    	} else if(data.resultado == 2)
		    	{
		    		if(typeof(data.tipo_logradouro) == 'string' && typeof(data.logradouro) == 'string')jQuery('#billing\\:street1').val(data.tipo_logradouro + ' ' + data.logradouro);
		    		if(typeof(data.cidade) == 'string')jQuery('#billing\\:city').val(data.cidade);
		    		if(typeof(data.bairro) == 'string')jQuery('#billing\\:street4').val(data.bairro);
		    		//if(typeof(data.uf_id) == 'string')jQuery("#billing\\:region_id").selectBox('value', data.uf_id); 
		    		if(typeof(data.uf_id) == 'string')jQuery('#billing\\:region_id').val(data.uf_id);
					jQuery('#billing\\:street1').focus();
					
					get_save_billing_function(URL_OSC+'onestepcheckout/ajax/save_billing/', URL_OSC+'onestepcheckout/ajax/set_methods_separate/', true)();
		    	} else {
					jQuery('#billing\\:street1').val("");
					jQuery('#billing\\:street2').val("");
					jQuery('#billing\\:street3').val("");
					jQuery('#billing\\:street4').val("");
					jQuery('#billing\\:city').val("");
					//jQuery("#billing\\:region_id").selectBox('value', 0);
					jQuery("#billing\\:region_id").val("");
					
					jQuery('#billing\\:street1').focus();
					
					//get_save_billing_function(URL_OSC+'onestepcheckout/ajax/save_billing/', URL_OSC+'onestepcheckout/ajax/set_methods_separate/', true)();
				}
		    },
			onFailure: function(){ 
				jQuery('#loading-postcode-billing').hide();
				
				jQuery('#billing\\:street1').removeAttr('readonly');
				jQuery('#billing\\:street1').removeAttr('disabled');
				jQuery('#billing\\:street1').css('background-color', '#FFFFFF');
				
				jQuery('#billing\\:street4').removeAttr('readonly');
				jQuery('#billing\\:street4').removeAttr('disabled');
				jQuery('#billing\\:street4').css('background-color', '#FFFFFF');
				
				jQuery('#billing\\:city').removeAttr('readonly');
				jQuery('#billing\\:city').removeAttr('disabled');
				jQuery('#billing\\:city').css('background-color', '#FFFFFF');
				
				jQuery('#billing\\:region_id').removeAttr('readonly');
				jQuery('#billing\\:region_id').removeAttr('disabled');
				jQuery('#billing\\:region_id').css('background-color', '#FFFFFF');
			
				jQuery('#billing\\:street1').focus();
				
				//get_save_billing_function(URL_OSC+'onestepcheckout/ajax/save_billing/', URL_OSC+'onestepcheckout/ajax/set_methods_separate/', true)();
			}, 
		    parameters: {cep:zip},
		}
    );	
};//getZipAddress

jQuery(document).ready(function(){
	jQuery('#billing\\:postcode').blur(function(){
		getZipAddressBilling(URL_OSC, jQuery(this).val());
	});
});