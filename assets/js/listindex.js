   $(function() {
      $('#accordion').accordion({
			animated: 'easeslide',
			active: false,          //close at all panels startup
			autoHeight: false,
			navigation: false,
			collapsible: true,
			clearStyle : true
	       });
   });
   $(function() {
      $('#accordionResizer').resizable({
			   minHeight: 40,
			   resize: function() {
 			      $('#accordion').accordion('resize');
			   }
   });
      
   $('#dialog:ui-dialog' ).dialog('destroy');
   $('#prntlist').prop('disabled',true);
   //Append a click event listener to button
   $('#showlist').bind('click', function() {
	$('#prntlist').button('enable');
    });   
   $('#grp-descr').hide();
   $('#itm-descr').hide();
   $('#mov-dropdown').hide();
   $('#itm-dropdesc').hide();
   $('#itm-descarea').hide();
   $('#custom-alert').hide();
   $('#itm-droparea').change(function() {
	$('itm-descr').show();
        $('#itm-descarea').show();
        var selData = rtnSelectedIdStr('itm');
   });
   $('#grp-dropdown').change(function () {
      var paramData = new Array();
      var selData = { sid: 0,
                      str: ''
      }
      selData = rtnSelectedIdStr('grp');
      if(selData['sid'] > 0)
      {
          paramData = { grpid:  selData['sid'] };
      }
      console.log('listindex.js-paramData-id('+paramData['grpid']+') grpstr('+selData['str']+')');
       //display ajax loader animation
      //$( '#ajaxLoadAni' ).fadeIn( 'slow' );
      if(paramData['grpid'] != '')
      {
	 $('#itm-dropdown').empty();
	 $('#itm-dropdesc').show();
	 clearTextBox('itm-descr');
	 $.ajax({
	      type: 'POST',
	      url: 'index.php?lists/getitems',
	      data: paramData,
	      cache: false,
	      async: false,
	      success:
		function(data){
		 $('#itm-droparea').show();
		 $('#itm-droparea').html(data);
                 $("#itm-dropdown").wijdropdown();
      },
	      error: function(response) {
		  console.log('Ajax-error: '+response.status + ' ' + response.statusText);
	      }              
	  });
      } else {
	 $('#itm-dropdown').empty();
	 $('#itm-droparea').hide();
      }
       
   });
   $('#dialog-form').dialog({
        autoOpen: false,
        height: 525,
        width: 425,
        modal: true,
        open: function() { $('.ui-dialog').css('box-shadow','inset -5px -5px 5px #888')},
        buttons: {	
            'Save': function() {
            //display ajax loader animation
        $( '#ajaxLoadAni' ).fadeIn( 'fast' );
        var bValid = updSelectedItem();
        $( '#ajaxLoadAni' ).fadeOut( 'slow' );
        if ( bValid ) {
          window.location.reload();
        }
        $( this ).dialog( "close" );
            },
            'Move': function() {
               moveItemToGroup();
            },
            'Cancel': function() {
               $( this ).dialog( "close" );
            }
        },
        close: function() {
        }
   });
  $('#printdata')
         .button({
            icons: {
                primary: 'ui-icon-print'
            },
         })
         .click(function() {
	    callPrint('showhere');
	 });
  $('#editlist')
         .button({
            icons: {
                primary: 'ui-icon-wrench'
            },
         })
         .click(function() {
                $('#dialog-form').dialog('open');
	 });
  $('#showlist')
	 .button({
            icons: {
                primary: 'ui-icon-gear'
            },
         })
	 .click(function() { var ar = getUserChkdItems();
		if(ar.length >= 0){
		  listArrayToConsole(ar);
		}
		var paramdata = {
		   'piks': ar
		};                     
		$('#showhere').empty();
		$.ajax({
		 type: 'POST',
		 url: 'index.php?lists/updpicks',
		 data: paramdata,
		 cache: false,
		 async: false,
		success:
		     function(data){
		       $('#showhere').html(data); 
 		       $('#showhere').show();
		     },
		     beforeSend: function(){
		       //console.log( 'Ajax-beforeSend' );
		     },
		     complete: function (xhr, status) {
		       if (status === 'error' || !xhr.responseText) {
			  console.log('saveList-Complete-status=error');
		       } else {
			 var data = xhr.responseText;
			 $('#showhere').html(data).append;
		       }
		   },
		     error: function(response) {
		       console.log('saveList-Ajax-error: '+response.status + ' ' + response.statusText);
		   }                                  
		});                   
		  
	 });
 $('#prntlist')
	 .button({
            icons: {
                primary: 'ui-icon-print'
            },
         })
	 .click(function() { var ar = getUserItemDescr();
		if(ar.length >= 0){
		  var paramdata = {
		     'qtys': ar
		  };                     
		  $('#showhere').empty();
		  $.ajax({
		   type: 'POST',
		   url: 'index.php?lists/prntsave',
		   data: paramdata,
		   cache: false,
		   async: false,
		  success:
		       function(data){
			 $('#showhere').html(data); 
			 $('#showhere').show();
			 callPrint("showhere");
		       },
		       beforeSend: function(){
			 //console.log( 'prntsave-beforeSend' );
		       },
		       complete: function (xhr, status) {
			 if (status === 'error' || !xhr.responseText) {
			    console.log('prntsave-Complete-status=error');
			 } else {
			   var data = xhr.responseText;
			   $('#showhere').html(data).append;
			 }
		     },
		       error: function(response) {
			 console.log('prntsave-error: '+response.status + ' ' + response.statusText);
		     }
		  });
		}
	 }
  );
 $('#refresh')
	 .button({
            icons: {
	        text: false,
                primary: 'ui-icon-arrowrefresh-1-w'
            },
         })
	 .click(function() {
               window.location.reload(true);		
		  
	 }
  );
   $('#custom-alert').html(output_msg).dialog({
       modal: true,
       title: title_msg,
       resizable: false,
       height: 225,
       width: 425,
       open: function() { $('.custom-alert').css('box-shadow','inset -5px -5px 5px #888')},
       buttons: {	
               'Save': function() {
         //display ajax loader animation
                   /*
                   $( '#ajaxLoadAni' ).fadeIn( 'fast' );
                   var bValid = updSelectedItem();
                   $( '#ajaxLoadAni' ).fadeOut( 'slow' );
                   if ( bValid ) {
                     window.location.reload();
                   }
                   */
                   alert('now what');
                   $( this ).dialog( "close" );
               },
               'Cancel': function() {
                   $( this ).dialog( "close" );
               }
           },
           close: function() {
               // whatever
           //   clearControls();
           }
   });
  function callPrint(strid) {
      var prtContent = document.getElementById(strid);
      var WinPrint = window.open('', 'PrintWindow', 'left=210,top=110,width=800,height=900,toolbar=yes,scrollbars=yes,resizable=yes');
      WinPrint.document.write('<html><head><title>The List</title>'+
              '<link rel="stylesheet" href="<?php echo base_url();?>style/print.css" type="text/css" />'+
              '</head><body><div="prnt">');
      WinPrint.document.write(prtContent.innerHTML);
      WinPrint.document.write('</div><p><button id="prntlist" class="prntlist" onclick="print();">Print</button></body></html>');
      WinPrint.document.close();
      WinPrint.focus();
  }
  function clearControls()
  {
      clearTextBox('grp-descr');
      clearTextBox('itm-descr');
      clearDropDown('grp-dropdown');
      clearDropDown('itm-dropdown');
      
  }
  function custom_alert(output_msg, title_msg)
  {
    if (!title_msg)
        title_msg = 'Alert';

    if (!output_msg)
        output_msg = 'No Message to Display.';
    $('#custom-alert').html(output_msg);
    $('#custom-alert').show();
    $('#custom-alert').dialog('open');
  }
  function moveItemToGroup()
  {
      var rtn = false;
      var apiks = new Array();
      var iid = 0;
      var itm = rtnTextboxIdStr('itm');
      if(itm['sid'] >= 0)
      {
          iid = itm['sid'];
      }
      console.log(iid);
      if(iid == 0)
      {
	 custom_alert('You need to select a valid item to move.','Yo');
      } else {
	 custom_alert('Move '+iid+' to Group');
      }
   
  }
  {
      clearTextBox('grp-descr');
      clearTextBox('itm-descr');
      clearDropDown('grp-dropdown');
      clearDropDown('itm-dropdown');
      
  }
  function updSelectedItem()
  {
      var rtn = false;
      var apiks = new Array();
      var grp = rtnTextboxIdStr('grp');
      if(grp['sid'] >= 0)
      {
          apiks.push( 'g.' + grp['sid'] + "|" + grp['str'] );
      }
      var itm = rtnTextboxIdStr('itm');
      if(itm['sid'] >= 0)
      {
          apiks.push( 'i.' + itm['sid'] + "|" + itm['str'] );
      }
      console.log(apiks);
      var aparm = { 'piks': apiks }
      $('#showhere').empty();
      $.ajax({
       type: 'POST',
       url: 'index.php?lists/updlist',
       data: aparm,
       cache: false,
       async: false,
      success:
	   function(data){
	     $('#showhere').html(data); 
	     $('#showhere').show();
	     $rtn = true;
	   },
	   beforeSend: function(){
	     //console.log( 'updSelectedItem-beforeSend' );
	   },
	   complete: function (xhr, status) {
	     if (status === 'error' || !xhr.responseText) {
		console.log('updSelectedItem-Complete-status=error');
	     } else {
	       var data = xhr.responseText;
	       $('#showhere').html(data).append;
	       //console.log( 'updSelectedItem-complete-xhr.resonseText='+ data);
	     }
	 },
	   error: function(response) {
	     //console.log('updSelectedItem-error: '+response.status + ' ' + response.statusText);
	 }                                  
      });
      return rtn;
  }
  function clearTextBox(which)
  {
     var str = '';
     $("#" + which).show();
     $('input[type="text"]').each(function(){
         if($(this).attr('name') == which){
	    $(this).val(str);
         }
     });
  }
  function clearDropDown(which)
  {
     var str = '';
     $('#' + which).show();
     $('select').each(function(){
         if($(this).attr('name') == which){
	    $(this).val(str);
         }
     });
  }
  function rtnSelectedIdStr(which)
  {
      var rtn = { sid: 0,
                  str: ''};
      var id = 0;
      var str = '';
      $('#' + which + '-dropdown option:selected').each(function () {
           str = $(this).text();
	   id = $(this).val();
       });
      if(str != '')
      {
        $('#' + which + '-descr').show();
	$('input[type="text"]').each(function(){
	    if($(this).attr('name') == which + '-descr'){
	       $(this).val(str);
             }
	 });
      }
      rtn = {
	 sid: id,
	 str: str   };
      return rtn;
  }
  function rtnTextboxIdStr(which)
  {
      var rtn = { sid: 0,
                  str: ''};
      var id = 0;
      var tb1 = '';
      var tb2 = 'xx';
      $('#' + which + '-dropdown option:selected').each(function () {
           tb1 = $(this).text();
	   id = $(this).val();
       });
      if(tb1 != '')
      {
	$('input[type="text"]').each(function(){
	    if($(this).attr('name') == which + '-descr'){
               tb2 = $(this).val();
            }
	 });
      }
      rtn = { sid: id,
              str: tb2   };
      return rtn;
  }
  function getUserChkdItems()
  {
    var arr = new Array();
    $("input[type='checkbox']:checked").each(
         function() { var itm = { 'gd' : this.id.substr(0,this.id.indexOf('.')),
                                  'id' : this.id.substr(this.id.indexOf('.')+1) };
         arr.push(itm); }
   )
   return arr;
  }
  function listArrayToConsole(arr)
  {
     if($.isArray(arr)){
       for(j = 0; j < arr.length; j++){
         var grpid = arr[j].gd + '|' + arr[j].id;
         console.log('['+grpid+']');
       }
     }
  }
  function getUserItemDescr()
  {
    var arr = new Array();
    $("input[type='text']").each( function() {
		  var itm = { 'id'  : this.id.substr(this.id.indexOf('.')+1),
		  	      'str' : this.value};
                  if(itm.id.indexOf('-descr') <= 0){
                     arr.push(itm);
		  } 
    
	 })
   return arr;
  }
});