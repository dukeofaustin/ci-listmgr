
<h3>There was a problem with the upload</h3>

<ul>
<?php foreach ($error as $item => $value):?>
<li><?php echo $item;?>: <?php echo $value;?></li>
<?php endforeach; ?>
</ul>

<p><?php echo anchor('index.php?uploadfrm', 'Upload More Files'); ?></p>

