/* author: felice ostuni
  tool to replace fieldset with jquery-ui tabs (require jquery 1.3.x and jquery-ui 1.7.x)
 
  usage:  append this before e dataedit  build()

    html_helper::js('jquery/jquery.formtabs.js');
    html_helper::head_script('
        $(document).ready(function() { $('.dataform').formTabs({ propery: 'value'}); });
    ');
  
 */


(function($) {

    $.fn.formTabs = function(settings) {

	settings = jQuery.extend({
			//property: 'value',
		}, settings);

        this.find('.df_body:first').prepend('<ul></ul>');
        var ul = this.find("ul");

        //console.log('custom property: ', settings.propery);
        this.find('fieldset').each(function() {
            $(this).css('border','none');
            $(this).find('legend').css('display','none');
            $(this).wrap('<div id="tab_'+$(this).attr('id')+'" />');
            $(ul).append('<li><a href="#tab_'+$(this).attr('id')+'"><span>'+$(this).find('legend').text()+'</span></a></li>');

        });
        this.find('.df_body:first').tabs({ cookie: { expires: 30, name: 'tabss'} });
        $(".ui-tabs-panel").css('padding-top',0);
    };


})(jQuery);