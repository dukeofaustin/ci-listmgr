<script src="<?php echo base_url();?>assets/js/slideshow.js" type="text/javascript"></script>
   <div style="width: 750px; height: 650px; margin: auto;">	<div id="showcase" class="showcase">
       <?php foreach ($image_list as $image):?>
            <!-- Each child div in #showcase with the class .showcase-slide represents a slide. -->
            <div class="showcase-slide">
                <!-- Put the slide content in a div with the class .showcase-content. -->
                <div class="showcase-content">
                    <img src="<?php echo $image->picture;?>"/>
                </div>
                <!-- Put the thumbnail content in a div with the class .showcase-thumbnail -->
                <div class="showcase-thumbnail">
                    <img src="<?php echo $image->picture;?>" width="140px" />
                    <!-- The div below with the class .showcase-thumbnail-caption contains the thumbnail caption. -->
                    <div class="showcase-thumbnail-caption"><?php echo $image->fname;?></div>
                    <!-- The div below with the class .showcase-thumbnail-cover is used for the thumbnails active state. -->
                    <div class="showcase-thumbnail-cover"></div>
                </div>
                <!-- Put the caption content in a div with the class .showcase-caption -->
                <div class="showcase-caption">
                    <h2><?php echo $image->pdescr;?></h2>
                </div>
            </div>
       <?php endforeach; ?>
    </div>
  </div>
</div>