/**
 * @file
 * Placeholder file for custom sub-theme behaviors.
 *
 */
(function ($, Drupal) {

  /**
   * Use this behavior as a template for custom Javascript.
   */
  Drupal.behaviors.exampleBehavior = {
    attach: function (context, settings) {

		jQuery(window).scroll(function() {    

			//console.log(jQuery(window).scrollTop());
		    if (jQuery(window).scrollTop() > 95) {
					jQuery(".off-canvas-content").addClass("header-fix");
		    } else {
					jQuery(".off-canvas-content").removeClass("header-fix");
		    }
		});



		// jQuery('#edit-field-tipos-de-turismo-target-id ul > li > input:checkbox').change(
		jQuery('.formulario-expuesto-que-hacer input:checkbox').change(
		    function(){
				console.log("Test");
		        posicion = jQuery( this ).parent().parent().index()+1;
		        padre = jQuery( this ).parent().parent().parent().parent().get( 0 ).tagName;

		        console.log(posicion);
		        console.log(padre);

		        if( padre != "LI" ) {
			       // if (jQuery(this).is(':checked')) {
			        	// console.log("#edit-field-tipos-de-turismo-target-id .form-checkboxes > ul > li:nth-child(" + posicion + ") > ul");
			        	// console.log("Padre: " + padre);
			        	jQuery( ".formulario-expuesto-que-hacer .form-checkboxes > ul > li:nth-child(" + posicion + ") > ul" ).toggle();
			       	// } else {
			        	// jQuery( ".seccion-que-hacer .views-exposed-form .form-checkboxes > ul > li:nth-child(" + posicion + ") > ul" ).hide();
			        // }

		        }
		    }
		);



		jQuery( ".seccion-que-hacer .views-exposed-form" ).addClass( "columns medium-3" );

		// if($('#block-menusecundario h2').length != 0) {
		// 	$('#block-menusecundario h2').remove();
		// }
		

		
			
    }
  };





	/**
   * Comportamiento para mostrar y ocultar el bloque con el formulario de búsqueda
   */
	jQuery('.search-button').click(function(e) {
	    e.preventDefault();
	  	jQuery('.searchBox').toggleClass('invisible');
	});
	



})(jQuery, Drupal);


