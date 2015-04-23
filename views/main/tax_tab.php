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


<?php if($data['taxonomies']) : ?>
    <?php
            $T = new \Taxonomies\Base($modx);
            foreach ($data['taxonomies'] as $t) :
        ?>
            <fieldset class="tax-fs">
                <legend style="border: 1px solid #ddd !important;">
                    <?php  print $t->get('pagetitle'); ?>
                </legend>
                <?php
                    $properties = $t->get('properties');
                    if ($children = $this->modx->getOption('children', $properties, array()))
                    {
                       print  $T->getFieldItems($data['current_values'], $children); // 1st time

                    }
                ?>
                <a class="pull-right taxonomies-btn taxonomies-btn-primary taxonomies-btn-mini " data-toggle="modal" data-id="<?php print $t->get('id') ?>" data-modal="quick-add-terms" href="<?php print $data['connector_url']; ?>&class=ajax&method=termsmodal&page_id=<?php print $t->get('id') ?>" onclick="javascript:launch_modal(this,event);"><i class="fa fa-edit"></i>&nbsp;Taxonomy Quick Edit</a>
                <div class="clear">&nbsp;</div>
            </fieldset>
        <?php endforeach; ?>

<?php else: ?>
    <div class="taxonomies-danger">No Taxonomy Found.</div>
<?php endif; ?>

<div class="modal fade" id="quick-add-terms"></div>

<?php include dirname(dirname(__FILE__)).'/includes/footer.php';  ?>