<script src="<?php echo base_url();?>assets/js/imagegrid.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/js/imagegrd1.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/jcrop/jquery.Jcrop.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>style/jcrop/jquery.Jcrop.css" type="text/css" />

  <div id="leftbtncol" class="column">
     <div class="innertube">
        <button id="frmgupd">Groups</button>
        <button id="frmifnd">Search</button>
        <button id="refresh">Refresh</button>
     </div>
     <div class="innertube">
        <div id="gridview">
           <?php include('imagepage.php'); ?>
        </div>
     </div>
     <br />
     <div class="innertube">
       <div id="dialog-imag">
          <div id="imagview"></div>
             <input type="hidden" name="x" id="x" size="4"/>
             <input type="hidden" name="y" id="y" size="4"/>
             <input type="hidden" name="x2" id="x2" size="4"/>
             <input type="hidden" name="y2" id="y2" size="4"/>
             <input type="hidden" name="w" id="w" size="4" />
             <input type="hidden" name="h" id="h" size="4"/>
          <div id="shodata"></div>
       </div>
     </div>
   </div>      
   <div id="rightgrdcol" class="column">
     <div class="innertube">
        <div id="formarea">
        </div>
     </div>
     <div class="innertube">
        <div id="lastarea">
        </div>
     </div>
   </div>
</div>
