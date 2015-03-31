<?php include dirname(dirname(__FILE__)).'/includes/header.php';  ?>

<script id="term_tpl" type="text/x-handlebars-template">
	<div class="term-item">
		<a href="#">{{name}}</a><a class="remove" onclick="javascript:remove_me.call(this,event,'div');" href="#"><span class="fa fa-remove"></span></a>
	</div>
</script>

<script id="row_term_tpl" type="text/x-handlebars-template">
    <tr data-row_term_name="{{pagetitle}}">
        <td><a onclick="javascript:get_terms(this,event);" data-id="{{id}}" href="#">{{pagetitle}}</a></td>
        <td><a onclick="javascript:add_term(this,event);" data-term_name="{{pagetitle}}" href="#" class="taxonomies-btn taxonomies-btn-info taxonomies-btn-mini pull-right">Add</a></td>
    </tr>
</script>

<div class="taxonomies_canvas_inner">
	<h2 class="taxonomies_cmp_heading" id="taxonomies_pagetitle">Welcome to Taxonomies Dashboard</h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder">
	<p>More features coming soon...</p>
</div>
<br>

<table class="tax-classy tax-classy-alt">
    <thead>
    <tr>
        <th>Id</th>
        <th>Taxonomy</th>
        <th>Alias</th>
        <th>&nbsp</th>
    </tr>
    </thead>
    <tbody>
        <?php if($data['taxonomies']) : ?>
            <?php foreach($data['taxonomies'] as $t) : ?>
            <tr>
                <td><?php print $t->get('id'); ?></td>
                <td><?php print $t->get('pagetitle'); ?></td>
                <td><?php print $t->get('alias'); ?></td>
                <td><a href="#" class="taxonomies-btn taxonomies-btn-primary taxonomies-btn-mini " data-toggle="modal" data-target="#quick-add-terms" >Quick Add Terms</a></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="quick-add-terms" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Quick Add Terms</h4>
      </div>
      <div class="modal-body clearfix">
          <div class="tax-col4">
              <div class="taxonomies-select">
                  <p class="tax-description">Select taxonomy to add its Direct Child Terms...</p>
                  <select onchange="javascript:get_terms(this,event);" name="taxonomy_id" id="taxonomies-select-input">
                      <option value="">Please Select...</option>
                      <?php if($data['taxonomies']) : ?>
                          <?php foreach($data['taxonomies'] as $t) : ?>
                              <tr>
                                  <option value="<?php print $t->get('id'); ?>"><?php print $t->get('pagetitle'); ?></option>
                              </tr>
                          <?php endforeach; ?>
                      <?php endif; ?>
                  </select>
                    <div class="clear">&nbsp;</div>
                  <table class="tax-classy">
                      <thead>
                      <tr>
                          <th>Child Terms</th>
                          <th><a href="#" class="taxonomies-btn taxonomies-btn-mini taxonomies-btn-default" onclick="javascript:add_all_terms(event);">Add All </a></th>
                      </tr>
                      </thead>
                      <tbody id="terms-container">

                      </tbody>
                  </table>

              </div>
          </div><!--e.tax-col4-->
        <div class="tax-col6">
            <div class="terms-wrap">
                <p class="tax-description">Press "ENTER" after typing the term...</p>
                <input type="text" name="term-entry" id="term-entry" placeholder="Type Term to be added..." >
                <button class="taxonomies-btn taxonomies-btn-info taxonomies-btn-mini" onclick="javascript:add_term(this,event);" >Add</button>

            </div>
        </div><!--e.tax-col6-->
          <div class="clear">&nbsp;</div>
          <div id="taxonomies-bc"></div>

      </div>
      <div class="modal-footer">
          <img id="ajax-loader" src="<?php print $data['loader'];  ?>" alt=""/>&nbsp;
        <button type="button" class="taxonomies-btn taxonomies-btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="taxonomies-btn taxonomies-btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>


<?php include dirname(dirname(__FILE__)).'/includes/footer.php';  ?>


