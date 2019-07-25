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
		    if (jQuery(window).scrollTop() > 95) {
					jQuery(".off-canvas-content").addClass("header-fix");
		    } else {
					jQuery(".off-canvas-content").removeClass("header-fix");
		    }
		});



		// $( "#other" ).click(function() {
		// });


		
			
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


