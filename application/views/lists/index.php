<script type="text/javascript">
  $(function() {
    $('#accordion').accordion({
			animated: 'easeOutElastic',
			active: false,          //close at all panels startup
			autoHeight: false,
			navigation: false,
			collapsible: true,
			clearStyle : true,
			icons: { 'header': 'ui-icon-plus', 'activeHeader': 'ui-icon-minus' }
			
    });
    $('#accordionResizer').resizable({
			   minHeight: 40,
			   resize: function() {
 			      $('#accordion').accordion('resize');
			   }
    });
   $('#accordian').hide();    $('#dialog:ui-dialog' ).wijdialog('destroy');
   $('#prntlist').prop('disabled',true);
   //Append a click event listener to button
   $('#showlist').bind('click', function() {
	     $('#prntlist').button('enable');
    });   
   $('#grp-descr').hide();
   $('#itm-descr').hide();
   $('#itm-dropdesc').hide();
   $('#itm-descarea').hide();
   $('#mov-dropdown').hide();
   $('#custom-area').hide();
   $('#mov-dropdown').change(function() {
        $('#mov-descr').show();
        var selData = rtnSelectedIdStr('mov');
        if(selData['sid'] > 0)
        {
            paramData = { grpid:  selData['sid'] };
        }
        console.log('mov-dropdown-id('+paramData['grpid']+') grpstr('+selData['str']+')');
   });
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
          console.log('paramData-id('+paramData['grpid']+') grpstr('+selData['str']+')');
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
         $('#itm-dropdown').wijdropdown();
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
   $('#dialog-form').wijdialog({
            autoOpen: false,
            height: 525,
            width: 425,
            modal: true,
	    open: function() { $('.ui-dialog').css('box-shadow','inset -5px -5px 5px #888');
	                       clearControls();
	       },
            buttons: {	
                'Save': function() {
                    //display ajax loader animation
                    $( '#ajaxLoadAni' ).fadeIn( 'fast' );
                    var bValid = updSelectedItem();
                    $( '#ajaxLoadAni' ).fadeOut( 'slow' );
                    if ( bValid ) {
                      window.location.reload();
                    }
                   $( this ).wijdialog( 'close' );
                },
                'Delete': function() {
	           //display ajax loader animation
                    $('#ajaxLoadAni').fadeIn( 'fast' );
                    var bValid = updItemRecord('del');
                    $('#ajaxLoadAni' ).fadeOut( 'slow' );
                    if (bValid)
                    {
                      window.location.reload();
                    }
                    $( this ).wijdialog( 'close' );
                },
                'Move': function() {
                    showGroupDialog();
                   //$( this ).wijdialog( 'close' );
 		        },
                'Cancel': function() {
                    $( this ).wijdialog( 'close' );
                }
            },
            close: function() {
               //$(this).wijdialog('destroy');
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
                $('#dialog-form').wijdialog('open');
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
               console.log('***['+data+']***');
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
			 callPrint('showhere');
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
	 });
 $('#refresh')
	 .button({
            icons: {
	        text: false,
                primary: 'ui-icon-arrowrefresh-1-w'
            },
         })
	 .click(function() {
            window.location.reload(true);		
		  
	 });
  $('#custom-area').wijdialog({
       autoOpen: false,
       captionButtons: {
	    pin: {visible: false },
	    refresh: {visible: true },
	    toggle: {visible: false },
	    minimize: {visible: false },
	    maximize: {visible: false },
	    close: {visible: false }
          },       
       modal: true,
       title: 'Select Group to Assign',
       resizable: false,
       height: 300,
       width: 400,
       zIndex: 0,
       open: function() { $('.custom-area').css('box-shadow','inset -5px -5px 5px #888');
       },
       buttons: {	
               'Save': function() {
                    //display ajax loader animation
                   $('#ajaxLoadAni').fadeIn( 'fast' );
                   var bValid = updItemRecord();
                   $('#ajaxLoadAni' ).fadeOut( 'slow' );
                   if (bValid)
                  {
                     window.location.reload();
                  }
	              $( this ).wijdialog( 'close' );
               },
               'Cancel': function() {
                   $( this ).wijdialog( 'close' );
               }
           },
           close: function() {
              //   clearControls();
     	      $('#dialog-form').wijdialog('destroy');
           }
    })
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
  function customAlert(output_msg, title_msg)
  {
    if (!title_msg)
        title_msg = 'Alert';
    if (!output_msg)
        output_msg = 'No Message to Display.';
    	
  }
  function showGroupDialog()
  {
      var rtn = false;
      var param = { grpid: 0,
                    itmid: 0 };      
      var iid = 0;
      var gid = 0;
      var itm = rtnTextboxIdStr('itm');
      if(itm['sid'] >= 0)
      {
          iid = itm['sid'];
      }
      console.log(iid);
      if(iid == 0) {
        customAlert('You need to select a valid item to move.','Yo');
      } else {
        var grp = rtnSelectedIdStr('grp');
        if(grp['sid'] > 0)
        {
           param['grpid'] = grp['sid'];
           param['itmid'] = itm['sid'];
        }
      }
      console.log(param);
      if(param['grpid'] > 0)
      {
        $('#custom-area').empty();
        $.ajax({
          type: 'POST',
           url: 'index.php?lists/getgroups',
          data: param,
         cache: false,
         async: false,
       success:
        function(data){
          $('#custom-area').html(data);
          $('#custom-area').wijdialog('open');	
          $('#mov-dropdown').wijdropdown();
          $('#mov-dropdown').show();
          $rtn = true;
       },
       beforeSend: function(){
          //console.log( 'showGroupDialog-beforeSend' );
       },
       complete: function (xhr, status) {
          if (status === 'error' || !xhr.responseText) {
            console.log('showGroupDialog-Complete-status=error');
          } else {
            var data = xhr.responseText;
            console.log( 'showGroupDialog-complete-xhr.resonseText='+ data);
          }
       },
       error: function(response) {
          console.log('moveItemToGroup-error: '+response.status + ' ' + response.statusText);
       }                                  
       });
      }
  }
  function updItemRecord(mode)
  {
     var rtn = false;
     var param = { mode: 'upd',
                  grpid: 0,
                  itmid: 0,
                  descr: '' };
     if(mode){
	    param['mode'] = mode;
     }
     var tmp = rtnTextboxIdStr('mov'); //get mov dropdown id
     if(typeof tmp != 'undefined' && tmp['sid'] >= 0) {
        param['grpid'] = tmp['sid'];
     }
     tmp = rtnTextboxIdStr('itm');
     if(typeof tmp != 'undefined' && tmp['sid'] >= 0)  {
	     param['itmid'] = tmp['sid'];
         param['descr'] = tmp['str'];
     }
     console.log(param);
     if(param['mode'].length > 0 && param['itmid'] > 0 && param['descr'].length > 0)
     {
        $('#showhere').empty();
        $.ajax({
         type: 'POST',
         url: 'index.php?lists/upditem',
         data: param,
         cache: false,
         async: false,
        success:
             function(data){
           $('#showhere').html(data); 
           $('#showhere').show();
           $rtn = true;
             },
             beforeSend: function(){
           //console.log( 'updItemRecord-beforeSend' );
             },
             complete: function (xhr, status) {
           if (status === 'error' || !xhr.responseText) {
              console.log('updItemRecord-Complete-status with error');
           }
         },
         error: function(response) {
           console.log('updItemRecord-error: '+response.status + ' ' + response.statusText);
         }                                  
      });
    }
    return rtn;
  }
  function updSelectedItem()
  {
      var rtn = false;
      var apiks = new Array();
      var grp = rtnTextboxIdStr('grp');  //which group dropdown mov or grp
      if(grp['sid'] >= 0)
      {
          apiks.push( 'g.' + grp['sid'] + '|' + grp['str'] );
      }
      var itm = rtnTextboxIdStr('itm');
      if(itm['sid'] >= 0)
      {
          apiks.push( 'i.' + itm['sid'] + '|' + itm['str'] );
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
	       //$('#showhere').html(data).append;
	       console.log( 'updSelectedItem-complete-xhr.resonseText='+ data);
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
     $('#' + which).show();
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
	    $('select option:selected').removeAttr('selected');
	    //$(this).attr('selectedIndex', '-1');
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
     rtn = { sid: id, str: str };
     return rtn;
  }
 
  function rtnTextboxIdStr(which)
  {
      var rtn = { sid: 0,
                  str: ''};
      var id = 0;
      var tbx1 = '';
      var tbx2 = 'xx';
      $('#' + which + '-dropdown option:selected').each(function () {
           tbx1 = $(this).text();
    	   id = $(this).val();
      });
      if(tbx1 != '')
      {
        $('input[type="text"]').each(function(){
            if($(this).attr('name') == which + '-descr'){
                   tbx2 = $(this).val();
                }
         });
      }
      rtn = { sid: id,
              str: tbx2   };
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
</script>

<script type="text/javascript">
   var gibberish=["This is just some text to read while you do nothing.  ",
		  "Welcome to Duke's CSS Library", "Dreams come to those that drink the kool aid or take a magic carpet ride"];
   function filltext(words){
      for (var i=0; i<words; i++)
      document.write(gibberish[Math.floor(Math.random()*3)]+" ")
   }
</script>
   <div id="leftcolumn" class="column">
      <div class="innertube">
        <button id="refresh" class="refresh">Refresh</button>
        <button id="editlist" class="editlist">Edit</button>
        <button id="showlist" class="showlist">List</button>
        <button id="prntlist" class="prntlist">Print</button>
      </div>
      <div class="innercolumn">
	    <div id="accordionResizer" style="padding: 1px; height: 90%;" class="ui-widget-content">
          <div id="accordion">
	         <?php foreach($groups as $k => $v): 
		         echo '<h3><a href="#sec-'.$k.'">'.$v.'</a></h3>';
	             $cnt = 0;
	             foreach($items as $row):
                   if($cnt == 0){
                      echo '<div>'; 
                      $cnt++;
                   }
		           if($row->grpid == $k) {
                        $checked = '';
                        foreach($picks as $key => $val){
                          if($key == $k)
                          {
                            if(stripos($val, '.'.$row->itemid.'.') !== false){
                              $checked = 'checked=checked';
                               break;
                             }
                           }
                        }
	                    echo '<input id="'.$k.'.'.$row->itemid.'" type="checkbox" '.$checked.' />';
	                    echo '<label for="'.'g.'.$k.'.c.'.$row->itemid.'">'.$row->item.'</label>';
	                 }
	             endforeach; 
                 if($cnt > 0) { 
	               echo '</div>';  //atleast one item for group
                 }
	             $cnt = 0; 
	         endforeach; ?> 
	      </div>
	    </div>
      </div>
   </div>
   <div id="midcolumn" class="column">
      <div class="innertube">
          <div id="edit-table">
             <div id="showhere">
     	      <div id="custom-area" title="Move Item to Group">
  	          </div>
           
             </div>
	      </div>
      </div>
      <div id="dialog-form" title="Edit List Items">
  	     <p class="validateTips">Select what to update</p>
 	     <form>
	       <fieldset>
	         <label for="grp-dropdown">Available Groups</label>
	         <select name="grp-dropdown" id="grp-dropdown" style="width: 70%; margin: 5px 5px 5px 5px;"> 
	           <option value="-1"></option>
	           <option value="0">--Add New--</option>
                <?php foreach($groups as $k => $v): ?> 
                      <?php echo '<option value='.$k.'>'.$v.'</option>'; ?>     
                <?php endforeach; ?> 
	         </select>
	         <br/>
	         <div id='grp-descr'>
		     <p />
  	           <label for="grp-descr">Description </label>
	           <input type="text" name="grp-descr" id="grp-descr" value="" maxlength="80" size="60" style="width:80%;"/>
	         </div>
		     <br/>
		     <p />
             <div id='itm-droparea'>
             </div>
             <div id='itm-descarea'>
  	           <label for="itm-descr">Description </label>
	           <input type="text" name="itm-descr" id="itm-descr" value="" maxlength="80" size="60" style="width:80%" />
	         </div>
	       </fieldset>
	     </form>
	  </div>
   </div>      
