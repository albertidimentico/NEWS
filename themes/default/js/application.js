			
			
			/*C IMG*/
			$("#menu_enlace0").mouseover(function(){
				$("#menu_img1").attr('src', 'HTTP://sunglasscity.es/app/WEB/theme/default/img/menu/parquecristobal.jpg');
			});			
			
			$("#menu_enlace13").mouseover(function(){
				$("#menu_img1").attr('src', 'HTTP://sunglasscity.es/app/WEB/theme/default/img/menu/oasis.jpg');
			});	
			
			$("#menu_enlace14").mouseover(function(){
				$("#menu_img1").attr('src', 'HTTP://sunglasscity.es/app/WEB/theme/default/img/menu/americasplaza.jpg');
			});	
			
			$(document).ready(function()
			{
				//RESDES SOCIALES
				$("#social-team-member-1 li, #social-team-member-2 li, #social-team-member-3 li, #social-team-member-4 li").each(function() {
					$("a strong", this).css("opacity", "0");
				});
				
				$("#social-team-member-1 li, #social-team-member-2 li, #social-team-member-3 li, #social-team-member-4 li").hover(function() { // Mouse over
					$(this)
						.stop().fadeTo(500, 1)
						.siblings().stop().fadeTo(500, 0.2);
						
					$("a strong", this)
						.stop()
						.animate({
						opacity: 1,
							top: "-10px"
						}, 300);
					
				}, function() { //SALIDA DEL MOUSE
					$(this)
						.stop().fadeTo(500, 1)
						.siblings().stop().fadeTo(500, 1);
						
					$("a strong", this)
						.stop()
						.animate({
							opacity: 0,
							top: "-1px"
						}, 300);
				});
			});


			// M Y O VENTANAS
			$(document).ready(function(){
					$("#ocultar").click(function(){$("#msgid1").hide("slow")});
					$("#mostrar").click(function(){$("#msgid1").show("slow")});
			
			});

				$(document).ready(function () {
			$("#ventana_parquecristobal").click(function () { 
				  $("#ventana_info_parquecristobal").css("display","block");
				   
			});
				$("#close_parquecristobal").click(function () { 
				  $("#ventana_info_parquecristobal").css("display","none");
				  
			});
			});
			
			$(document).ready(function () {
			$("#ventana_oasis").click(function () { 
				  $("#ventana_info_oasis").css("display","block");
				   
			});
				$("#close_oasis").click(function () { 
				  $("#ventana_info_oasis").css("display","none");
				  
			});
			});
			
			$(document).ready(function () {
			$("#ventana_america").click(function () { 
				  $("#ventana_info_america").css("display","block");
				   
			});
				$("#close_america").click(function () { 
				  $("#ventana_info_america").css("display","none");
				  
			});
			});
			
			$(document).ready(function () {
			$("#registro_boton").click(function () { 
				  $("#registro_all").css("display","block");
				    $("#marcas_bg").css("display","none");
			});
				$("#close").click(function () { 
				  $("#registro_all").css("display","none");
				   $("#marcas_bg").css("display","block");
			});
			});
			
			$(document).ready(function () {
			$("#login_boton").click(function () { 
				  $("#registro_login").css("display","block");
				  $("#marcas_bg").css("display","none");
			});
				$("#close_login").click(function () { 
				  $("#registro_login").css("display","none");
				    $("#marcas_bg").css("display","block");
			});
			});
			$(document).ready(function () {
			$("#a_boton").click(function () { 
				  $("#legal").css("display","block");
			});
				$("#close_legal").click(function () { 
				  $("#legal").css("display","none");
			});
			});
				
		    $(document).ready(function () {
			$("#boton_contacto").click(function () { 
				  $("#contacto_ventana").css("display","block");
			});
				 $("#close_contacto").click(function () { 
				  $("#contacto_ventana").css("display","none");
			});
			});
			
			  $(document).ready(function () {
			$("#boton_empleo").click(function () { 
				  $("#empleo_ventana").css("display","block");
			});
				 $("#close_empleo").click(function () { 
				  $("#empleo_ventana").css("display","none");
			});
			});
			 
			 $(document).ready(function () {
			$("#close_registrofinal").click(function () { 
				  $("#registro_final").css("display","none");
			});
			
			});	
		    
			$(document).ready(function () {
			$("#close_login_datos").click(function () { 
				  $("#datos_login").css("display","none");
			});
			
			});	
						
			$(document).ready(function () {
				$("body").keyup(function (key) {
				
				if(key.which == 37){alert(key.which);}
				if(key.which == 38){alert(key.which);}
				if(key.which == 39){alert(key.which);}
				
					
					
				});
			});
			
			
		
       