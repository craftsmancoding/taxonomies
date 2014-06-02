INIT = {

/*
	update_review: function(){
		$('select#state').on('change',function(e){
			var review_id = $(this).data('review_id'),
				state	= $(this).val(),
            	values = { id: review_id, state: state };
	    	var url = connector_url + 'review_save';
		    $.post( url, values, function(data){
		    	console.log(data)
		    	$('.moxy-msg').show();
		    	data = $.parseJSON(data);
		    	if(data.success == true) {
		    		$('#moxy-result').html('Success');
		    	} else{
		    		$('#moxy-result').html('Failed');
		    	}
		    	$('#moxy-result-msg').html(data.msg);
		    	$(".moxy-msg").delay(3200).fadeOut(300);
		    } );
		    e.preventDefault();
	    })
	},
*/

	fill_form_fields : function() {
        // var product is def'd in the productcontainer.js
		$.each(product, function(name, val){
	        var $el = $('#'+name),
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
	},


	edit_image_modal: function() {

		$( document ).on( "click", "a.edit-img", function() {
		  console.debug('edit_image_modal...');
			var url_img_update = $(this).attr('href');
			 $.ajax({
                    type: "GET",
                    url: url_img_update,
                    success: function(data)
                    {	

                       var form = $(data).find('#modal-container').html();
                       $(".update-container").html(form);

                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        $("#update-image").html(errorThrown + ": " + this.url_img_update);
                    },
                    dataType: "html"
                });
		});
	},

	multi_select_drag: function() {
		console.debug('multi_select_drag...');
		var selectedClass = 'selected',
        	clickDelay = 600,
	        // click time (milliseconds)
	        lastClick, diffClick; // timestamps


		$("#product_images li").on('click',function(e) {
	    	if (e.ctrlKey) {
	    		$(this).toggleClass('selected');
	    		if($(this).hasClass('ui-draggable')) {
	    			$(this).removeClass('ui-draggable');
	    		}
	    		$(this).draggable({
			        revertDuration: 10,
			        // grouped items animate separately, so leave this number low
			        start: function(e, ui) {
			            ui.helper.addClass(selectedClass);
			        },
			        stop: function(e, ui) {
			            // reset group positions
			            $('.' + selectedClass).css({
			                top: 0,
			                left: 0
			            });
			        },
			        drag: function(e, ui) {
			            // set selected group position to main dragged object
			            // this works because the position is relative to the starting position
			            $('.' + selectedClass).css({
			                top: ui.position.top,
			                left: ui.position.left
			            });
			        }
			    });
		        return false;
		    } 
		})
	},

//	drag_drop_delete: function() {

	/*	$( "#product_images" ).sortable({
		  activate: function( event, ui ) {
		  	$('#trash-can').show().slideDown();
		  },
		  deactivate: function( event, ui ) {
		  	$('#trash-can').hide();
		  }
		});*/
/*
		$( "#trash-can" ).droppable({
			
			over: function( event, ui ) {
				$(this).addClass('over-trash');
			},
			out: function(event, ui) {
				var id = $(ui.draggable).attr('id');
				$(this).removeClass('over-trash');
			},
		    drop: function( event, ui ) {
		      	var id = $(ui.draggable).attr('id');
		      	var url = connector_url + 'image_save';
		      	var img_id = $(ui.draggable).find('a').data('image_id');
		      	
		      	if (confirm("Are you Sure you want to Delete this Image?")) {
		      		$(this).removeClass('over-trash');
		            $.post( url+"&action=delete", { image_id: img_id }, function( data ){
				    	data = $.parseJSON(data);
				    	if(data.success == true) {
				    		$('#'+id).hide();
				    	} else{
				    		$('#moxy-result').html('Failed');
				    		$('#moxy-result-msg').html(data.msg);
				    		$(".moxy-msg").delay(3200).fadeOut(300);
				    	}
				    } );
			    }
			    $(this).removeClass('over-trash');
			    return false;
		    }

	    });
*/
//	}

    
}

jQuery(function() {
	// INIT.update_review();
	//INIT.fill_form_fields();
	INIT.edit_image_modal();
	//INIT.drag_drop_delete();
/*
	jQuery('#moxytab').tabify();
	jQuery('.datepicker').datepicker();
	jQuery("#product_images").sortable();
    jQuery("#product_images").disableSelection();
	
    jQuery(function() {
        jQuery(".sortable").sortable({
            connectWith: ".connectedSortable",
        }).disableSelection();
    });  
*/

/*
	jQuery(document).on('submit','#product_update',function(){     
	        console.log('product_update');

	        var values = $(this).serialize();
	        var rand = Math.random()*10000000000000000;
		    $.post( controller_url + '&class=product&method=edit&rand='+rand, values, function(data){
		    	$('.moxy-msg').show();
		    	data = $.parseJSON(data);
		    	if(data.success == true) {
		    		$('#moxy-result').html('Success');
		    	} else{
		    		$('#moxy-result').html('Failed');
		    	}
		    	$('#moxy-result-msg').html(data.msg);
		    	$(".moxy-msg").delay(3200).fadeOut(300);
		    	$('#product_name').text( $('#name').val() );
		    } );
		    return false;

	});
*/

	$(document).on('submit','#product_create', function(){
        console.log('product_create');
		var values = $(this).serialize();
        var rand = Math.random()*10000000000000000;

	    $.post( controller_url + '&class=product&method=create&rand='+rand, values, function(data){
	    	data = $.parseJSON(data);
	    	if(data.success == true) {
	    		window.location.href = controller_url + '&class=product&method=edit&product_id=' + data.product_id;
	    	} else{
	    		$('#moxy-result').html('Failed');
	    		$('#moxy-result-msg').html(data.msg);
	    		$(".moxy-msg").delay(3200).fadeOut(300);
	    	}
	    } );
	    return false;
	});
});