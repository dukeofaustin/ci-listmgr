//<script type="text/javascript">
$(function() {
    $(":input[type='radio']").wijradio();
    $(":input[type='text']").wijtextbox();
    //$(":input[type='checkbox']").wijcheckbox();
    $( "#dlg:ui-dialog" ).wijdialog( "destroy" );
    $( "#dlg-edituser" ).hide();
    $( "#dlg-forgot" ).hide();
    $( "#dlg-login" ).wijdialog({position: [400, 300],
        autoOpen: true,
        captionButtons: {
            pin: {visible: false },
            refresh: {visible: true },
            toggle: {visible: false },
            minimize: {visible: false },
            maximize: {visible: false },
            close: {visible: true }
        },       
        modal: true,
        title: 'Login Screen',
        resizable: false,
        height: 325,
        width: 400,
        zIndex: 0,
        open: function() { $('.user-area').css('box-shadow','inset -5px -5px 5px #888');
                            clearTextBox('uname');
                            clearTextBox('email');
                         },
        buttons: {
            "Login": function() {
                if(loginUser()){
					//alert('window.location call');
                    window.location = 'http://mangumreunion.com/listmgr/cdig/';
					$( this ).wijdialog( "close" );
                } else {
                   updateTips('Invalid login');
                   clearTextBox('uname');
                   clearTextBox('email');
                }
            },
            "Register": function() {
                showUserDialog();
                $( this ).wijdialog( "close" );
            },
            Cancel: function() {
                $( this ).wijdialog( "close" );
            }
        }
    });
    $( "#dlg-edituser" ).wijdialog({
        autoOpen: true,
        height: 550,
        width: 450,
        modal: true,
        zIndex: 0,
        //contentUrl: 'http://www.mangumreunion.com/listmgr/cdig',
        //expandingAnimation: { effect: "puff", duration: 300, easing: "easeOutExpo" },
        open: function() { $('.user-area').css('box-shadow','inset -5px -5px 5px #888');
        },
        buttons: {
            "Save": function() {
                bValid = validateUserData();		    
                if ( bValid ) {
                    updUserData('upd');
                    $( this ).wijdialog( "close" );
                }
            },
            Cancel: function() {
                $( this ).wijdialog( "close" );
            }
        },
        close: function() {
            allFlds.val( "" ).removeClass( "ui-state-error" );
        }
    });
    var fname = $( "#fname" ),
        lname = $( "#lname" ),
        uname = $( "#uname" ),
        email = $( "#email" ),
        pword = $( "#pword" ),
        userid = $( "#userid" )
        allFlds = $( [] ).add( fname ).add( lname ).add( uname ).add( email ).add( pword ),
        tips = $( ".validateTips" );

    function updUserData(mode)
    {
        if(typeof mode == 'undefined' || mode === ''){
           mode = 'upd';
        }
        var params = {
           emode: mode,
           fname: $('#fname').val(),
           lname: $('#lname').val(),
           uname: $('#uname').val(),
           email: $('#email').val(),
           pword: $('#pword').val()
        };
        $.ajax({
        type: 'POST',
        url: 'index.php?login/upduser',
        dataType: 'json',
        data: params,
        cache:false,
        async: false,
        success:
          function(data){
            //console.log( 'Ajax-no errors' );
            //console.log('data.message='+data);
            alert(data);
            $('#user-area').append(data); 
        },
        beforeSend: function(){
            //console.log( 'Ajax-beforeSend' );
        },
        complete: function (xhr, status) {
            if (status === 'error' || !xhr.responseText) {
               //console.log('Complete-status=error');
            } else {
              var data = xhr.responseText;
              $('#user-area').html(data).append;
              //console.log( 'Ajax-complete-xhr.resonseText='.data);
            }
                $( "#dlg-edituser" ).wijdialog( "close" );		
            },
        error: function(response) {
            //console.log('Ajax-error: '+response.status + ' ' + response.statusText);
        }
        });
    }
    var uname = $( "#uname" ),
        email = $( "#email" ),
        allFlds = $( [] ).add( uname ).add( email ).add( pword ),
        tips = $( ".validateTips" );
	function loginUser()
	{
      var rtn = false;
	  var params = {
          uname: $('#uname').val(),
          email: $('#email').val()
      };
      $.ajax({
		type: 'POST',
		url: 'index.php?login/signon',
		data: params,
		cache: false,
		async: false,
        dataType: 'json',
		success:
		  function(data){
		    //console.log( 'Ajax-no errors' );
            var pname = data.uname;
            if (typeof pname !== 'undefined') {
                if(pname.toLowerCase().indexOf(params['uname'].toLowerCase()) != -1){
                   rtn = true;
                }
            }
		},
		beforeSend: function(){
		    //console.log( 'Ajax-beforeSend' );
		},
		complete: function (xhr, status) {
		    if (status === 'error' || !xhr.responseText) {
		       //console.log('Complete-status=error');
		    }
        },
		error: function(response) {
		    //console.log('Ajax-error: '+response.status + ' statusText: ' + response.statusText);
		}
      });
      return rtn;
	}

    function validateUserData() {

        var bValid = false;

        var fname = $('#fname'),
            lname = $('#lname'),
            uname = $('#uname'),
            email = $('#email'),
            pword = $('#pword');
        bValid = checkLength( fname, "fname", 3, 25 );
        bValid = bValid && checkLength( lname, "lname", 3, 25 );
        bValid = bValid && checkLength( uname, "uname", 3, 16 );
        bValid = bValid && checkLength( email, "email", 6, 80 );
        bValid = bValid && checkLength( pword, "pword", 5, 16 );
        bValid = bValid && checkRegexp( fname, /^[a-z]([a-z_])+$/i, "First name may only be letters." );
        bValid = bValid && checkRegexp( lname, /^[a-z]([a-z_])+$/i, "Last name may only be letters." );
        bValid = bValid && checkRegexp( uname, /^[a-z]([0-9a-z_])+$/i, "Username may consist of a-z, 0-9, underscores, begin with a letter." );
        // From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
        bValid = bValid && checkRegexp( email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. ui@jquery.com" );
        bValid = bValid && checkRegexp( pword, /^([0-9a-zA-Z])+$/, "Password field only allows : a-z 0-9" );
        if(bValid){
           return true;    
        }
        return false;
    }
    function showUserDialog(){
        $( "#dlg-edituser" ).wijdialog( "open" );
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

    function setTextBoxVal(which, txt)
    {
       var str = txt.val();
       $('#' + which).show();
       $('input[type="text"]').each(function(){
           if($(this).attr('name') == which){
              $(this).val(str);
           }
       });
    }
    
    function updateTips( t ) {
        tips.text( t ).addClass( "ui-state-highlight" );
        setTimeout(function() {
            tips.removeClass( "ui-state-highlight", 1500 );
        }, 500 );
    }

    function checkLength( o, n, min, max ) {
        if ( o.val().length > max || o.val().length < min ) {
            o.addClass( "ui-state-error" );
            updateTips( "Length of " + n + " must be between " +
                min + " and " + max + "." );
            return false;
        } else {
            return true;
        }
    }

    function checkRegexp( o, regexp, n ) {
        if ( !( regexp.test( o.val() ) ) ) {
            o.addClass( "ui-state-error" );
            updateTips( n );
            return false;
        } else {
            return true;
        }
    }

});
//</script>
