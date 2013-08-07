<html>
<head>
<title>Smileys</title>
    <?php echo smiley_js('comments');?>  
</head>
<body>
<form name="blog">
    <textarea name="comments" id="comments" cols="40" rows="4"></textarea>
</form>
<p>Click to insert a smiley!</p>
<?php echo $data; ?>
</body>
</html>
