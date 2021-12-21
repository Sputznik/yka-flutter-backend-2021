<?php

  $value = isset( $atts['value'] ) ? $atts['value'] : "";

  if( !$value && isset( $atts['default'] ) && $atts['default'] ){
    $value = $atts['default'];
  }

?>
<input type="text"
       placeholder="<?php _e( $atts['placeholder'] ? $atts['placeholder'] : '' );?>"
       id = "<?php _e( $atts['id'] );?>"
       name="<?php _e( $atts['name'] );?>"
       value="<?php _e( $value );?>"
       class="regular-text"/>
