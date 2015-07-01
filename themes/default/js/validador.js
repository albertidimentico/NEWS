// VALIDADO DE FORMULARIOS

	$().ready(function() {
	//VALIDADO DE FORMULARIO
	$("#form").validate({
		rules: {
			"nombre": "required",	
			"nif":{ required: true, minlength: 9 },
			"telefono":{required:true, number:true, minlength: 9},
			"email": { required: true, email: true },
			"direccion": "required",
			"contraseña": "required",
			"contraseña_pass": { required: true, equalTo:"#contraseña", minlength: 6 },
	
		},
		messages: {
			"nombre": "POR FAVOR,INDIQUE EL NOMBRE.",
			"nif":  { required: 'DEBE INGRESAR UN NIF O PASAPORTE'},
			"telefono":{ required: 'DEBE INGRESAR UN NÚMERO', number:'EL NÚMERO NO ES VALIDO.', minlength:'EL TELÉFONO DEBE TENER 9 CARÁCTERES' },
			"email": { required:'DEBE INGRESAR UN CORREO ELÉCTRÓNICO', email:'DEBE INGRESAR UN CORREO CON EL FORMATO CORRECTO. POR EJEMPLO: EJEMPLO@EJEMPLO.COM' },
			"direccion": "DEBE INDICAR UNA DIRECCIÓN.",
			"contraseña": "DEBE ESPECIDICAR UNA CONTRASEÑA",
			"contraseña_pass":{ required: 'Debe ingresar la contraseña', equalTo: 'Debe ingresar la misma constraseña', minlength:'la contraseña debe tener mas 6' },
		}
		
		
		});
		
		
		$("#contacto_formulario").validate({
		rules: {
			"nombre": "required",	
			"nif":{ required: true, minlength: 9 },
			"telefono":{required:true, number:true, minlength: 9},
			"email": { required: true, email: true },
			
	
		},
		messages: {
			"nombre": "POR FAVOR,INDIQUE EL NOMBRE.",
			"nif":  { required: 'DEBE INGRESAR UN NIF O PASAPORTE'},
			"telefono":{ required: 'DEBE INGRESAR UN NÚMERO', number:'EL NÚMERO NO ES VALIDO.', minlength:'EL TELÉFONO DEBE TENER 9 CARÁCTERES' },
			"email": { required:'DEBE INGRESAR UN CORREO ELÉCTRÓNICO', email:'DEBE INGRESAR UN CORREO CON EL FORMATO CORRECTO. POR EJEMPLO: EJEMPLO@EJEMPLO.COM' },
			
		}
		});
		
		
		
		
	    $("#empleoformulario").validate({
		rules: {
			"nombre": "required",	
			"email": { required: true, email: true },
			"foto": "required",	
			"curriculum": "required",
			"nif": "required",
			"localidad": "required",
			"direccion": "required",
			"telefono": "required",
		    "movil": "required",
		},
		messages: {
			"nombre":  { required: 'DEBE INGRESAR UN NOMBRE'},
			"email": { required:'DEBE INGRESAR UN CORREO ELÉCTRÓNICO', email:'DEBE INGRESAR UN CORREO CON EL FORMATO CORRECTO. POR EJEMPLO: EJEMPLO@EJEMPLO.COM' },
			"foto":  { required: 'DEBE INDICAR UNA FOTOGRAFÍA'},
			"curriculum":  { required: 'DEBE INDICAR ARCHIVO'},
			"nif":  { required: 'DEBE INDICAR UN DOCUMENTO DE IDENTIDAD'},
			"localidad":  { required: 'DEBE INDICAR UNA LOCALIDAD'},
			"direccion":  { required: 'DEBE INDICAR UNA DIRECCIÓN'},
			"telefono":  { required: 'DEBE INDICAR UN TELÉFONO'},
			"movil":  { required: 'DEBE INDICAR UN TELÉFONO MOVIL'},
		}
		});
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	});
	
	