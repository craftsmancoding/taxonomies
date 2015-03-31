<?php if(!empty($data['links'])) : ?>
    <?php foreach ($data['links'] as $i) : ?>
        <span onclick="javascript:drillDown('<?php  print $i['id']; ?>');" class="tax_breadcrumb"><?php  print $i['pagetitle']; ?></span> &raquo;
    <?php endforeach; ?>
<?php endif; ?>
<span><?php print $data['last']; ?></span>
