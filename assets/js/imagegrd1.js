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
          show_Busy();
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
            //displayGroup();
            showEditForm($(this).text());
  })
  $('#frmiupd')
     .button({
            icons: {
                primary: 'ui-icon-circle-plus'
            },
         })
         .click(function() {
            //displayItem();
            showEditForm($(this).text());
  })
  $('#frmifnd')
     .button({
            icons: {
                primary: 'ui-icon-circle-zoomin'
            },
         })
         .click(function() {
            showFindForm($(this).text());
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
                    $("#imagview").append('<table border="1" cols="4" id="imgtbl" border="0" cellpadding="1" cellspacing="1">');
                    $("#imagview").append('<tr><td><input type="checkbox" name="cropimg" value="C" /> Crop </td></tr>');
                    $("#imagview").append('<tr><td><input type="checkbox" name="rsizimg" value="R" /> Resize </td></tr>');
                    $("#imagview").append('<tr><td>Rotate Image</td></tr>');
                    $("#imagview").append('<tr><td colspan="3"><select name="flipimg" style="width: 100px;">');
                    $("#imagview").append('<option value="90left">90 degrees left</option>');
                    $("#imagview").append('<option value="90rite">90 degrees right</option>');
                    $("#imagview").append('<option value="180flip">180 degrees flip</option>');
                    $("#imagview").append('</select><td/><tr>');
                    $("#imagview").append('<tr><td colspan="4"> <img src="'+ifile+'" id="pix" /></td></tr>');
                    $("#imagview").append('<tr><td><div id="subtitle"><label>'+lfile+'</label></div></td></tr>');
                    $("#imagview").append('</table>');
                    $("#imagview").append('<script language="Javascript">'+
                                                  '$("#pix").Jcrop({' +
                                                                 'aspectRatio: 0,' +
                                                                   'bgOpacity: .3,' +
                                                                     'bgColor: "lightblue",'+
                                                                    'onSelect: updateCoords});'+
                                           '</script>');
                  }
             },
           buttons: {
              'Save' : function() {
                 if(updImageSize())
                   console.log('updImageSize=true');
                 $( this ).wijdialog( 'close' );
              },
              'Close': function() {
                 $( this ).wijdialog( 'close' );
              }
           }
  });     
})
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
  function getImageChange()
  {
    var rtn = '';
    $("input[type='checkbox']:checked").each(function(){
        rtn = $(this).val();
    });
   return rtn;
  }  
  function updImageSize()
  {
    var rtn = false;
    var params = {
            ifile : getImageName(),  
            imgid : getImageId(),
            emode : getImageChange(),
            which : 'item',
            topx  : $('#x').val(),
            topy  : $('#y').val(),
            botx  : $('#x2').val(),
            boty  : $('#y2').val(),
            wide  : $('#imgwide').val(),
            hite  : $('#imghite').val()
        }
        result = '';
        $.ajax({
           type: 'POST',
           url: 'index.php?gallery/chgsize',
           data: params,
           cache:false,
           async: false,
           beforeSend: function(){
              console.log('chgsize-beforeSend');
           },
           success:
             function(data){
               //$('#lastarea').empty(); 
               //$('#lastarea').append(data);
               result = data;
               rtn = true;
               //getImageForm(getImageGroup(), getImageId(), getImageDescr());
           },
           complete: function (xhr, status) {
              if (status === 'error' || !xhr.responseText) {
                console.log('chgsize-complete status: Error');
                result = status+' ['+xhr.resposeText+']';
              }
           },
           error: function(response) {
              console.log('chgsize-ajax-error-status: '+response.status + ' statusText: ' + response.statusText);
              result = 'response ['+respons.status+']-['+response.statusText+']';
           }
        });
        $('#lastarea').empty(); 
        $('#lastarea').append(result); 
        return rtn;
  }
