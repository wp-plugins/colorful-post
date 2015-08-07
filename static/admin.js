(function($) {
  /**
   * bind the button "Pick Color"
   *
   */
  $(function() {
    var $buttonPickColorWrap = $('#container-nnColorfulPosts-set-post-title-color');
    if ( !$buttonPickColorWrap.size() ) {
      return false;
    }


    var $buttonPickColor = $('#button-nnColorfulPosts-pick-color');


    // init
    $buttonPickColor.wpColorPicker({
      defaultColor: $buttonPickColor.attr('data-default-color')
    });


    // set the button text
    var $buttonColorResult = $buttonPickColorWrap.find('.wp-color-result');
    $buttonColorResult.attr('title', 'Set Post Title Color');
  });



  /**
   * for settings page at backend for this plugin
   *
   */
  $(function() {
    var $container = $('#container-nnColorfulPosts-options');
    if ( !$container.size() ) {
      return false;
    }


    // bind the button "Reset all post title color to default" click event
    var $buttonResetAllPostTitleColor = $('#button-nnColorfulPosts-reset-all-post-title-color');
    $buttonResetAllPostTitleColor.on('click', function() {
      // confirm
      if ( !window.confirm('Are you sure you want to reset all your post title color settings to their original state?') ) {
        return false;
      }

      // submit the form
      $('#form-nnColorfulPosts-reset-all-post-title-color').submit();
    });
  });
})(jQuery);