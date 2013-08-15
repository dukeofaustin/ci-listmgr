<?php   
   if(isset($_GET['logout']) && $_GET['logout'] == 1) {  
       setcookie('authorized', 0, time()-3600);  
   }   
?>  
<!DOCTYPE html>
<html>
<meta charset="UTF-8">
<title>say what?</title>
<?php include_once('application/views/templates/jsandcss.php'); ?>
<script src="<?php echo base_url();?>assets/js/useredit.js" type="text/javascript"></script>
<body>
<!--  <div class="contentwrapper"> -->
    <div id="user-area"></div>
    <div id="dlg-login" title="Login Screen">
      <form action="index.php?login/signon">
        <fieldset>
          <p class="validateTips">Please login.</p>
          <label for="uname">Username</label>
          <input type="text" name="uname" id="uname" class="text ui-widget-content ui-corner-all" />
          <br/>
          <label for="email">Email</label>
          <input type="text" name="email" id="email" value="" class="text ui-widget-content ui-corner-all" />
        </fieldset>
      </form>
    </div>  
    <div id="dlg-forgot" title="Reset Password">
      <form>
        <fieldset>
            <label for="uname3">Username</label>
            <input type="text" name="uname3" id="uname3" class="text ui-widget-content ui-corner-all" />
            <br />
            <label for="email3">Email</label>
            <input type="text" name="email3" id="email3" value="" class="text ui-widget-content ui-corner-all" />
        </fieldset>
      </form>
    </div>
<!-- contentwrapper -->
</body>
</html>
