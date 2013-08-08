
   <h3>Your files were successfully uploaded!</h3>
   <div id="leftbtncol" class="column">
     <div class="innertube">
         <?php foreach ($upload_data as $item):?>
              <?php if(is_array($item)) {
                  foreach ($item as $i => $v):?>
                     <p><?php echo $i;?>: <?php echo $v;?></p>
                  <?php endforeach; ?>
              <?php  } else {; ?>
                 <p><?php echo $item;?>: <?php echo $value;?></p>
              <?php } ?>
         <?php endforeach; ?>
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
</div>


<p><?php echo anchor('index.php?upload', 'Upload Another File!'); ?></p>
