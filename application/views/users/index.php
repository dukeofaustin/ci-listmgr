<title>Users Management</title>
<script type="text/javascript">
$(function(){
   $('.dg_form :submit').click(function(e){
       e.preventDefault();
       var $form = $(this).parents('form');
       var action_name = $(this).attr('class').replace("dg_action_","");
       var action_control = $('<input type="hidden" name="dg_action['+action_name+']" value=1 />');
       
       $form.append(action_control);
       
       var post_data = $form.serialize();
       action_control.remove();
       
       var script = $form.attr('action')+'/post';
       $.post(script, post_data, function(resp){
           if(resp.error){
              alert(resp.error);
           } else {
              switch(action_name){
                 case 'delete' :
                     // remove deleted rows from the grid
                     $form.find('.dg_check_item:checked').parents('tr').remove();
                     $('dg_form').load('index.php'); 

                     break;
                 case 'insert' :
                 case 'update' :
                     //alert(action_name)// do something else...
                     break;
           }
       }
    })
})
$('.dg_check_toggler').click(function(){
       var checkboxes = $(this).parents('table').find('.dg_check_item');
       if($(this).is(':checked')){
               checkboxes.attr('checked','true');
       } else {
               checkboxes.removeAttr('checked');
       }
  });
});
</script>
        <style>
                .dg_form table{
                        border:1px solid silver;
                }
                
                .dg_form th{
                        background-color: #5cebc2;
                        font-family: Calibri, Arial, sans-serif;
                        font-size: 16px;
                }
                
                .dg_form td{
                        background-color: #b0e0e6;
                        font-size:14px;
                }
                
                .dg_form input[type=submit]{
                     font-family: Calibri, Arial, sans-serif;
                     font-size: 14px;
                     margin-top: 2px;
                     margin-bottom: 2px;
                     background: #70ffd6;
                     width: 90px;
                     -moz-border-radius: 1px;
                     -webkit-border-radius: 1px;                     
                }
        </style>
 <div id="contentwrapper">
   
     <div class="innertube">
<?php
		$this->Datagrid->hidePkCol(false);
		$this->Datagrid->setHeadings(array('email'=>'E-mail'));
		$this->Datagrid->ignoreFields(array('pword'));
		if($error = $this->session->flashdata('form_error')){
			echo "<font color=red>$error</font>";
		}
		echo form_open('index.php?users',array('class'=>'dg_form'));
		echo $this->Datagrid->generate();
		echo Datagrid::createButton('delete','Delete');
		echo Datagrid::createButton('insert','Insert');
		echo form_close();
?>
	  </div>
</div>