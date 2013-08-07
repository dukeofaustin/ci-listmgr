<!DOCTYPE html>

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
	<title>Design Patterns in C#</title>
	<style type="text/css" media="screen">
	#container {
	 width: 600px;
	 margin: auto;
	 font-family: calibri, arial;
	}

	table {
	 width: 600px;
	 margin-bottom: 10px;
	}

	td {
	 border-right: 1px solid #aaaaaa;
	 padding: 1em;
	}

	td:last-child {
	 border-right: none;
	}

	th {
	  text-align: left;
	  padding-left: 1em;
	  background: #cac9c9;
	  border-bottom: 1px solid white;
	  border-right: 1px solid #aaaaaa;
	}

	#pagination a, #pagination strong {
	  background: #32CDCD;
	  padding: 4px 7px;
	  text-decoration: none;
	  border: 1px solid #cac9c9;
	  color: #292929;
	  font-size: 13px;
	}

	#pagination strong, #pagination a:hover {
	 font-weight: normal;
	 background: #cac9c9;
	}		
	</style>
</head>
<body>
     <div id="container">
		<h1>Grocery Items</h1>
		<?php echo '<h2>'.$total.'</h2>'; ?>
		<?php
		foreach($results as $data) {
		    echo $data->Type . " - " . $data->Item . "<br>";
		}
                ?>
         <p><?php echo $links; ?></p>
    </div>
     
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript" charset="utf-8"></script>	

<script type="text/javascript" charset="utf-8">
	$('tr:odd').css('background', '#e3e3e3');
</script>
</body>
</html>	