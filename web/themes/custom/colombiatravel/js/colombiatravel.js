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
      //alert("I'm alive!");


			jQuery(window).scroll(function() {    
		    if (jQuery(window).scrollTop() > 95) {
					jQuery(".off-canvas-content").addClass("header-fix");
		    } else {
					jQuery(".off-canvas-content").removeClass("header-fix");
		    }
			});
			
    }
  };


	



})(jQuery, Drupal);


