$(document).ready(function () {
  $(':input[type="text"]').wijtextbox();
  $(':input[type="checkbox"]').wijcheckbox();
  $('.pagination-digg > li a').live('click', function(eve){
      eve.preventDefault();
      var result = 'paginate';
      var link = $(this).attr('href');
      $.ajax({
       url: link,
       type: "GET",
       dataType: "html",
       beforeSend: function(){
          show_Busy('gridview');
       },   
       success: function(html) {
          update_Page(html);
        },
        complete: function (xhr, status) {
          if (status === 'error' || !xhr.responseText) {
             //result = 'paginate-Complete-status=error';
          }
        },
        error: function(response) {
          result = 'paginate-Ajax-error: '+response.status + ' ' + response.statusText;
      }
    });
    console.log(result);
    return false;
  });
  $('#frmgupd')
     .button({
            icons: {
                primary: 'ui-icon-pencil'
            },
         })
         .click(function() {
            showImgEditForm($(this).text());
  })
  $('#frmifnd')
     .button({
            icons: {
                primary: 'ui-icon-circle-zoomin'
            },
         })
         .click(function() {
            showImgFindForm($(this).text());
  })
  $('#refresh')
	 .button({
            icons: {
	        text: false,
                primary: 'ui-icon-arrowrefresh-1-w'
            },
         })
	 .click(function() {
            window.location.reload(true);		
		  
  })
  $('#dialog-imag').wijdialog({
            autoOpen: false,
            height: 725,
            width: 725,
            modal: true,
            open: function() { $('.ui-dialog').css('box-shadow','inset -5px -5px 5px #888');
                  var ifile = getImageName(); // $('#filename').val();
                  var lfile = ifile.substr(ifile.lastIndexOf('/')+1);
                  console.log('ifile='+ifile);
                  if(ifile != 'undefined' && ifile.length > 3){
                    $("#imagview").empty();
                    $('#imagview').append(formatImageDlg(ifile, lfile));
                  }
             },
            buttons: {
              'Save' : function() {
                 if(modifyImage())
                   console.log('modifyImage=true');
                 $( this ).wijdialog( 'close' );
              },
              'Close': function() {
                 $( this ).wijdialog( 'close' );
              }
           }
  });     
})
  function enableImageCrop()
  {
     var rtn = '<script language="Javascript">'+
                  '$("#pix").Jcrop({aspectRatio: 0,' +
                                   'bgOpacity: .3,' +
                                   'bgColor: "lightblue",'+ 
                                   'onSelect: updateCoords});' +
                '</script>';
     return rtn;            
  }
  function formatImageDlg(imgfile, lblfile)
  {
    //get radio button (Resize/Crop/Flip)
    var tag = $('input:radio[name=radiobtn2]:checked').val()
    var tbl = '<div id="imgtbl"><table cols="1" border="0" cellpadding="1" cellspacing="1">';
    if(tag == 'F') {
        tbl = tbl + '<tr><td><label>Rotate Image</>&nbsp;&nbsp;';
        tbl = tbl + '<select name="img-dropdown" id="img-dropdown" style="width: 200px;" class="wijmo-wijdropdown">';
        tbl = tbl + '<option value="X">No change</option>';
        tbl = tbl + '<option value="L">Flip 90 degrees left</option>';
        tbl = tbl + '<option value="R">Flip 90 degrees right</option>';
        tbl = tbl + '<option value="V">Flip 180 degrees</option>';
        tbl = tbl + '</select></td></tr>';
        tbl = tbl + '<tr><td></td></tr>';
     }
     tbl = tbl + '<tr><td> <img src="'+imgfile+'" id="pix" /></td></tr>';
     tbl = tbl + '<tr><td><div id="subtitle"><label>'+lblfile+'</label></div></td></tr>';
     tbl = tbl + '</table></div>';
     if(tag == 'C'){
         tbl = tbl + enableImageCrop();
     }
    return tbl;
  }
        
  function updateCoords(c)
  {
      $('#x').val(c.x);
      $('#y').val(c.y);
      $('#x2').val(c.x2);
      $('#y2').val(c.y2);
      //$('#w').val(c.w);
      //$('#h').val(c.h);
  };
  function getImageId()
  {
    return $('#imgidnbr').val();
  }
  function getImageGroup()
  {
    return $('#tagidnbr').val();
  }
  function getImageName()
  {
    return $('#filename').val();
  }
  function getImageDescr()
  {
    return $('#descr').val();
  }
  function getImgEditMode()
  {
    var rtn = $('input:radio[name=radiobtn2]:checked').val()
    if(rtn == 'F') {
       pikobj = rtnSelectedIdStr('img');
       rtn = pikobj['sid'];
       if(!isString(rtn)){
          rtn = 'X';
       }
    } else if(rtn !== 'C') {
        rtn = 'X';
    }
    return rtn;
  }  
  function modifyImage()
  {
    var rtn = false;
    var mode = getImgEditMode();
    if(mode.length == 0)
      alert('mode is not set!');
    var params = {
            ifile : getImageName(),  
            imgid : getImageId(),
            emode : mode,
            which : 'item',
            topx  : $('#x').val(),
            topy  : $('#y').val(),
            botx  : $('#x2').val(),
            boty  : $('#y2').val(),
            wide  : $('#imgwide').val(),
            hite  : $('#imghite').val()
        }
        result = 'pre-modimage-call';
        $.ajax({
           type: 'POST',
           url: 'index.php?gallery/modimage',
           data: params,
           cache:false,
           async: false,
           beforeSend: function(){
              //console.log('modimage-beforeSend');
           },
           success:
             function(data){
               result = data;
               console.log('modimage-data='+data);
               rtn = true;
           },
           complete: function (xhr, status) {
              if (status === 'error' || !xhr.responseText) {
                console.log('modimage-complete status: Error');
                result = status;
              } else {
                result = 'modimage-ajax-complete';
              }
           },
           error: function(response) {
              console.log('modimage-ajax-error-status: '+response.status + ' statusText: ' + response.statusText);
              result = 'response ['+respons.status+']-['+response.statusText+']';
           }
        });
        $('#formarea').empty(); 
        $('#lastarea').append(result); 
        return rtn;
  }
