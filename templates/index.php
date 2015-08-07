<div class="wrap">
  <h2><?php echo $this -> pluginName ?> Options</h2>

  <?php
    echo settings_errors('nnColorfulPostsSettingsUpdateSuccess');
  ?>

  <div id="container-nnColorfulPosts-options">
    <?php
    $suffix = 'nnColorfulPosts-reset-all-post-title-color';
    ?>
    <form action="<?php echo $this -> settingsUrl; ?>" method="post"
          id="form-<?php echo $suffix; ?>">
      <input type="hidden" name="action" value="<?php echo $suffix; ?>" />

      <input type="submit" class="button button-secondary" value="Reset all post title color to default"
             id="button-<?php echo $suffix; ?>" />

      <div class="notice-message">
        Clicking this button will reset all your post title color settings to their original state.
        Please note this operation is irreversible.
      </div>
    </form>

  </div><!-- /container-nnColorfulPosts-options -->
</div>