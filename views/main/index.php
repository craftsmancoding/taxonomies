<?php include dirname(dirname(__FILE__)).'/includes/header.php';  ?>

<script id="term_tpl" type="text/x-handlebars-template">
	<div class="term-item">
		<a href="#">{{name}}</a><a class="remove" onclick="javascript:remove_me.call(this,event,'div');" href="#"><span class="fa fa-remove"></span></a>
	</div>
</script>

<div class="taxonomies_canvas_inner">
	<h2 class="taxonomies_cmp_heading" id="taxonomies_pagetitle">Welcome to Taxonomies Dashboard</h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder">
	<p>More features coming soon...</p>
</div>
<br>
<a href="#" class="taxonomies-btn taxonomies-btn-primary" data-toggle="modal" data-target="#quick-add-terms" >Quick Add Terms</a>


<!-- Modal -->
<div class="modal fade" id="quick-add-terms" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Quick Add Terms</h4>
      </div>
      <div class="modal-body">
        <div class="taxonomies-select">
        	<p class="tax-description">Select taxonomy to add its Direct Child Terms...</p>
        	<select name="taxonomies-select" id="taxonomies-select-input">
        		<option value="1">Taxonomy 1</option>
        		<option value="1">Taxonomy 2</option>
        		<option value="1">Taxonomy 3</option>
        		<option value="1">Taxonomy 4</option>
        		<option value="1">Taxonomy 5</option>
        	</select>
        </div>
        <div class="clear">&nbsp;</div>
        <div class="terms-wrap">
            <p class="tax-description">Press "ENTER" after typing the term...</p>
        	<input type="text" onkeyup="javascript:type_term();" name="term-entry" id="term-entry" placeholder="Type Term to be added..." >
        	<div class="term-item"><a href="#">Term 1</a><a class="remove" onclick="javascript:remove_me.call(this,event,'div');" href="#"><span class="fa fa-remove"></span></a></div>
        	<div class="term-item"><a href="#">Term 2</a><a class="remove" onclick="javascript:remove_me.call(this,event,'div');" href="#"><span class="fa fa-remove"></span></a></div>
        	<div class="term-item"><a href="#">Term 3</a><a class="remove" onclick="javascript:remove_me.call(this,event,'div');" href="#"><span class="fa fa-remove"></span></a></div>
        	<div class="term-item"><a href="#">Term 4</a><a class="remove" onclick="javascript:remove_me.call(this,event,'div');" href="#"><span class="fa fa-remove"></span></a></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>


<?php include dirname(dirname(__FILE__)).'/includes/footer.php';  ?>


