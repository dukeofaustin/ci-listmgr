        <script type="text/javascript">
           $(document).ready(function () {
               $('#getfile')
                    .button({
                           icons: {
                           text: false,
                               primary: 'ui-icon-circle-plus'
                           },
                        })
                    .click(function() {
                         makeFileList();
                         
               })
               $('#doupload')
                    .button({
                           icons: {
                           text: false,
                               primary: 'ui-icon-gear'
                           },
                        })
                    .click(function() {
                         //makeFileList();
                         
               })
           })
           function makeFileList() {
                  var input = document.getElementById("getfile");
                  var ul = document.getElementById("fileList");
                  while (ul.hasChildNodes()) {
                      ul.removeChild(ul.firstChild);
                  }
                  for (var i = 0; i < input.files.length; i++) {
                      var li = document.createElement("li");
                      li.innerHTML = input.files[i].name;
                      ul.appendChild(li);
                  }
                  if(!ul.hasChildNodes()) {
                      var li = document.createElement("li");
                      li.innerHTML = 'No Files Selected';
                      ul.appendChild(li);
                  }
         }
         </script>        
        <!-- IMPORTANT:  FORM's enctype must be "multipart/form-data" -->
        <form method="post" action="index.php?upload/imagefile" enctype="multipart/form-data">
            <input name="Filedata[]" id="getfile" type="file" multiple="" onChange="makeFileList();" />
            <input type="submit" id="doupload" value="Upload" />
        </form>
        <br /><br />
     <div id="leftbtncol" class="column">
     <div class="innertube">
       <p>
 	      <strong>Files You Selected:</strong>
	   </p>
	    <div id="fileList">...no files selected for upload</div>
        <div id="gridview">
        </div>
     </div>
   </div>      
   <div id="rightgrdcol" class="column">
     <div class="innertube">
        <div id="formarea">
           <p>form-column</p>
        </div>
     </div>
   </div>
</div>
