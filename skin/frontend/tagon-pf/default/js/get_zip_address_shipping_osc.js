var getZipAddressShipping = function(url, zip)
{
	if (zip.length != 9) {
		jQuery('#shipping\\:postcode').val("");
		jQuery('#shipping\\:street1').val("");
		jQuery('#shipping\\:street2').val("");
		jQuery('#shipping\\:street3').val("");
		jQuery('#shipping\\:street4').val("");
		jQuery('#shipping\\:city').val("");
		//jQuery("#shipping\\:region_id").selectBox('value', 0);
		jQuery("#shipping\\:region_id").val(""); 
		return false;
	}

	url = url+'republicavirtual/ajax/endereco';

    var request = new Ajax.Request(url,
		{
		    method: 'post',
			onCreate: function(){ 
				jQuery('#loading-postcode-shipping').show();
				
				jQuery('#msg-cep-shipping').remove();
				
				jQuery('#shipping\\:street1').val('');
				jQuery('#shipping\\:street1').attr('readonly', true);
				jQuery('#shipping\\:street1').attr('disabled', true);
				jQuery('#shipping\\:street1').css('background-color', '#cccccc');

				jQuery('#shipping\\:street4').val('');
				jQuery('#shipping\\:street4').attr('readonly', true);
				jQuery('#shipping\\:street4').attr('disabled', true);
				jQuery('#shipping\\:street4').css('background-color', '#cccccc');
				
				jQuery('#shipping\\:city').val('');
				jQuery('#shipping\\:city').attr('readonly', true);
				jQuery('#shipping\\:city').attr('disabled', true);
				jQuery('#shipping\\:city').css('background-color', '#cccccc');
				
				jQuery('#shipping\\:region_id').val('');
				jQuery('#shipping\\:region_id').attr('readonly', true);
				jQuery('#shipping\\:region_id').attr('disabled', true);
				jQuery('#shipping\\:region_id').css('background-color', '#cccccc');
			},
		    onSuccess: function(res){
				jQuery('#loading-postcode-shipping').hide();
				
				jQuery('#shipping\\:street1').removeAttr('readonly');
				jQuery('#shipping\\:street1').removeAttr('disabled');
				jQuery('#shipping\\:street1').css('background-color', '#FFFFFF');
				
				jQuery('#shipping\\:street4').removeAttr('readonly');
				jQuery('#shipping\\:street4').removeAttr('disabled');
				jQuery('#shipping\\:street4').css('background-color', '#FFFFFF');
				
				jQuery('#shipping\\:city').removeAttr('readonly');
				jQuery('#shipping\\:city').removeAttr('disabled');
				jQuery('#shipping\\:city').css('background-color', '#FFFFFF');
				
				jQuery('#shipping\\:region_id').removeAttr('readonly');
				jQuery('#shipping\\:region_id').removeAttr('disabled');
				jQuery('#shipping\\:region_id').css('background-color', '#FFFFFF');
				
		    	var data = res.responseText.evalJSON();
		    	if(data.resultado == 1)
		    	{
		    		if(typeof(data.tipo_logradouro) == 'string' && typeof(data.logradouro) == 'string')jQuery('#shipping\\:street1').val(data.tipo_logradouro + ' ' + data.logradouro);
		    		if(typeof(data.cidade) == 'string')jQuery('#shipping\\:city').val(data.cidade);
		    		if(typeof(data.bairro) == 'string')jQuery('#shipping\\:street4').val(data.bairro);
		    		//if(typeof(data.uf_id) == 'string')jQuery("#shipping\\:region_id").selectBox('value', data.uf_id);
		    		if(typeof(data.uf_id) == 'string')jQuery('#shipping\\:region_id').val(data.uf_id);
					jQuery('#shipping\\:street2').focus();
					
					get_save_billing_function(URL_OSC+'onestepcheckout/ajax/save_billing/', URL_OSC+'onestepcheckout/ajax/set_methods_separate/', true)();
		    	} else if(data.resultado == 2)
		    	{
		    		if(typeof(data.tipo_logradouro) == 'string' && typeof(data.logradouro) == 'string')jQuery('#shipping\\:street1').val(data.tipo_logradouro + ' ' + data.logradouro);
		    		if(typeof(data.cidade) == 'string')jQuery('#shipping\\:city').val(data.cidade);
		    		if(typeof(data.bairro) == 'string')jQuery('#shipping\\:street4').val(data.bairro);
		    		//if(typeof(data.uf_id) == 'string')jQuery("#shipping\\:region_id").selectBox('value', data.uf_id);
		    		if(typeof(data.uf_id) == 'string')jQuery('#shipping\\:region_id').val(data.uf_id);
					jQuery('#shipping\\:street1').focus();
					
					get_save_billing_function(URL_OSC+'onestepcheckout/ajax/save_billing/', URL_OSC+'onestepcheckout/ajax/set_methods_separate/', true)();
		    	} else {
					jQuery('#shipping\\:street1').val("");
					jQuery('#shipping\\:street2').val("");
					jQuery('#shipping\\:street3').val("");
					jQuery('#shipping\\:street4').val("");
					jQuery('#shipping\\:city').val("");
					//jQuery("#shipping\\:region_id").selectBox('value', 0);
					jQuery("#shipping\\:region_id").val(""); 
					
					jQuery('#shipping\\:street1').focus();
					
//					get_save_billing_function(URL_OSC+'onestepcheckout/ajax/save_billing/', URL_OSC+'onestepcheckout/ajax/set_methods_separate/', true)();
				}
		    },
			onFailure: function(){ 
				jQuery('#loading-postcode-shipping').hide();
				
				jQuery('#shipping\\:street1').removeAttr('readonly');
				jQuery('#shipping\\:street1').removeAttr('disabled');
				jQuery('#shipping\\:street1').css('background-color', '#FFFFFF');
				
				jQuery('#shipping\\:street4').removeAttr('readonly');
				jQuery('#shipping\\:street4').removeAttr('disabled');
				jQuery('#shipping\\:street4').css('background-color', '#FFFFFF');
				
				jQuery('#shipping\\:city').removeAttr('readonly');
				jQuery('#shipping\\:city').removeAttr('disabled');
				jQuery('#shipping\\:city').css('background-color', '#FFFFFF');
				
				jQuery('#shipping\\:region_id').removeAttr('readonly');
				jQuery('#shipping\\:region_id').removeAttr('disabled');
				jQuery('#shipping\\:region_id').css('background-color', '#FFFFFF');
			
				jQuery('#shipping\\:street1').focus();
				
				//get_save_billing_function(URL_OSC+'onestepcheckout/ajax/save_billing/', URL_OSC+'onestepcheckout/ajax/set_methods_separate/', true)();
			}, 
		    parameters: {cep:zip},
		}
    );	
};//getZipAddress

jQuery(document).ready(function(){
	jQuery('#shipping\\:postcode').blur(function(){
		getZipAddressShipping(URL_OSC, jQuery(this).val());
	});
});