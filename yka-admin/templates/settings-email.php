<?php
  if( isset( $_POST['submit'] ) && isset( $_POST['yka_settings'] ) && !empty( $_POST['yka_settings'] ) ){
    update_option( 'yka_settings', $_POST['yka_settings'] );
  }

  $settings = get_option( 'yka_settings' );
  $email_to_address = isset( $settings['email_to_address'] ) ? $settings['email_to_address'] : '';
?>
<form method="post">
  <table class="form-table" role="presentation">
    <tbody>
      <tr>
        <th scope="row">
          <label for="survey-slug">Email To Address</label></th>
          <td>
            <input type="text" class="regular-text" id="survey-slug" name="yka_settings[email_to_address]" value="<?php echo $email_to_address; ?>" placeholder="Email">
          </td>
      </tr>
    </tbody>
  </table>
  <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p></form>
</form>
