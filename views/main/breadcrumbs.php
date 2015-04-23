<?php if(!empty($data['links'])) : ?>
    <?php foreach ($data['links'] as $i) : ?>
        <a data-toggle="modal" data-id="<?php print $i['id']; ?>" data-modal="quick-add-terms" href="<?php print $data['connector_url'].'&class=ajax&method=termsmodal&page_id='.$i['id'] ?>" onclick="javascript:launch_modal(this,event);" class="tax_breadcrumb"><?php  print $i['pagetitle']; ?></a> &raquo;
    <?php endforeach; ?>
<?php endif; ?>
<span><?php print $data['last']; ?></span>
