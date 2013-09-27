/*
 * Site-wide javascript scripts
 * Wesley McGehee
 * 20130529
 */
  function inlineDebug(o)
  {
    console.log('just keep stepping into the unknown...');
    debugger;
  }
  function isString(o)
  {
    //ref: http://www.webcosmoforums.com/javascript-ajax/25689-how-check-if-string-null-empty-javascript.html
    //if(var) -- This would make javascript make an implicit conversion to boolean for the variable
    if(o && o.val != '' && typeof o != 'undefined') {
        return true;
    } else {
        return false;
    }
  }
  function updateTips(t) {
     tips.text(t).addClass('ui-state-highlight');
     setTimeout(function() {
        tips.removeClass('ui-state-highlight', 1500 );
    }, 500 );
  }
  function checkLength( o, n, min, max ) {
    if(isString(o)) {
        if(o.val().length > max || o.val().length < min) {
           o.addClass('ui-state-error');
           updateTips('Length of ' + n + ' must be between ' + min + ' and ' + max + '.');
           return false;
        } else {
           return true;
        }
    } else { return false; }
  }
  function checkRegexp(o, regexp, n) {
    if (!( regexp.test( o.val()))) {
      o.addClass('ui-state-error');
      updateTips( n );
      return false;
    } else {
      return true;
    }
  }
  function rtnSelectedIdStr(which)
  {
      var rtn = { sid: 0,
                  str: ''};
      var id = 0;
      var str = "";
      $("#" + which + "-dropdown option:selected").each(function () {
           str = $(this).text();
           id = $(this).val();
      });
      if(str != "")
      {
        $("#" + which + "-descr").show();
        $("input[type='text']").each(function(){
           if($(this).attr('name') == which + '-descr'){
               $(this).val(str);
           }
        });
      }
      rtn = {  sid: id,
               str: str   };
      return rtn;
  }
  function setDropdownSelection(which,txt)
  {
    
    $('#'+which + ' option:contains('+txt+')').attr('selected', 'selected');
    console.log('setDropdownSelection-val('+$('#'+which).val()+')');
  }
  
function cleanItemString (paramstr)
{        
    "use strict";
    var rtn;
    rtn = paramstr;
    if(typeof rtn != 'undefined' && rtn.length > 0) {
        // convert single or double quote with back-tick
        if(rtn.indexOf("'") > -1) {
           rtn = rtn.replace(/\'/g, "`"); }
        if(rtn.indexOf('"') > -1) {
           rtn = rtn.replace(/\"/g, "`"); }
        if(rtn.indexOf('*') > -1) {
           rtn = rtn.replace(/\*/g, "`"); }
        if(rtn.indexOf('<') > -1) {
           rtn = rtn.replace(/\</g, "("); }
        if(rtn.indexOf('>') > -1) {
           rtn = rtn.replace(/\>/g, ")"); }
    }
    return rtn;
}

