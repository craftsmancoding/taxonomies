/**
 * This is the javascript that supports our thin HTML "client" to help it 
 * interface with our REST API.
 *
 *
 * @package moxycart
 */
 
if (typeof jQuery == 'undefined') {
    alert('jQuery is not loaded. Moxycart HTML client cannot load.');
}
else {
    console.debug('[moxycart html client]: jQuery loaded.');
}

/**
 * In its own function in case anything changes with
 * routing.
 * @param c string classname
 * @param m string methodname
 */
function controller_url(c,m) {
    return moxycart.controller_url+'&class='+c+'&method='+m;
}

/**
 * Update HTML on the page
 */
function replace_me(target,data) {
    console.debug('[replace_me] target: ',target);
    if(jQuery('#'+target).length ==0) {
        console.error('[replace_me] Invalid target id %s. Selector failed', target);
    }
    else {
        jQuery('#'+target).html(data);
    }
}

/**
 * Grabs a value from src id and appends it to dst id
 * @param string src DOM ID
 * @param string dst DOM ID
 */ 
function append_me(src,dst) {
    console.debug('[append_me] src: '+src+' dst: '.dst);
    var data = jQuery('#'+src).val();
    jQuery('#'+dst).append(data);
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
 * Paint the canvas with the data received from the specified page 
 * @param string page name of function in the pageConteroller
 * @param object data (optional) default {}
 * @param string target (optional) DOM ID where to write result
 */
function paint(page,data,target) {
    target = typeof target !== 'undefined' ? target : 'moxycart_canvas'; // default
    data = typeof data !== 'undefined' ? data : {}; // default
    data._nolayout = 1; // omits header/footer wrapping
    console.debug('[paint]',page,data,target);
    var url = controller_url('page',page);
    jQuery.get(url, data, function( response ) {    
        replace_me(target,response);
        jQuery('#moxycart_msg').html('');
        jQuery('#moxycart_msg').show();
    })
    .fail(function() {
        console.error('[paint] get request to %s failed', url);
        return show_error('Get request failed: '+url);
    });
}

/**
 * Given JSON, populate a form with it.  IDs of form fields should correspond
 * to the keys of the JSON data.
 *
 * @param object data
 */
function populate_form(data) {
	jQuery.each(data, function(name, val){
        var $el = jQuery('#'+name),
            type = $el.attr('type');	    
        switch(type){
            case "checkbox":
                $el.attr("checked", "checked");
                break;
            case "radio":
                $el.filter('[value="'+val+'"]').attr("checked", "checked");
                break;
            default:
                $el.val(val);
        }
    });
}

/**
 * mapi : Moxycart API
 *
 * This is the primary function that drives our simple HTML client. This function
 * can dynamically load/replace parts of a page (sorta a "javascript include"), 
 * and it can approximate the effect of clicking on a standard <a> link, but it's 
 * all Ajax-REST based.
 *
 * @param string classname controller class to be requested for a JSON response
 * @param string methodname 
 * @param hash data any additional data to be included in the request to the controller 
 */
function mapi(classname,methodname,data,redirect) {
    data = typeof data !== 'undefined' ? data : {}; // default
    
    console.debug('[mapi]',classname,methodname,data);
    
    // We need to set some POST data, otherwise routing will fail.
    data._moxycart = Math.random()*10000000000000000;
    // Ajax post
    var url = controller_url(classname,methodname);    
    jQuery.post(url, data, function( response ) {
        console.debug(response);
        if(response.status == 'fail') {
            console.log(response.data.errors);
            var msg = 'Error:<br/>';
            for(var fieldname in response.data.errors) {
                msg = msg + response.data.errors[fieldname] + '<br/>';
            }
            return show_error(msg); 
        }
        else if (response.status == 'success') {
            show_success(response.data.msg);
            if (redirect) {
                paint(redirect);
            }
        }
    },'json')
    .fail(function() {
        console.error('[mapi] post to %s failed', url);
        return show_error('Request failed.');
    });
}

/**
 *
 *
 */
function submit_form(formid,url,redirect) {
    console.debug('[submit_form]',formid,url);
    jQuery.post(url, jQuery('#'+formid).serialize(),function( response ) {
        console.debug(response);
        if(response.status == 'fail') {
            console.log(response.data.errors);
            var msg = 'Error:<br/>';
            for(var fieldname in response.data.errors) {
                msg = msg + response.data.errors[fieldname] + '<br/>';
            }
            return show_error(msg); 
        }
        else if (response.status == 'success') {
            show_success(response.data.msg);
            paint(redirect);
        }
    },'json');
}

function handle_response(response){
    if(response.status == 'fail') {
        console.log(response.data.errors);
        var msg = 'Error:<br/>';
        for(var fieldname in response.data.errors) {
            msg = msg + response.data.errors[fieldname] + '<br/>';
        }
        return show_error(msg); 
    }
    else if (response.status == 'success') {
        show_success(response.data.msg);
        paint(redirect);
    }
}

/**
 * Submitting a search form and repainting the page
 *
 */
function searchform(formid,page) {
    var data = jQuery('#'+formid).serialize();
    console.debug('[searchform] refresh page: '+page,data);
    return paint(page,data);
}

/**
 * Show a simple error message, then fade it out and clear it so we can reuse the div.
 */
function show_error(msg) {
    jQuery('#moxycart_msg').html('<div class="danger">'+msg+'</div>');
}


/**
 * Show a success message, then fade it out and clear it so we can reuse the div.
 */
function show_success(msg) {
    jQuery('#moxycart_msg').html('<div class="success">'+msg+'</div>')
    .delay(3000).fadeOut(function() {
        jQuery(this).html('');
        jQuery(this).show(); 
    });
}