<?php showHtmlHeader(); ?>
<?php verifyAutorization(); ?>
  
<head>
    <title>My Lists</title>
    <?php include_once('jsandcss.php'); ?>
    <script id="scriptInit" type="text/javascript">
	$(document).ready(function () {
	    $("#flyoutmenu").wijmenu();
	    $("#menu").wijmenu();
	    $("#grp-dropdown").wijdropdown();
	    $("#itm-dropdown").wijdropdown();
	    $(":input[type='radio']").wijradio();
	    $(":input[type='text']").wijtextbox();
	    $(":input[type='checkbox']").wijcheckbox();
	});
    </script>
</head> 
<body>
   <!--[if !IE]><body class="body"><![endif]-->
   <!--[if IE ]><body class="ie-body"><![endif]-->
   <!--[if IE]>
   <p>Only IE shows this paragraph.</p>
   <![endif]-->

  <div class="container">
     <div class="header">
        <!--  site logo -->
	<div id="logo">
	   <a href="index.php">   
	       <img src="<?php echo base_url();?>images/prototitle.jpg" width="350" height="75" alt="icon" />
	   </a>
	</div>
	<div id="personal">
	   <?php echo '<h1>Welcome '.getLoginName().'</h1>'; ?>
	   <a href="<?php echo base_url();?>index.php?login/editform"><?php echo 'My Settings'; ?></a>&nbsp;&nbsp;&nbsp;&nbsp
	   <a href="<?php echo base_url();?>index.php?login/signout"><?php echo 'Logout'; ?></a>
	</div>
	<? date_default_timezone_set('America/Chicago');
 	echo date("Y-m-d H:i:s",time()); ?>
    </div>
    <div id="wmmenu">
	     <?php include_once('navigate.php'); ?>
    </div> <!--wmmenu-->
  </div> <!--container-->
  <div id="contentwrapper">  
  <?php
   
   /**
    * verifyAutorization cookie
    * @param   none
    * @return  void
   */
    function verifyAutorization() {

        if (!$_COOKIE['authorized'] == "1") {
			header("Location: index.php?login");
        }
    }

   /**
    * showHtmlHeader
    * @param   none
    * @return  void
   */
    function showHtmlHeader() {
		header('Content-type: text/html; charset=utf-8'); 
        echo '<!DOCTYPE html xml:lang="en-us" lang="en-us">'; 
    }
   /**
    * getLoginName
    * @param   none
    * @return  logged in name
   */
    function getLoginName() {
 	    $rtn = '';
        $ci = get_instance();
	    $ci->load->library('session');
	    $sarr = $ci->session->all_userdata();
        if (isset($sarr['fname']) && $sarr['fname'] != "") {
          $rtn = $sarr['fname'];
	    }
	    return $rtn;
    }
    //print_r($_SERVER)    
?>