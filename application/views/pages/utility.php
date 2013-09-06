   <div id="leftbtncol" class="column">
     <div class="innertube">
       <?php if(isset($result)) {
              foreach ($result as $item):?>
               <p><?php echo $item;?></p>
             <?php endforeach; ?>
             <?php } else { ?>
               <p>Nothing to clean up</p>
               
             <?php } ?>
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
        </div>
     </div>
   </div>
