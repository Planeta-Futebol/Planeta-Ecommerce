jQuery(document).ready(function(){

	if (jQuery('#tipopessoa').val() == "Pessoa Jurídica") {
    	jQuery('#taxvat').mask('99.999.999/9999-99');
		jQuery('#group_rg').hide();
		jQuery('#group_ie').show();

	    Validation.add('validate-cnpj', 'CNPJ inválido', function(value, element)
	    {
			var cpf = value;
			cpf = cpf.replace(".","");
			cpf = cpf.replace(".","");
			cpf = cpf.replace("/","");
			cpf = cpf.replace("-","");

			if(cpf.length != 14)return this.optional(element);;

			var vCNPJ = cpf.substring(0,12);

		    var mControle = "";
		    var aTabCNPJ = new Array(5,4,3,2,9,8,7,6,5,4,3,2);
		    for (i = 1 ; i <= 2 ; i++){
			    mSoma = 0;
			    for (j = 0 ; j < vCNPJ.length ; j++)
				    mSoma = mSoma + (vCNPJ.substring(j,j+1) * aTabCNPJ[j]);

			    if (i == 2 ) mSoma = mSoma + ( 2 * mDigito );
			    mDigito = ( mSoma * 10 ) % 11;
			    if (mDigito == 10 ) mDigito = 0;
				    mControle1 = mControle ;
			    mControle = mDigito;
			    aTabCNPJ = new Array(6,5,4,3,2,9,8,7,6,5,4,3);
		    }
		    result = (mControle1 * 10) + mControle;

		    if ( cpf.substring(12,14) == result ) { return true; }

			return false;
	    });

		if (jQuery('#isento').attr('checked')) {
			// desabilita ie
			jQuery('#ie').val('ISENTO');
			jQuery('#ie').attr('readonly', true);
			jQuery('#ie').attr('disabled', true);
			jQuery('#ie').css('background-color', '#cccccc');
	    };

	} else {
    	jQuery('#taxvat').mask('999.999.999-99');
		jQuery('#group_rg').show();
		jQuery('#group_ie').hide();

	    Validation.add('validate-cpf', 'CPF inválido', function(value, element)
	    {
			var cpf = value;
			cpf = cpf.replace(".","");
			cpf = cpf.replace(".","");
			cpf = cpf.replace("-","");

			if(cpf.length != 11)return this.optional(element);;

			var d1_sum = 0;
			var d2_sum = 0;
			var calc_n = 11;
			var digit_1 = new Number;
			var digit_2 = new Number;
			for(var i = 0; i < 9; i++)d1_sum += cpf.charAt(i) * --calc_n;
			if((x = d1_sum % 11) < 2)digit_1 = 0;
				else digit_1 = 11 - x;

			if(cpf.charAt(9) != digit_1)return false;

			calc_n = 12;
			for(var i = 0; i < 10; i++)d2_sum += cpf.charAt(i) * --calc_n;
			if((x = d2_sum % 11) < 2)digit_2 = 0;
				else digit_2 = 11 - x;

			if(cpf.charAt(10) != digit_2)return false;

			if ((cpf === "00000000000") ||
				(cpf === "11111111111") ||
				(cpf === "22222222222") ||
				(cpf === "33333333333") ||
				(cpf === "44444444444") ||
				(cpf === "55555555555") ||
				(cpf === "66666666666") ||
				(cpf === "77777777777") ||
				(cpf === "88888888888") ||
				(cpf === "99999999999")) {
				return false;
			}

			return true;
	   });

	}

    var checkTipopessoa = function()
    {
    	if (jQuery('#tipopessoa').val() == "Pessoa Jurídica") {
    		jQuery('#taxvat').mask('99.999.999/9999-99');
    		jQuery('#group_rg').hide();
    		jQuery('#group_ie').show();
    	} else {
    		jQuery('#taxvat').mask('999.999.999-99');
    		jQuery('#group_rg').show();
    		jQuery('#group_ie').hide();
    	}
    };

    jQuery('#tipopessoa').change(checkTipopessoa);


	if (jQuery('#isento').attr('checked')) {
		// desabilita ie
		jQuery('#ie').val('ISENTO');
		jQuery('#ie').attr('readonly', true);
		jQuery('#ie').attr('disabled', true);
		jQuery('#ie').css('background-color', '#cccccc');
    };


    var checkIsento = function()
    {
    	if(jQuery(this).attr('checked')) {
			// desabilita ie
			jQuery('#ie').val('ISENTO');
			jQuery('#ie').attr('readonly', true);
			jQuery('#ie').attr('disabled', true);
			jQuery('#ie').css('background-color', '#cccccc');

    	} else {
    		jQuery('#ie').val('');
			jQuery('#ie').removeAttr('readonly');
			jQuery('#ie').removeAttr('disabled');
			jQuery('#ie').css('background-color', '#FFFFFF');
    	}
    };

    jQuery('#isento').change(checkIsento);

    jQuery('.phones').inputmask({
		mask: ['(99) 9999-9999', '(99) 99999-9999'],
		keepStatic: true,
		autoUnmask: true
	});

});