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
function add_term(event) {
    console.log('test');
    var term_name = jQuery('#term-entry').val();
    if( term_name !== '' )
    {
        var term_tpl = Handlebars.compile(jQuery('#term_tpl').html());
        jQuery('.terms-wrap').append(term_tpl({"name":term_name}));
    }
    event.preventDefault()
}

/**
 * get_terms
 *
 */
function get_terms(obj,event)
{
    var page_id = jQuery(obj).val();
    $.ajax({
        type: "GET",
        url: taxonomies.connector_url+'&method=terms&page_id='+page_id,
        success: function(response) {
            console.log(response);
        }
    });
    event.preventDefault();
}