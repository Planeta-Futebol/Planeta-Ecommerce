jQuery(document).ready(function(){
    jQuery('#telephone').mask('99 9999-9999');
    jQuery('#fax').mask('99 9999-9999?9');
    jQuery('#cpf').mask('999.999.999-99');
    
    Validation.add('validate-cpf', 'CPF inválido', function(value, element){
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
    
    jQuery('.validate-zip-international').mask('99999-999');
});