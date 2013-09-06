 
  function getEditMode() {
     return $('#editwhat').val().toLowerCase();
  }
  /* showImgFindForm is used to look up tbl_images records */
  function showImgFindForm(which)
  {
     $.ajax({
        type: 'POST',
        url: 'index.php?gallery/findform',
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
               console.log('showImgFindForm-Complete status: Error');
            }
        },
        error: function(response) {
            console.log('showImgFindForm-error-status: '+response.status + ' statusText: ' + response.statusText);
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
  /* showImgEditForm is used to Ins/Upd tbl_lstgrp records and Ins-Only tbl_lstitem records */
  function showImgEditForm(which)
  {
    if(which.toLowerCase().indexOf('group') != -1) {
        getGroupForm();
    } else if (which.toLowerCase().indexOf('item') != -1) {
        getImageForm(0,-1,'(insert new item)');
    }
  }
 
  function getImageForm(tagid, imgid, descr)
  {
     var descrip = cleanItemString(descr);
     var params = { 'which': 'item',
                    'tagid': tagid,
                    'imgid': imgid,
                    'descr': descrip };
     $.ajax({
        type: 'POST',
        url: 'index.php?gallery/getform',
        data: params,
        cache:false,
        async: false,
        success:
          function(data){
	        $("#formarea").empty();
            $("#formarea").html(data);
            if(imgid > 0)
               $('#frmdel').button('enable');
            console.log('getImageForm['+data+']');
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
     tagidnbr = pikdata['sid'];
     grpdescr = pikdata['str'];
     imgidnbr = $('#imgidnbr').val();

     if (editmode === 'del') {
        descrip = '<deleted>';
     }
     if(descrip.length > 1) {
        var params = { 'mode': editmode,
                       'tagid': tagidnbr,
                       'imgid': imgidnbr,
                       'descr': descrip,
                       'ajax': '1'       
                     };
        $.ajax({
           type: 'POST',
           url: 'index.php?gallery/upditem',
           data: params,
           cache:false,
           async: false,
           beforeSend: function(){
               show_Busy('formarea');
           },
           success:
             function(data){
               rtn = true;
               var linkstr = '('+tagidnbr+','+imgidnbr+',';  //find unique string in row
               putTableCellText(linkstr, descrip);           //replace old string with new
           },
           complete: function (xhr, status) {
              if (status === 'error' || !xhr.responseText) {
                 console.log('updateItem-complete status: Error');
              }
              hide_Busy('formarea');
           },
           error: function(response) {
              console.log('updateItem-ajax-error-status: '+response.status + ' statusText: ' + response.statusText);
           }
         });
     }
     return rtn;
  }
  function getGroupForm()
  {
     var params = {'which': 'group',
                   'tagid': 0,
                   'imgid': -1,
                   'descr': '' };
     $.ajax({
        type: 'POST',
        url: 'index.php?gallery/getform',
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
               console.log('getGroupForm-Complete status: Error');
            }
        },
        error: function(response) {
 	       console.log('getGroupForm-error-status: '+response.status + ' statusText: ' + response.statusText);
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
                    'tagid': $('#tagidnbr').val(),
                    'imgid': $('#imgidnbr').val(),
                    'descr': descrip,
                    'ajax': '1'       
                  };
     $.ajax({
        type: 'POST',
        url: 'index.php?gallery/updgroup',
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
  function validGroupPick()
  {
      var pikdata = { sid: -1,
                      str: ''};
      pikdata = rtnSelectedIdStr('frm');
      return (pikdata['sid'] >= 0);
  }
