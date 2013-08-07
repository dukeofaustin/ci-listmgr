 

      
	<title>Design Patterns in C#</title>
	<script type="text/javascript">
	      function get_record_id(record_id)
	      {
	         //alert('Id:'+record_id);
	         var p = {}; //instantiate the array
	         p['record_id'] = record_id; //assign your record_id variable to it.
	         var str = ""; // do i need this callback (function)since p6 says we don't need the callback str here
	         $('#container').load('index.php?wmtest/xjqry/'+record_id);
	      };

       </script>  
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
     <div id="container">
		<h2>Grocery Items</h2>
		<?php
		foreach($results as $data) {
		    echo $data->Type . " - " . $data->Item . "<br>";
		}
                ?>
         <p><?php echo $links; ?></p>
         <p><?php echo $testvar; ?></p>

         <p><input type="button" value="Click Me!" onclick="get_record_id(23); return false;">
</p>

    </div>
     
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript" charset="utf-8"></script>	

<script type="text/javascript" charset="utf-8">
	$('tr:odd').css('background', '#e3e3e3');
</script>
