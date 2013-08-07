<script src="<?php echo base_url();?>assets/js/itemgrid.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/js/itemgrd1.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/js/itemfind.js" type="text/javascript"></script>
   <div id="leftbtncol" class="column">
     <div class="innertube">
        <button id="frmgupd">Group</button>
        <button id="frmiupd">Item</button>
        <button id="frmifnd">Search</button>
        <button id="refresh">Refresh</button>
     </div>
     <div class="innertube">
        <div id="formarea">
           <p>form-column</p>
        </div>
     </div>
   </div>      
   <div id="rightgrdcol" class="column">
     <div class="innertube">
        <div id="gridview">
           <?php include('itempage.php'); ?>
        </div>
     </div>
   </div>
</div>
