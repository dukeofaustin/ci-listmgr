$(function(){
   $('.dg_form :submit').click(function(e){
       e.preventDefault();
       var $form = $(this).parents('form');
       var action_name = $(this).attr('class').replace("dg_action_","");
       var action_control = $('<input type="hidden" name="dg_action['+action_name+']" value=1 />');
       
       $form.append(action_control);
       
       var post_data = $form.serialize();
       action_control.remove();
       
       var script = $form.attr('action')+'/ajax';
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
                     alert('action_name')// do something else...
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