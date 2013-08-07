<script type="text/javascript">
 
$(document).ready(function()
{
	$("#showcase").awShowcase(
	{
		content_width:			700,
		content_height:			650,
		fit_to_parent:			false,
		auto:				    false,
		interval:			    3000,
		continuous:			    false,
		loading:			    true,
		tooltip_width:			200,
		tooltip_icon_width:		32,
		tooltip_icon_height:	32,
		tooltip_offsetx:		18,
		tooltip_offsety:		0,
		arrows:				    true,
		buttons:			    true,
		btn_numbers:			true,
		keybord_keys:			true,
		mousetrace:			    false, /* Trace x and y coordinates for the mouse */
		pauseonover:			true,
		stoponclick:			true,
		transition:			    'vslide', /* hslide/vslide/fade */
		transition_delay:		300,
		transition_speed:		500,
		show_caption:			'show', /* onload/onhover/show */
		thumbnails:			     true,
		thumbnails_position:	'outside-last', /* outside-last/outside-first/inside-last/inside-first */
		thumbnails_direction:	'vertical', /* vertical/horizontal */
		thumbnails_slidex:		0, /* 0 = auto / 1 = slide one thumbnail / 2 = slide two thumbnails / etc. */
		dynamic_height:			false, /* For dynamic height to work in webkit you need to set the width and height of images in the source. Usually works to only set the dimension of the first slide in the showcase. */
		speed_change:			true, /* Set to true to prevent users from swithing more then one slide at once. */
		viewline:			    false /* If set to true content_width, thumbnails, transition and dynamic_height will be disabled. As for dynamic height you need to set the width and height of images in the source. */
	})
});

</script>
<div style="width: 845px; height: 850px; margin: auto;">
	<div id="showcase" class="showcase">
                  
		<!-- Each child div in #showcase with the class .showcase-slide represents a slide. -->
		<div class="showcase-slide">
			<!-- Put the slide content in a div with the class .showcase-content. -->
			<div class="showcase-content">
				<img src="<?php echo $ipath;?>AnArtistsJourney.jpg" alt="01" />
			</div>
			<!-- Put the thumbnail content in a div with the class .showcase-thumbnail -->
			<div class="showcase-thumbnail">
				<img src="<?php echo $ipath;?>AnArtistsJourney.jpg" alt="01" width="140px" />
				<!-- The div below with the class .showcase-thumbnail-caption contains the thumbnail caption. -->
				<div class="showcase-thumbnail-caption">AnArtistsJourney-Caption</div>
				<!-- The div below with the class .showcase-thumbnail-cover is used for the thumbnails active state. -->
				<div class="showcase-thumbnail-cover"></div>
			</div>
			<!-- Put the caption content in a div with the class .showcase-caption -->
			<div class="showcase-caption">
				<h2>Be creative. Get Noticed!</h2>
			</div>
		</div>
		<div class="showcase-slide">
			<div class="showcase-content">
				<img src="<?php echo $ipath;?>MyLittleBrother.jpg" alt="02" />
			</div>
			<div class="showcase-thumbnail">
				<img src="<?php echo $ipath;?>MyLittleBrother.jpg" alt="02" width="140px" />
			</div>
			<!-- Put the caption content in a div with the class .showcase-caption -->
			<div class="showcase-caption">
				<h2>Groove meister!</h2>
			</div>
		</div>
		<div class="showcase-slide">
			<div class="showcase-content">
				<img src="<?php echo $ipath;?>CheckingForTeeth.jpg" alt="03" />
			</div>
			<div class="showcase-thumbnail">
				<img src="<?php echo $ipath;?>CheckingForTeeth.jpg" alt="03" width="140px" />
				<div class="showcase-thumbnail-caption">CheckingForTeeth-Caption</div>
			</div>
			<!-- Put the caption content in a div with the class .showcase-caption -->
			<div class="showcase-caption">
				<h2>It is holiday time</h2>
			</div>
		</div>
		<div class="showcase-slide">
			<div class="showcase-content">
				<img src="<?php echo $ipath;?>BabySmile.jpg" alt="04" />
			</div>
			<div class="showcase-thumbnail">
				<img src="<?php echo $ipath;?>BabySmile.jpg" alt="04" width="140px" />
				<div class="showcase-thumbnail-caption">BabySmile-Caption</div>
				<div class="showcase-thumbnail-cover"></div>
			</div>
			<!-- Put the caption content in a div with the class .showcase-caption -->
			<div class="showcase-caption">
				<h2>The fields as running with it.</h2>
			</div>
		</div>
		<div class="showcase-slide">
			<div class="showcase-content">
				<img src="<?php echo $ipath;?>WhiteSuitSitting.jpg" alt="05" />
			</div>
			<div class="showcase-thumbnail">
				<div class="showcase-thumbnail-content">WhiteSuitSitting-Content<br/> I'm not <b>bold</b></div>
				<div class="showcase-thumbnail-cover"></div>
			</div>
			<!-- Put the caption content in a div with the class .showcase-caption -->
			<div class="showcase-caption">
				<h2>Last caption before WhiteSuitHead-content</h2>
			</div>
		</div>
		<div class="showcase-slide">
			<div class="showcase-content">
				<img src="<?php echo $ipath;?>WhiteSuitHead.jpg" alt="06" />
			</div>
			<div class="showcase-thumbnail">
				<div class="showcase-thumbnail-content">WhiteSuitHead-Content</div>
				<div class="showcase-thumbnail-cover"></div>
			</div>
		</div>
		<div class="showcase-slide">
			<div class="showcase-content">
				<img src="<?php echo $ipath;?>UncleBobAndTyler.jpg" alt="07" />
			</div>
			<div class="showcase-thumbnail">
				<img src="<?php echo $ipath;?>UncleBobAndTyler.jpg" alt="07" width="140px" />
				<div class="showcase-thumbnail-caption">UncleBobAndTyler-Caption</div>
				<div class="showcase-thumbnail-cover"></div>
			</div>
		</div>
		<div class="showcase-slide">
			<div class="showcase-content">
				<img src="<?php echo $ipath;?>MelissasWedding.jpg" alt="08" />
			</div>
			<div class="showcase-thumbnail">
				<img src="<?php echo $ipath;?>MelissasWedding.jpg" alt="08" width="140px" />
				<div class="showcase-thumbnail-caption">Melissa and Casey's Wedding, August 2012 in Cedar Park</div>
			</div>
		</div>
	</div>

</div>
