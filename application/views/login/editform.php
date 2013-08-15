<script src="<?php echo base_url();?>assets/js/useredit.js" type="text/javascript"></script>
<div id="user-area"></div>
<div id="dlg-edituser" title="<?php echo $title;?>">
    <p class="validateTips">All form fields are required.</p>
    <form>
      <fieldset>
        <label for="fname">First Name</label>
        <input type="text" name="fname" id="fname" class="text ui-widget-content ui-corner-all" value="<?php echo $users['fname'];?>" />
        <label for="lname">Last Name</label>
        <input type="text" name="lname" id="lname" class="text ui-widget-content ui-corner-all" value="<?php echo $users['lname'];?>" />
        <label for="uname">Username</label>
        <input type="text" name="uname" id="uname" class="text ui-widget-content ui-corner-all" value="<?php echo $users['uname'];?>"/>
        <label for="email">Email</label>
        <input type="text" name="email" id="email" class="text ui-widget-content ui-corner-all" value="<?php echo $users['email'];?>"/>
        <label for="pword">Password</label>
        <input type="password" name="pword" id="pword" class="text ui-widget-content ui-corner-all" value="<?php echo $users['pword'];?>"/>
        <input type="hidden" name="userid" value="<?php echo $users['userid'];?>"/>
      </fieldset>
    </form>
</div>
<div id="dlg-edituser" title="Register" />
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
