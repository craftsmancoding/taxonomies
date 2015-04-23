<?php include dirname(dirname(__FILE__)).'/includes/header.php';  ?>

<script id="term_tpl" type="text/x-handlebars-template">
	<div class="term-item">
		<a onclick="javascript:get_terms(this,event);" data-id="{{id}}" href="#">{{name}}</a><a class="remove" onclick="javascript:remove_me.call(this,event,'div');" href="#"><span class="fa fa-remove"></span></a>
	</div>
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
                <td><a class="taxonomies-btn taxonomies-btn-primary taxonomies-btn-mini " data-toggle="modal" data-id="<?php print $t->get('id'); ?>" data-modal="quick-add-terms" href="<?php print $data['connector_url'].'&class=ajax&method=termsmodal&page_id='.$t->get('id') ?>" onclick="javascript:launch_modal(this,event);">Quick Add Terms</a></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<div class="modal fade" id="quick-add-terms"></div>


<?php include dirname(dirname(__FILE__)).'/includes/footer.php';  ?>


