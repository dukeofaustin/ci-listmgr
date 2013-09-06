
  function getEditMode() {
     return $('#editwhat').val().toLowerCase();
  }
  /* showFindForm is used to look up tbl_lstitem descriptions */
  function showFindForm(which)
  {
     $.ajax({
        type: 'POST',
        url: 'index.php?lists/findform',
        cache:false,
        async: false,
        success:
          function(data){
	        $("#formarea").empty();
            $("#formarea").html(data);
            console.log(data);
        },
        complete: function (xhr, status) {
            if (status === 'error' || !xhr.responseText) {
               console.log('showFindForm-Complete status: Error');
            }
        },
        error: function(response) {
            console.log('showFindForm-error-status: '+response.status + ' statusText: ' + response.statusText);
        }
      });
      return false;
  }
  function loadPage(destId, pagetoload){
     $('<div id="info" />').load(pagetoload, function() {
                        $(this).appendTo(destId)
                                    .slideDown(3000);
     });
     return false;
  }
  /* showEditForm is used to Ins/Upd tbl_lstgrp records and Ins-Only tbl_lstitem records */
  function showEditForm(which)
  {
    if(which.toLowerCase().indexOf('group') != -1) {
        displayGroup();
    } else if (which.toLowerCase().indexOf('item') != -1) {
        displayItem(0,-1,'(insert new item)');
    }
  }
  function displayItem(grpid, itmid, descr)
  {
     var descrip = cleanItemString(descr);
     var params = { 'which': 'item',
                    'grpid': grpid,
                    'itmid': itmid,
                    'descr': descrip };
     $.ajax({
        type: 'POST',
        url: 'index.php?lists/getform',
        data: params,
        cache:false,
        async: false,
        success:
          function(data){
	        $("#formarea").empty();
            $("#formarea").html(data);
            if(itmid > 0)
               $('#frmdel').button('enable');
        },
        complete: function (xhr, status) {
            if (status === 'error' || !xhr.responseText) {
               console.log('displayItem-Complete status: Error');
            }
        },
        error: function(response) {
            console.log('displayItem-error-status: '+response.status + ' statusText: ' + response.statusText);
        }
      });
      return false;
  }
  function updateItem(editmode)
  {
     var rtn = false;
     var descrip = $('#frm-descr').val();
     descrip = cleanItemString(descrip);
     var pikdata = { sid: 0,
                     str: ''};

     pikdata = rtnSelectedIdStr('frm');
     grpidnbr = pikdata['sid'];
     grpdescr = pikdata['str'];
     itmidnbr = $('#itmidnbr').val();

     if (editmode === 'del') {
        descrip = '<deleted>';
     }
     if(descrip.length > 1) {
        var params = { 'mode': editmode,
                       'grpid': grpidnbr,
                       'itmid': itmidnbr,
                       'descr': descrip,
                       'ajax': '1'       
                     };
        $.ajax({
           type: 'POST',
           url: 'index.php?lists/upditem',
           data: params,
           cache:false,
           async: false,
           beforeSend: function(){
               show_Busy('formarea');
           },
           success:
             function(data){
               rtn = true;
               var linkstr = '('+grpidnbr+','+itmidnbr+',';  //find unique string in row
               putTableCellText(linkstr, descrip);           //replace old string with new
           },
           complete: function (xhr, status) {
              if (status === 'error' || !xhr.responseText) {
                 console.log('updateItem-complete status: Error');
              }
              $( this ).wijdialog( "close" );
           },
           error: function(response) {
              console.log('updateItem-ajax-error-status: '+response.status + ' statusText: ' + response.statusText);
           }
           
         });
     }
     return rtn;
  }
  function displayGroup()
  {
     var params = {'which': 'group',
                   'grpid': 0,
                   'itmid': -1,
                   'descr': '' };
     $.ajax({
        type: 'POST',
        url: 'index.php?lists/getform',
        data: params,
        cache:false,
        async: false,
        success:
          function(data){
	        $("#formarea").empty();
            $("#formarea").html(data);
        },
        complete: function (xhr, status) {
            if (status === 'error' || !xhr.responseText) {
               console.log('displayGroup-Complete status: Error');
            }
        },
        error: function(response) {
 	       console.log('displayGroup-error-status: '+response.status + ' statusText: ' + response.statusText);
        }
      });
      return false;
  }
  function updateGroup(editmode)
  {
     var rtn = false;
     var descrip = $('#frm-descr').val();
     if (editmode === 'del') {
        descrip = '<deleted>';
     }
     descrip = cleanItemString(descrip);
     var params = { 'mode': editmode,
                    'grpid': $('#grpidnbr').val(),
                    'itmid': $('#itmidnbr').val(),
                    'descr': descrip,
                    'ajax': '1'       
                  };
     $.ajax({
        type: 'POST',
        url: 'index.php?lists/updgroup',
        data: params,
        cache:false,
        async: false,
        beforeSend: function(){
            show_Busy('formarea');
        },
        success:
          function(data){
            rtn = true;
        },
        complete: function (xhr, status) {
           if (status === 'error' || !xhr.responseText) {
              console.log('updateGroup-complete status: Error');
           }
           hide_Busy('formarea');
        },
        error: function(response) {
    	   console.log('updateGroup-ajax-error-status: '+response.status + ' statusText: ' + response.statusText);
        }
      });
     return rtn;
  }
  function putTableCellText(link, txt)
  {  var rtn = '';
     var table = document.getElementById('gridtable');
     var rows = table.getElementsByTagName('tr');
     for (var i=0,len=rows.length; i<len; i++){
        var grp = rows[i].cells[0].innerHTML;
        var tmp = rows[i].cells[1].innerHTML;
        var lnk = rows[i].cells[2].innerHTML;
        //console.log(tmp);
        if(lnk.indexOf(link) > 0)
        {
           rows[i].cells[1].innerHTML = txt;
           console.log('replace['+tmp+'] with ['+txt+']');
           break;
        }
    }
    return rtn;
  }
  function getTableCellText(link)
  {  var rtn = '';
     var table = document.getElementById('gridtable');
     var rows = table.getElementsByTagName('tr');
     for (var i=0,len=rows.length; i<len; i++){
        var grp = rows[i].cells[0].innerHTML;
        var tmp = rows[i].cells[1].innerHTML;
        var lnk = rows[i].cells[2].innerHTML;
        //console.log(tmp);
        if(lnk.indexOf(link) > 0)
        {
              //console.log('GROUP['+grp+']');
          rtn = tmp+'|'+grp;
          break;
        }
     }
     return rtn;
  }
  function update_Page(html){
      window.setTimeout( function(){
       $("#formarea").empty();
       $('#gridview').empty();
       $('#gridview').html(html);
      } , 200);
  }
  function validGroupPick()
  {
      var pikdata = { sid: 0,
                      str: ''};
      pikdata = rtnSelectedIdStr('frm');
      return (pikdata['sid'] > 0);
  }
