<?php if(!empty($data['links'])) : ?>
    <?php foreach ($data['links'] as $i) : ?>
        <a onclick="javascript:get_terms(this,event);" data-id="<?php  print $i['id']; ?>" href="#" class="tax_breadcrumb"><?php  print $i['pagetitle']; ?></a> &raquo;
    <?php endforeach; ?>
<?php endif; ?>
<span><?php print $data['last']; ?></span>
