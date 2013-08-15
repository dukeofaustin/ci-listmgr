        <!-- Begin menu markup -->
        <ul id="flyoutmenu">
          <li><a href="#">Maintnance</a>
             <ul>
                 <li><a href="<?php echo base_url();?>index.php?lists/itemgrid">Edit Groups/Items</a>
                 <li><a href="<?php echo base_url();?>index.php?users">User Grid</a></li>
                 <li><a href="<?php echo base_url();?>index.php?upload">Upload Files</a></li>
            </ul>     
          </li>
          <li><a href="#">Applications</a>
            <ul>
               <li><a href="<?php echo base_url();?>index.php?lists">Groceries</a></li>
               <li><a href="<?php echo base_url();?>index.php?news">News Feed</a>
               <li><a href="#">Image Section</a>
                  <ul>
                    <li><a href="<?php echo base_url();?>index.php?gallery">Gallery</a></li>
                    <li><a href="<?php echo base_url();?>index.php?gallery/images">Image Grid</a></li>
                  </ul>
               </li>
            </ul>
          </li>
          <li><a href="#">Help</a>
               <ul>
                 <li><a href="<?php echo base_url();?>index.php?session">Dev-Session</a></li>
                 <li><a href="<?php echo base_url();?>index.php?login">Login Form</a></li>
                 <li><a href="#">Admin Section</a>
                    <ul>
                      <li><a href="<?php echo base_url();?>index.php?gallery/cleanup">Cleanup User Files</a></li>
                    </ul>
                 </li>
                 <li><a href="<?php echo base_url();?>index.php?about">About</a></li>
               </ul>
          </li>
        </ul> <!--flyoutmenu-->
