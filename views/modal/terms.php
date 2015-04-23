<script>
    var terms = <?php print json_encode($data['terms']) ;?>;
    jQuery.each( terms, function( key, value ) {
        var term_tpl = Handlebars.compile(jQuery('#term_tpl').html());
        jQuery('.terms-wrap-inner').append(term_tpl({"name":value.pagetitle,"id":value.id}));
    });
</script>
<div class="modal-backdrop fade in" style="height: 100%;"></div>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Quick Add Terms</h4>
        </div>
        <div class="modal-body clearfix tax-modal-body">
            <?php if($data['taxonomies']) : ?>
                <div class="tax-col2">
                    <ul class="tax-tabs">
                        <?php foreach($data['taxonomies'] as $t) : ?>
                            <li <?php print $data['taxonomy_id'] == $t->get('id') ? 'class="active"' : ''; ?>><a data-id="<?php print $t->get('id'); ?>"  data-toggle="modal" data-modal="quick-add-terms" href="<?php print $data['connector_url'].'&class=ajax&method=termsmodal&page_id='.$t->get('id') ?>" onclick="javascript:launch_modal(this,event);"><?php print $t->get('pagetitle'); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div><!--e.tax-col2-->
                <div class="tax-col8">
                    <div id="taxonomies-bc"></div>
                    <div class="terms-wrap">
                        <p class="tax-description">Separate Terms with a comma</p>
                        <input type="text" name="term-entry" id="term-entry" onkeyup="javascript:add_term(this,event);" placeholder="Type Term to be added..." >
                        <div class="terms-wrap-inner"></div>
                    </div>
                </div><!--e.tax-col8-->
            <?php endif; ?>
            <div class="clear">&nbsp;</div>
        </div>
        <div class="modal-footer">
            <img id="ajax-loader" src="<?php print $data['loader'];  ?>" alt=""/>&nbsp;
            <button type="button" class="taxonomies-btn taxonomies-btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="taxonomies-btn taxonomies-btn-primary">Save changes</button>
        </div>
    </div>
</div>