<script id="term_tpl" type="text/x-handlebars-template">
    <div class="term-item">
        <a onclick="javascript:get_terms(this,event);" data-id="{{id}}" href="#">{{name}}</a><a class="remove" onclick="javascript:remove_me.call(this,event,'div');" href="#"><span class="fa fa-remove"></span></a>
    </div>
</script>

<script id="new_term_tpl" type="text/x-handlebars-template">
    <div class="term-item">
        <a onclick="return false;" href="#">{{name}}</a><a class="remove" onclick="javascript:remove_me.call(this,event,'div');" href="#"><span class="fa fa-remove"></span></a>
    </div>
</script>

<a class="taxonomies-btn taxonomies-btn-primary taxonomies-btn-mini " data-toggle="modal" data-id="<?php print $data['page_id']; ?>" data-modal="quick-add-terms" href="<?php print $data['connector_url']; ?>&class=ajax&method=termsmodal&page_id=<?php print $data['page_id']; ?>" onclick="javascript:launch_modal(this,event);">Quick Add Terms</a>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder">
    <p>Pages which use this Term.</p>
</div>
<br>
<?php if($data['terms']) : ?>
    <table class="tax-classy tax-classy-alt">
        <thead>
            <tr><th>Pagetitle</th><th>Alias</th></tr>
        </thead>
        <tbody>
            <?php foreach ($data['terms'] as $t): ?>

                <tr>
                    <td><a href="<?php print MODX_MANAGER_URL. "?a=resource/update&id=".$t->get('page_id'); ?>"><?php print $t->Page->get('pagetitle'); ?> (<?php print $t->Page->get('id'); ?>)</a></td>
                    <td><?php print $t->Page->get('alias'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="taxonomies-danger">No pages have been assigned to this Term.</div>
<?php endif; ?>

<div class="modal fade" id="quick-add-terms"></div>

<?php include dirname(dirname(__FILE__)).'/includes/footer.php';  ?>