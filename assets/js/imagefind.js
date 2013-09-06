$(document).ready(function () {
    $(':input[type="text"]').wijtextbox();
    $('#searchstr').focus();
    $('#searchstr').keypress(function(e) {
       code = e.keyCode ? e.keyCode : e.which;
       if(code.toString() == 13) {
         searchImage();
       }
    });      
    $('#findbtn')
       .button({
              icons: {
                  primary: 'ui-icon-search'
              },
           })
           .click(function() {
             searchImage();
    });
    $('#nonebtn')
       .button({
              icons: {
                  primary: 'ui-icon-circle-close'
              },
           })
           .click(function() {
  	          $('#gridview').empty();
              window.open('index.php?gallery/images','_self');		
            
     });
     $('tr:odd').css('background', '#b0ffd8');
})
  function searchImage()
  {
      var descrip = cleanItemString($('#searchstr').val());
      var form_data = {
        'descr': descrip,
        'ajax': '1'       
      };    
      $.ajax({
        type: 'POST',
        url: 'index.php?gallery/itemfind',
        data: form_data,
        cache:false,
        async: false,
        success:
          function(data){
  	        result = 'ok'
	        $('#gridview').empty();
            $('#gridview').html(data);
            console.log(data)
        },
        complete: function (xhr, status) {
	    if (status === 'error' || !xhr.responseText) {
	       result = 'searchImage-Complete status: Error';
	    } else {
 	       //result = 'searchImage-Complete responseText: '+xhr.responseText;
        }
        },
        error: function(response) {
 	       result = 'searchImage-error-status: '+response.status + ' statusText: ' + response.statusText;
        }
      });
      return false;
  }
