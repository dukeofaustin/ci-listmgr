$(document).ready(function () {
  $(':input[type="text"]').wijtextbox();
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
            height: 625,
            width: 525,
            modal: true,
            open: function() { $('.ui-dialog').css('box-shadow','inset -5px -5px 5px #888');
                  var ifile = $('#filename').val();
                  var lfile = ifile.substr(ifile.lastIndexOf('/')+1);
                  console.log('ifile='+ifile);
                  if(ifile != 'undefined' && ifile.length > 3){
                    $("#imagview").empty();
                    $("#imagview").append('<img src="'+ifile+'" alt="image?" />');
                    $("#imagview").append('<div id="subtitle"><label>'+lfile+'</label></div>');
                  }
             },
           buttons: {  
              'Close': function() {
                 $( this ).wijdialog( 'close' );
              }
           }
  });     
})