/**
 * Removes an element from its location. We use "event" here so we can determine
 * where exactly the thing to be removed is located.
 *
 * E.g. place it in a <td> to remove the containing row:
 * onclick="javascript:remove_me.call(this,event,'tr');"
 */

function remove_me(event,parent) {
    console.debug('[remove_me] parent: '+parent);
    jQuery(this).closest(parent).remove();
}

/**
 * add_term
 */
function add_term(obj,event) {
    var term_name = jQuery(obj).prev().val() != undefined ? jQuery('#term-entry').val() : jQuery(obj).data('term_name');
    console.log(term_name);
    if( term_name !== '' )
    {
        var term_tpl = Handlebars.compile(jQuery('#term_tpl').html());
        jQuery('.terms-wrap').append(term_tpl({"name":term_name}));
    }
    event.preventDefault()
}

/**
 * add all terms
 * get data row_term_name attr of table tbody#terms-container
 * @param event
 */
function add_all_terms(event) {
    console.log('[Adding all terms]');
    var term_tpl = Handlebars.compile(jQuery('#term_tpl').html());
    var terms = jQuery("#terms-container tr");
    if(terms.length !== 0)
    {
        terms.each(function() {
            jQuery('.terms-wrap').append(term_tpl({"name":jQuery(this).data('row_term_name')}));
        });
    }

    event.preventDefault();
}

/**
 * get_terms
 * get all child pages of the give page_id
 */
function get_terms(obj,event)
{
    jQuery('#ajax-loader').show();
    var page_id = jQuery(obj).val();
    $.ajax({
        type: "GET",
        url: taxonomies.connector_url+'&method=terms&page_id='+page_id,
        success: function(response) {
            response = jQuery.parseJSON(response);
            jQuery('#terms-container').empty();
            jQuery.each( response, function( key, value ) {
                var row_term_tpl = Handlebars.compile(jQuery('#row_term_tpl').html());
                jQuery('#terms-container').append(row_term_tpl(value));
            });
            jQuery('#ajax-loader').hide();
        }
    });
    event.preventDefault();
}