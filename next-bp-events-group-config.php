<?php
// parte de configuracao e grupo do bp //


/* Custom Group Extension for Buddypress   */

/* Add the form and save the meta - $next_bp_events_group_custom_group_field */ 
// load conference configs is user is admin
if( is_super_admin() ){
  add_filter( 'groups_custom_group_fields_editable', 'next_bp_events_group_custom_group_field' ); // This adds the form to the group admin details page and the first page of the group creation steps.
  add_action( 'groups_group_details_edited', 'next_bp_events_group_custom_group_field_save' ); // This saves the meta when editing the meta on the group edit details page.
  add_action( 'groups_created_group',  'next_bp_events_group_custom_group_field_save' ); // This save the meta durring the group creation process.
}
/* Retrieve the meta specific to each group - $bp_next_ocs_group_field_1 fetches the meta_value in the bp_next_pack_group-field-1 meta_key attached to the group in question
*/
function next_bp_events_group_is_event() {
  global $bp, $wpdb;
  $field_meta = groups_get_groupmeta( $bp->groups->current_group->id, 'bp_next_pack_group-is-conference' );
  return $field_meta;
}

/* get group conference url
*/
function next_bp_events_group_conference_url() {
  global $bp, $wpdb;
  $field_meta = groups_get_groupmeta( $bp->groups->current_group->id, 'bp_next_pack_group-conference-url' );
  return $field_meta;
}

/* get group submission description
*/
function next_bp_events_group_conference_submission() {
  global $bp, $wpdb;
  $field_meta = groups_get_groupmeta( $bp->groups->current_group->id, 'bp_next_pack_group-conference-submission' );
  return $field_meta;
}



/* Create the form to save the meta for the group

For the forms 'value' we echo $bp_next_ocs_group_field_1, this is done so that when the group has existing data in the field..
*/
function next_bp_events_group_custom_group_field() {
global $bp, $wpdb;

 ?>
  <frameset id="conference-config">
    <h3>Configurações do evento</h3>
    <label for="group-is-conference">Este grupo é do tipo evento?</label>
    <input id="group-is-conference" type="radio" name="group-is-conference" value="1" 
    <?php if( next_bp_events_group_is_event() == 1 ){ ?>
      checked="checked"
    <?php } ?>  
    /> Sim
    <input id="group-is-conference" type="radio" name="group-is-conference" value="0"
    <?php if( next_bp_events_group_is_event() == 0 ){ ?>
      checked="checked"
    <?php } ?>  
    /> Não  
    <label for="group-conference-url">Digite a url do sistema de eventos:</label>
    <input type="text" name="group-conference-url" id="group-conference-url" value="<?php echo next_bp_events_group_conference_url(); ?>" />
    
    <label for="group-conference-submission">Digite o texto para a descrição pagina principal do evento</label>
    <input type="text" name="group-conference-submission" id="group-conference-submission" value="<?php echo next_bp_events_group_conference_submission(); ?>" />
    
  </frameset>
 <?php

}

// Show the group meta in group header
function next_bp_events_group_custom_group_field_show( $bp_next_pack_group_field_meta ) {

     echo '<br /><div class="bp_next_pack_group">'. next_bp_events_group_is_event() .'</div>';
}
  
add_filter( 'bp_before_group_header_meta', 'next_bp_events_group_custom_group_field_show' );

// save the group meta to the db
function next_bp_events_group_custom_group_field_save( $group_id ) {
  global $bp, $wpdb;

  $plain_fields = array(
    'is-conference',
    'conference-url',
    'conference-submission'    
/*    'field-3'     */
  );
  foreach( $plain_fields as $field ) {
    $key = 'group-' . $field;
    if ( isset( $_POST[$key] ) ) {
      $value = $_POST[$key];
      groups_update_groupmeta( $group_id, 'bp_next_pack_group-' . $field, $value );
    }
  }
}

/*
Include our custom css rules - $bp_bp_next_pack_group_insert_wp_head tells Wordpress to locate our custom style sheet and load it in the header ( specifically the <head> tag ).
If you look at line .., we are echo'ing <div class="bp_next_pack_group"> within $next_bp_events_group_custom_group_field_show.
*/
function next_bp_events_group_insert_css() {
  ?>
  <link href="<?php bloginfo('wpurl'); ?>/wp-content/plugins/buddypress-group-skeleton-component/includes/style.css" media="screen" rel="stylesheet" type="text/css"/>
  <?php 
}
add_action('wp_head', 'next_bp_events_group_insert_css');





