$(document).ready(function () {
  $('#frm-dropdown').wijdropdown();
  $(':input[type="text"]').wijtextbox();
  $('#dialog-imag').wijdialog();
  $('#dialog-imag').hide();
  $('#frmdel').prop('disabled',true);
  $('#frmupd').prop('disabled',true);
  $('#frm-descr').keyup(function(e) {
      code = e.keyCode ? e.keyCode : e.which;
      if(code.toString() >= 48 && code.toString() <= 90) { // between zero and z
         $('#frmupd').button('enable');
      }
      //console.log('code: '+code.toString());
  });      
  $('#frm-descr').live('input', function() {
       if(validGroupPick())
	      $('#frmupd').button('enable');
  });
  $('#frmdel')
     .button({
            icons: {
                primary: 'ui-icon-circle-minus'
            },
         })
         .click(function() {
            if(getEditMode() === 'item') {
                if(updateItem('del')) {
                }
            } else if(getEditMode() === 'group') {
                if(updateGroup('del')) {
                }
            }
   	        $("#formarea").empty();
            //window.location.reload(true);	
  });
  $('#frmupd')
     .button({
            icons: {
                primary: 'ui-icon-check'
            },
         })
         .click(function() {
            if($('#editwhat').val() === 'item') {
                if(updateItem('upd')) {
                }
            } else if($('#editwhat').val() === 'group') {
                if(updateGroup('upd')) {
                }
            }
   	        $("#formarea").empty();
  });
  $('#frmout')
     .button({
            icons: {
                primary: 'ui-icon-circle-close'
            },
         })
         .click(function() {
            //inlineDebug(this);
   	        $("#formarea").empty();
  });
})
$('#frm-dropdown').change(function () {
    var pikdata = { sid: 0,
                    str: ''};
    pikdata = rtnSelectedIdStr('frm');
    grpidnbr = pikdata['sid'];
    grpdescr = pikdata['str'];
    itmidnbr = $('#itmidnbr').val();
    editmode = getEditMode();
    $('#frmupd').button('enable');
    if(parseInt(itmidnbr,10) <= 0 && editmode === 'group') {  
       if(parseInt(pikdata['sid'],10) > 0)
       {
          $('#grpidnbr').val(grpidnbr);
          $('#frm-descr').val(grpdescr);
          if(grpdescr.length > 0 &&
             grpdescr.indexOf('Add New') < 0 &&
             grpdescr.indexOf('insert new item') < 0) {
            $('#frmdel').button('enable');
          }
        } else {
           console.log('itmgrd2-a-paramData-id(error-nada!)');
        }
    } else {
      console.log('itmgrd2-b-item group id for update');
    }
    console.log('itemgrd2-frm-dropdown-mode=('+editmode+') - descr('+$('#frm-descr').val()+') grpid('+grpidnbr+')');
});