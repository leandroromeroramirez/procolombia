(function ($, Drupal, drupalSettings) {

  'use strict';

  function replaceCoordinates(file, coordinates_origin, coordinates_destination){
    var loc = file.replace(/COORX/gi, coordinates_origin.x).replace(/COORY/gi, coordinates_origin.y).replace(/CODEX/gi, coordinates_destination.x).replace(/CODEY/gi, coordinates_destination.y).replace(/TRACOX/gi, coordinates_origin.tx).replace(/TRACOY/gi, coordinates_origin.ty).replace(/GRT/gi, coordinates_origin.r);
    return loc;
  }

  function getCoordinates(){
    var loc = {NA:{continent:{name:'Norteamérica',coordinates:{x:110,y:70,tx:30,ty:-35,r:105}},country:{CA:{name:'Canadá',coordinates:{x:110,y:70,tx:30,ty:-35,r:105}},US:{name:'Estados Unidos',coordinates:{x:120,y:90,tx:30,ty:-35,r:105}},MX:{name:'México',coordinates:{x:115,y:120,tx:20,ty:-40,r:90}},CW:{name:'Curazao',coordinates:{x:175,y:145,tx:40,ty:20,r:175}}}},CA:{continent:{name:'Caribe y Centroamérica',coordinates:{x:140,y:145,tx:20,ty:-40,r:90}},country:{}},SA:{continent:{name:'Suramérica',coordinates:{x:200,y:190,tx:-23,ty:40,r:268}},country:{CO:{name:'Colombia',coordinates:{x:163,y:160,tx:0,ty:0,r:0}},AR:{name:'Argentina',coordinates:{x:175,y:245,tx:-42,ty:12,r:-52}},BO:{name:'Bolivia',coordinates:{x:180,y:210,tx:-38,ty:23,r:295}},BR:{name:'Brasil',coordinates:{x:200,y:190,tx:-23,ty:40,r:268}},CL:{name:'Chile',coordinates:{x:170,y:240,tx:-42,ty:12,r:-45}},EC:{name:'Ecuador',coordinates:{x:160,y:185,tx:-46,ty:3,r:-38}},PE:{name:'Perú',coordinates:{x:163,y:190,tx:-43,ty:9,r:-44}},VE:{name:'Venezuela',coordinates:{x:175,y:160,tx:10,ty:40,r:225}}}},EU:{continent:{name:'Europa',coordinates:{x:300,y:75,tx:35,ty:30,r:190}},country:{DE:{name:'Alemania',coordinates:{x:300,y:75,tx:35,ty:30,r:190}},ES:{name:'España',coordinates:{x:277,y:93,tx:35,ty:30,r:190}},FR:{name:'Francia',coordinates:{x:290,y:80,tx:35,ty:30,r:190}},NL:{name:'Holanda',coordinates:{x:290,y:70,tx:35,ty:30,r:190}},GB:{name:'Reino Unido',coordinates:{x:280,y:65,tx:35,ty:30,r:190}}}},AS:{continent:{name:'Asia',coordinates:{x:140,y:145,tx:20,ty:-40,r:90}},country:{TR:{name:'Turquia',coordinates:{x:340,y:95,tx:30,ty:35,r:203}}}}};
    return loc;
  }

  Drupal.behaviors.currency_converter = {
    attach: function(context, settings) {}
  };

  $(document).ready(function() {
    if(drupalSettings){
      var flights_config = drupalSettings.settings.flights_config, a = getCoordinates(), coordinates_origin, coordinates_destination = a['SA']['country']['CO'].coordinates;

      if(flights_config.header.origin.continent_code !== null && flights_config.header.origin.country_code !== null){
          coordinates_origin = a[flights_config.header.origin.continent_code]['country'][flights_config.header.origin.country_code].coordinates;
      }else if (flights_config.header.origin.continent_code !== null && flights_config.header.origin.country_code == null){
        coordinates_origin = a[flights_config.header.origin.continent_code].continent.coordinates;
      }

      var a = replaceCoordinates(flights_config.svg, coordinates_origin, coordinates_destination);
      $("#edit-svg").append(a);

      $("#owl-flights").owlCarousel({
        autoPlay: 10000,//seconds
        items : 1,
        itemsDesktop : [1199,1],
        itemsDesktopSmall : [979,1]
      });
    }
});

})(jQuery, Drupal, drupalSettings);
