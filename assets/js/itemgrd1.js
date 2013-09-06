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
})