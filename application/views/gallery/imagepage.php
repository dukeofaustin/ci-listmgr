<div class="ui-widget">
    <div id="subtitle">
      <?php echo '<h1>'.$listtype.'</h1>' ?>
    </div>
    <p><?php echo $this->table->generate(); ?></p>
    <br/>
    <p><?php echo $this->pagination->create_links(); ?></p>
</div>

<script type="text/javascript" charset="utf-8">
  $('tr:odd').css('background', '#b0ffd8');
</script>
