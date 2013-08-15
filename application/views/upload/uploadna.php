
<h3>There was a problem with the upload</h3>

<ul>
<?php foreach ($error as $item => $value):?>
<p><?php echo $item;?>: <?php echo $value;?></p>
<?php endforeach; ?>
</ul>

<p><?php echo anchor('index.php?upload', 'Upload More Files'); ?></p>

