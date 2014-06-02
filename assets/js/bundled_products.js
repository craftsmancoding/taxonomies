jQuery(document).ready(function($){

// Check that we're not editing a sub, this string needs to *exactly match* what is shown in the alert when editing a sub.
if (fc_json["messages"]["warnings"] != "You are currently modifying a subscription.") {

  // Syntax: 
  // {"parent":["parentCode1","parentCode2"], "dependent":["dependantCode1","dependantCode2"], "quantity-match":true}
  var productBundles = [
    {"parent":["28","29","32","33","34","35","36","37"], "dependent":["38"], "quantity-match":"bundle-1:order"}
  ];

  for (var c = 0; c < productBundles.length; c++) {
    var parentFound = false;
    var parentName = [];
    var parentQuantity = 0;
    for (var pc = 0; pc < productBundles[c]["parent"].length; pc++) {
      for (var p = 0; p < fc_json.products.length; p++) {
        // setup parent elements
        if (productBundles[c]["parent"][pc] == fc_json.products[p].code) {
          parentFound = true;
          parentName.push(fc_json.products[p].name);
          var parentChildren = jQuery("input[value='"+fc_json.products[p].id+"']").parent("td").next("td.fc_cart_item_quantity");
          parentQuantity += parseInt(jQuery(parentChildren).children("input").val());
          jQuery(parentChildren).children("input").addClass("parent-"+c)

          if(productBundles[c]["quantity-match"]=="bundle-1:1") {
            jQuery(parentChildren).children("input").unbind().change(function() {
              var id = jQuery(this).attr('class').match(/parent-(\d+)/)[1]
              jQuery("input.child-"+id).val(totalQuantity(".parent-"+id));
              fc_TestCheckout();
            }).keyup(function(event){
              var id = jQuery(this).attr('class').match(/parent-(\d+)/)[1]
              jQuery("input.child-"+id).val(totalQuantity(".parent-"+id));
              fc_TestCheckout(event);
            });
          }
          else if (productBundles[c]["quantity-match"]=="bundle-1:order") {
            jQuery(parentChildren).children("input").unbind().change(function() {
              var id = jQuery(this).attr('class').match(/parent-(\d+)/)[1]
              jQuery("input.child-"+id).val(1);
              fc_TestCheckout();
            }).keyup(function(event){
              var id = jQuery(this).attr('class').match(/parent-(\d+)/)[1]
              jQuery("input.child-"+id).val(1);
              fc_TestCheckout(event);
            });
          }
          jQuery(parentChildren).children("span").children("a").attr("onclick","").click(function() {
            var id = jQuery(this).parent("span").siblings("input.fc_cart_item_quantity").attr('class').match(/parent-(\d+)/)[1];
            jQuery(this).parent("span").siblings("input.fc_cart_item_quantity").val(0);
            if (jQuery("input.parent-"+id).length == 1 || totalQuantity(".parent-"+id) == 0) { // Only remove the children product if this is the only parent
              jQuery("input.child-"+id).val(0).attr("readonly", true).addClass("fc_readonly");
            } else {
              jQuery("input.child-"+id).val(totalQuantity(".parent-"+id));
            }
            fc_TestCheckout();
          });
          p = fc_json.products.length; // Finishes the loop
        }
      }
    }

    // Find dependent fields
    var dependentMissing = false;
    for (var d = 0; d < productBundles[c]["dependent"].length; d++) {
      var dependentFound = false;
      for (var p = 0; p < fc_json.products.length; p++) {
        if (productBundles[c]["dependent"][d] == fc_json.products[p].code) {
          var dependentFound = true;
          jQuery("input[value="+fc_json.products[p].id+"]").parent("td").next("td")
          var dependentChildren = jQuery("input[value="+fc_json.products[p].id+"]").parent("td").next("td.fc_cart_item_quantity");
          jQuery(dependentChildren).children("input").addClass("child-"+c);
          jQuery(dependentChildren).children("span").hide();
          jQuery(dependentChildren).parent("tr").addClass("fc_dependent");
          jQuery("input[value="+fc_json.products[p].id+"]").siblings(".fc_cart_item_options").before("<span class=\"fc_dependent_text\"> - added with '" + parentName.join("', '") + "'</span>");
          if (productBundles[c]["quantity-match"]) {
            jQuery(dependentChildren).children("input").attr("readonly", true).addClass("fc_readonly");
          }
          if (productBundles[c]["quantity-match"]=="bundle-1:1") {
            if (jQuery(dependentChildren).children("input").val() != parentQuantity) {
              jQuery(dependentChildren).children("input").val(parentQuantity);
              showError("The product '"+fc_json.products[p].name+"' must have a matching quantity to '" + parentName.join("' and '") + "'. The quantity has been updated, please update the cart to save the new quantity.");
              fc_PreventCheckout();
            }
          }
          else if (productBundles[c]["quantity-match"]=="bundle-1:order") {
            if (jQuery(dependentChildren).children("input").val() != 1) {
              jQuery(dependentChildren).children("input").val(1);
            }
          }
          p = fc_json.products.length; // Finishes the loop
        }
      }
      if (dependentFound === false) {
        dependentMissing = true;
      }
    }
    if ((parentFound === false && dependentMissing === false) || (parentFound === true && dependentMissing === true)) {
      if (parentFound === false && dependentMissing === false) {
        showError("Parent product missing. Please re-add the product with the code(s) '" + productBundles[c]["parent"].join("' or '") + "'.");
      } else if (parentFound === true && dependentMissing === true) {
        showError("A required addon product for '" + parentName.join("' and '") + "' is missing. Please re-add the product.");
      }
      jQuery("input.parent-"+c+", input.child-"+c).val(0).attr("readonly", true).addClass("fc_readonly");
      fc_PreventCheckout();
    }
  }
}
});
function showError(text) {
if ($('#fc_error_container ul li').length) {
  jQuery("#fc_error_container ul").append("<li>"+text+"</li>");
} else {
  jQuery("table#fc_cart_table").before("<div id=\"fc_message_container\"><div id=\"fc_error_container\" class=\"fc_message fc_error\"><ul><li>"+text+"</li></ul></div></div>");
}
}
function totalQuantity(ident) {
var result = 0;
jQuery(ident).each(function() {
  result += parseInt(jQuery(this).val());
});
return result;
}
