//<script type="text/javascript">
$(function() {
    $(":input[type='radio']").wijradio();
    $(":input[type='dropdown']").wijdropdown();
    $("#frm-dropdown").wijdropdown();
    $(":input[type='text'],textarea, input[type='password']").wijtextbox();
    $(":input[type='checkbox']").wijcheckbox();
    $('#frmdel').prop('disabled',true);
    $('#frmupd').prop('disabled',true);
    $('#fname, #fpath, #descr').keyup(function(e) {
      code = e.keyCode ? e.keyCode : e.which;
      if(code.toString() >= 48 && code.toString() <= 90) { // between zero and z
         $('#frmupd').button('enable');
      }
      //console.log('code: '+code.toString());
    });      
    $('#fname').live('input', function() {
       if(validGroupPick())
	      $('#frmupd').button('enable');
    });
    $('input[type=radio]').live('change', function() {
        var chkval = getRadioValue('allow');
    });
    $('#frmdel')
       .button({
            icons: {
                primary: 'ui-icon-circle-minus'
            },
         })
         .click(function() {
            if(getEditMode() === 'item') {
                if(updImageItem('del')) {
                }
            } else if(getEditMode() === 'group') {
                if(updImageGroup('del')) {
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
                 
                //if(validateItemData() && updImageItem('upd')) {
                if(updImageItem('upd')) {                    
                }
            } else if($('#editwhat').val() === 'group') {
                if(updImageGroup('upd')) {
                }
            }
   	        //$("#formarea").empty();
    });
    $('#frmout')
     .button({
            icons: {
                primary: 'ui-icon-circle-close'
            },
         })
         .click(function() {
   	        $("#formarea").empty();
    });
    $('#frm-dropdown').change(function () {
        var pikdata = { sid: 0,
                        str: ''};
        pikdata = rtnSelectedIdStr('frm');
        tagidnbr = pikdata['sid'];
        grpdescr = pikdata['str'];
        $('#frmupd').button('enable');
        if(parseInt(pikdata['sid'],10) > 0)
        {
           $('#tagidnbr').val(tagidnbr);
        } else {
           console.log('editimage-a-paramData-id(error-nada!)');
        }
        console.log('editimage-frm-dropdown-mode=('+editmode+') - descr('+$('#descr').val()+') tagid('+tagidnbr+')');
    });
});
    var fname = $( "#fname" ),
        fpath = $( "#fpath" ),
        descr = $( "#descr" ),
        allow = $( "#allow" ),
        allFlds = $( [] ).add( fname ).add( fpath ).add( descr ).add( allow ),
        tips = $( ".validateTips" );
        
    function updImageItem(mode)
    {
        if(typeof mode == 'undefined' || mode === ''){
           mode = 'upd';
        }
        var params = {
           emode: mode,
           which: getEditMode(),
           imgid: $('#imgidnbr').val(),
           tagid: $('#tagidnbr').val(),
           fname: $('#fname').val(),
           fpath: $('#fpath').val(),
           descr: $('#descr').val(),
           allow: getRadioValue('allow')
        };
        //inlineDebug();
        $.ajax({
        type: 'POST',
        url: 'index.php?gallery/updimage',
        dataType: 'json',
        data: params,
        cache:false,
        async: false,
        success:
          function(data){
            //console.log( 'Ajax-no errors' );
            //console.log('data.message='+data);
            //alert(data);
            $('#formarea').append(data); 
        },
        beforeSend: function(){
            console.log( 'Ajax-beforeSend' );
        },
        complete: function (xhr, status) {
            if (status === 'error' || !xhr.responseText) {
               console.log('Complete-status=error');
            } else {
              var data = xhr.responseText;
              $('#formarea').html(data).append;
              console.log( 'Ajax-complete-xhr.resonseText='.data);
            }
        },
        error: function(response) {
            console.log('Ajax-error: '+response.status + ' (' + response.statusText+')');
        }
        });
    }
    function updImageGroup(editmode)
    {
        var rtn = false;
        var pikdata = { sid: 0,
                        str: ''};
   
        pikdata = rtnSelectedIdStr('frm');
        tagid = pikdata['sid'];
        descr = pikdata['str'];
        
        var descrip = $('#descr').val();
        
        if (editmode === 'del') {
           descrip = '<deleted>';
        }
        descrip = cleanItemString(descrip);
        var params = { 'mode': editmode,
                       'tagid': tagid,
                       'descr': descrip,
                       'ajax': '1'       
                     };
        $.ajax({
           type: 'POST',
           url: 'index.php?gallery/upditag',
           data: params,
           cache:false,
           async: false,
           beforeSend: function(){
               show_Busy();
           },
           success:
             function(data){
               rtn = true;
           },
           complete: function (xhr, status) {
              if (status === 'error' || !xhr.responseText) {
                 console.log('updateGroup-complete status: Error');
              }
              hide_Busy();
           },
           error: function(response) {
              console.log('updateGroup-ajax-error-status: '+response.status + ' statusText: ' + response.statusText);
           }
        });
      return rtn;
    }
    function validateItemData() {
        var filepathexpr = new RegExp("(\/|\\\\)([a-zA-Z0-9_ \- \s]+\\1)", "i");
        var bValid = false;
        var fname = $('#fname').val(),
            fpath = $('#fpath').val(),
            descr = $('#descr').val(),
            allow = $('#allow').val()
        bValid = checkLength( fname, "fname", 1, 128 );
        //bValid = bValid && checkRegexp( fname, filepathexpr, "Filename may consist of only alphanumeric and some special characters." );
        //bValid = bValid && checkLength( fpath, "fpath", 1, 128 );
        //bValid = bValid && checkRegexp( fpath, filepathexpr, "Filepath contains invalid character." );
        //bValid = bValid && checkLength( descr, "descr", 1, 128 );
        //bValid = bValid && checkLength( allow, "allow", 1, 4 );
        // From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/allow_address_validation/
        if(bValid){
           return true;    
        }
        return false;
    }

    function getRadioValue(which)
    {  var rtn = 0;
       $('input[type="radio"]').each(function() {
           if($(this).is(':checked')) {
             rtn = $(this).attr('value')
             //set hidden textbox value with checked radiobutton value
             $('input[type="text"]').each(function(){
                if($(this).attr('name') == which){
                   $(this).val(rtn);
                }
              });
           }
       });
       console.log('getRadioValue: '+rtn);
       return rtn;
    }
    function clearTextBox(which)
    {
       var str = '';
       $('#' + which).show();
       $('input[type="text"]').each(function(){
           if($(this).attr('name') == which){
              $(this).val(str);
           }
       });
    }

    function setTextBoxVal(which, txt)
    {
       var str = txt.val();
       $('#' + which).show();
       $('input[type="text"]').each(function(){
           if($(this).attr('name') == which){
              $(this).val(str);
           }
       });
    }
    
    function showBusy(){
       $('#showarea').block({
            message: '<img src="../cdig/images/ajax-loader.gif"/>'
        });
    }


//</script>
