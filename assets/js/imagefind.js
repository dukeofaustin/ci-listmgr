$(document).ready(function () {
    $(':input[type="text"]').wijtextbox();
    $('#searchstr').focus();
    $('#searchstr').keypress(function(e) {
       code = e.keyCode ? e.keyCode : e.which;
       if(code.toString() == 13) {
         searchItem();
       }
    });      
    $('#findbtn')
       .button({
              icons: {
                  primary: 'ui-icon-search'
              },
           })
           .click(function() {
             searchItem();
    });
    $('#nonebtn')
       .button({
              icons: {
                  primary: 'ui-icon-circle-close'
              },
           })
           .click(function() {
  	          $('#gridview').empty();
              window.open('index.php?lists/itemgrid','_self');		
            
     });
     $('tr:odd').css('background', '#b0ffd8');
})
  function searchItem()
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
	       result = 'searchItem-Complete status: Error';
	    } else {
 	       //result = 'searchItem-Complete responseText: '+xhr.responseText;
        }
        },
        error: function(response) {
 	       result = 'searchItem-error-status: '+response.status + ' statusText: ' + response.statusText;
        }
      });
      return false;
  }
    /* showFindForm is used to look up tbl_lstitem descriptions */
  function showFindForm(which)
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
               console.log('showFindForm-Complete status: Error');
            }
        },
        error: function(response) {
            console.log('showFindForm-error-status: '+response.status + ' statusText: ' + response.statusText);
        }
      });
      return false;
  }

