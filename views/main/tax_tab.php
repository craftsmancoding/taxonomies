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

<?php if($data['taxonomies']) : ?>
    <?php
            $T = new \Taxonomies\Base($modx);
            foreach ($data['taxonomies'] as $t) :
        ?>
            <fieldset><legend><?php  print $t->get('pagetitle'); ?></legend>
                <?php
                    $properties = $t->get('properties');
                    if ($children = $this->modx->getOption('children', $properties, array()))
                    {
                       print  $T->getFieldItems($data['current_values'], $children); // 1st time
                    }
                ?>

            </fieldset>
        <?php endforeach; ?>

<?php else: ?>
    <div class="taxonomies-danger">No Taxonomy Found.</div>
<?php endif; ?>

<div class="modal fade" id="quick-add-terms"></div>

<?php include dirname(dirname(__FILE__)).'/includes/footer.php';  ?>