
/**
 * See http://www.sencha.com/forum/showthread.php?21756-How-do-I-add-plain-text-to-a-Panel
 * And http://www.sencha.com/forum/showthread.php?38841-Using-Extjs-to-change-div-content
 */
function setBreadcrumbs(page_id) {
    console.log('[generate breadcrumbs]');
    jQuery.ajax({
        type: "GET",
        url: taxonomies.connector_url+'&method=breadcrumbs&page_id='+page_id,
        success: function(response) {
            console.log(response);
/*
            if($('#taxonomies-bc').length == 0) {
                $('#child_pages').after('<div id="lunchbox_breadcrumbs"></div>');
            }
*/

            $('#taxonomies-bc').html(response);
        }
    });
}

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
        jQuery('.terms-wrap-inner').append(term_tpl({"name":term_name}));
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
            jQuery('.terms-wrap-inner').append(term_tpl({"name":jQuery(this).data('row_term_name')}));
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
    var page_id = jQuery(obj).data('id');
    setBreadcrumbs(page_id);
    $.ajax({
        type: "GET",
        url: taxonomies.connector_url+'&class=ajax&method=terms&page_id='+page_id,
        success: function(response) {
            response = jQuery.parseJSON(response);
            jQuery('#terms-container').empty();
            if(response != null)
            {
                jQuery('.terms-wrap-inner').empty();
                jQuery.each( response, function( key, value ) {
                    var term_tpl = Handlebars.compile(jQuery('#term_tpl').html());
                    jQuery('.terms-wrap-inner').append(term_tpl({"name":value.pagetitle,"id":value.id}));
                });
            }

            jQuery('#ajax-loader').hide();
        }
    });
    event.preventDefault();
}

/**
 *
 * generic view modal call
 * See user.injuries.blade.php for sample usage
 * @param obj
 * @param event
 */
function launch_modal(obj,event)
{
    var modal = jQuery(obj).data('modal');
    var width = jQuery(obj).data('width') == undefined ? '60%' : jQuery(obj).data('width');
    var page_id = jQuery(obj).data('id');
    $.ajax({
        type: "GET",
        url: $(obj).attr('href'),
        success: function(response) {
            jQuery('.modal').modal('hide'); // hide any active modal
            jQuery('#'+modal).modal('show');
            jQuery('#'+modal).html(response);
            setBreadcrumbs(page_id);
        }
    });
    event.preventDefault();
}