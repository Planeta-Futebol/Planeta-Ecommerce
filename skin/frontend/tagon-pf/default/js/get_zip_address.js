var getZipAddress = function(url, zip)
{
	if (zip.length != 9) {
		jQuery('#zip').val("");
		jQuery('#street_1').val("");
		jQuery('#street_2').val("");
		jQuery('#street_3').val("");
		jQuery('#street_4').val("");
		jQuery('#city').val("");
		//jQuery("#region_id").selectBox('value', 0);
		jQuery("#region_id").val(""); 		
		return false;
	}
	
	url = url+'republicavirtual/ajax/endereco';

    var request = new Ajax.Request(url,
		{
		    method: 'post',
			onCreate: function(){ 
				jQuery('#loading-postcode').show();
				jQuery('#street_1').val('');
				jQuery('#street_1').attr('readonly', true);
				jQuery('#street_1').attr('disabled', true);
				jQuery('#street_1').css('background-color', '#cccccc');

				jQuery('#street_4').val('');
				jQuery('#street_4').attr('readonly', true);
				jQuery('#street_4').attr('disabled', true);
				jQuery('#street_4').css('background-color', '#cccccc');
				
				jQuery('#city').val('');
				jQuery('#city').attr('readonly', true);
				jQuery('#city').attr('disabled', true);
				jQuery('#city').css('background-color', '#cccccc');
				
				jQuery('#region_id').val('');
				jQuery('#region_id').attr('readonly', true);
				jQuery('#region_id').attr('disabled', true);
				jQuery('#region_id').css('background-color', '#cccccc');
			},
		    onSuccess: function(res){
				jQuery('#loading-postcode').hide();
				
				jQuery('#street_1').removeAttr('readonly');
				jQuery('#street_1').removeAttr('disabled');
				jQuery('#street_1').css('background-color', '#FFFFFF');
				
				jQuery('#street_4').removeAttr('readonly');
				jQuery('#street_4').removeAttr('disabled');
				jQuery('#street_4').css('background-color', '#FFFFFF');
				
				jQuery('#city').removeAttr('readonly');
				jQuery('#city').removeAttr('disabled');
				jQuery('#city').css('background-color', '#FFFFFF');
				
				jQuery('#region_id').removeAttr('readonly');
				jQuery('#region_id').removeAttr('disabled');
				jQuery('#region_id').css('background-color', '#FFFFFF');
				
		    	var data = res.responseText.evalJSON();
		    	if(data.resultado == 1)
		    	{
		    		if(typeof(data.tipo_logradouro) == 'string' && typeof(data.logradouro) == 'string')jQuery('#street_1').val(data.tipo_logradouro + ' ' + data.logradouro);
		    		if(typeof(data.cidade) == 'string')jQuery('#city').val(data.cidade);
		    		if(typeof(data.bairro) == 'string')jQuery('#street_4').val(data.bairro);
		    		//if(typeof(data.uf_id) == 'string')jQuery("#region_id").selectBox('value', data.uf_id); 
		    		if(typeof(data.uf_id) == 'string')jQuery('#region_id').val(data.uf_id);
					jQuery('#street_2').focus();
		    	} else if(data.resultado == 2)
		    	{
		    		if(typeof(data.tipo_logradouro) == 'string' && typeof(data.logradouro) == 'string')jQuery('#street_1').val(data.tipo_logradouro + ' ' + data.logradouro);
		    		if(typeof(data.cidade) == 'string')jQuery('#city').val(data.cidade);
		    		if(typeof(data.bairro) == 'string')jQuery('#street_4').val(data.bairro);
		    		//if(typeof(data.uf_id) == 'string')jQuery("#region_id").selectBox('value', data.uf_id); 
		    		if(typeof(data.uf_id) == 'string')jQuery('#region_id').val(data.uf_id);
					jQuery('#street_1').focus();
		    	} else {
					jQuery('#street_1').val("");
					jQuery('#street_2').val("");
					jQuery('#street_3').val("");
					jQuery('#street_4').val("");
					jQuery('#city').val("");
					//jQuery("#region_id").selectBox('value', 0);
					jQuery("#region_id").val(""); 		
					alert('CEP não encontrado, por favor tente novamente.')
				}
		    },
			onFailure: function(){ 
				jQuery('#loading-postcode').hide();
				
				jQuery('#street_1').removeAttr('readonly');
				jQuery('#street_1').removeAttr('disabled');
				jQuery('#street_1').css('background-color', '#edf7fd');
				
				jQuery('#street_4').removeAttr('readonly');
				jQuery('#street_4').removeAttr('disabled');
				jQuery('#street_4').css('background-color', '#FFFFFF');
				
				jQuery('#city').removeAttr('readonly');
				jQuery('#city').removeAttr('disabled');
				jQuery('#city').css('background-color', '#FFFFFF');
				
				jQuery('#region_id').removeAttr('readonly');
				jQuery('#region_id').removeAttr('disabled');
				jQuery('#region_id').css('background-color', '#FFFFFF');
			
				alert('CEP inválido, por favor tente novamente.\n\nCaso o problema persista:\nProssiga com o cadastro preenchendo todos os campos.');
			}, 
		    parameters: {cep:zip},
		}
    );	
};//getZipAddress

jQuery(document).ready(function(){
	jQuery('#zip').blur(function(){
		getZipAddress(URL_ZIP, jQuery(this).val());
	});
});