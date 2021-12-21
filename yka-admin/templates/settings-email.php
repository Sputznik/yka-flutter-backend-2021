<?php
  if( isset( $_POST['submit'] ) && isset( $_POST['yka_settings'] ) ){
    update_option( 'yka_settings', $_POST['yka_settings'] );
    $this->displayUpdateNotice();
  }

  $settings = $this->getSettings();

  $fields = array(
    'email_to_address' => array(
      'label'       => 'Email To Address',
      'placeholder' => 'Email To Address'
    ),
    'email_subject' => array(
      'label'       => 'Subject',
      'placeholder' => 'Notification from $site_name',
      'desc'        => '<strong>Available placeholder: </strong><code>$site_name</code>'
    ),
    'email_body' => array(
      'type'        => 'textarea',
      'label'       => 'Email Body',
      'placeholder' => 'Email Body',
      'desc'        => '<strong>Available placeholders:</strong><br/><code>$post_type, $post_title, $post_author, $reason, $current_user ( User who has reported the post )</code>'
    ),
    'rest_response' => array(
      'type'        => 'textarea',
      'label'       => 'Rest Api Response',
      'placeholder' => 'Rest Api Response',
    )
  );
?>
<h2>Report a post</h2>
<form method="post">
  <table class="form-table" role="presentation">
    <tbody>
      <?php foreach( $fields as $slug => $field ):?>
        <tr>
          <th scope="row">
            <label for="<?php _e( $slug );?>"><?php _e( $field['label'] );?></label></th>
            <td>
              <?php
                $form_field_atts = array(
                  'id'          => $slug,
                  'name'        => "yka_settings[emails][report][$slug]",
                  'type' 				=> isset( $field['type'] ) && $field['type'] ? $field['type'] : 'text',
                  'value'       => isset( $settings['emails']['report'][$slug] ) ? $settings['emails']['report'][$slug] : '',
                  'placeholder' => $field['placeholder']
                );
                $this->formField( $form_field_atts );
              ?>
              <?php if( isset( $field['desc'] ) && $field['desc'] ):?>
                <p class="description"><?php _e( $field['desc'] );?></p>
              <?php endif;?>
            </td>
        </tr>
      <?php endforeach;?>
    </tbody>
  </table>
  <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p></form>
</form>
