<?php

/* Custom Group Extension for Buddypress   */

/* Add the form and save the meta - $bp_next_pack_group_custom_group_field */ 
// load conference configs is user is admin
if( is_super_admin() ){
  add_filter( 'groups_custom_group_fields_editable', 'bp_next_pack_group_custom_group_field' ); // This adds the form to the group admin details page and the first page of the group creation steps.
  add_action( 'groups_group_details_edited', 'bp_next_pack_group_custom_group_field_save' ); // This saves the meta when editing the meta on the group edit details page.
  add_action( 'groups_created_group',  'bp_next_pack_group_custom_group_field_save' ); // This save the meta durring the group creation process.
}
/* Retrieve the meta specific to each group - $bp_next_ocs_group_field_1 fetches the meta_value in the bp_next_pack_group-field-1 meta_key attached to the group in question
*/
function bp_next_ocs_group_is_conference() {
  global $bp, $wpdb;
  $field_meta = groups_get_groupmeta( $bp->groups->current_group->id, 'bp_next_pack_group-is-conference' );
  return $field_meta;
}

/* get group conference url
*/
function bp_next_ocs_group_conference_url() {
  global $bp, $wpdb;
  $field_meta = groups_get_groupmeta( $bp->groups->current_group->id, 'bp_next_pack_group-conference-url' );
  return $field_meta;
}

/* Create the form to save the meta for the group

For the forms 'value' we echo $bp_next_ocs_group_field_1, this is done so that when the group has existing data in the field..
*/
function bp_next_pack_group_custom_group_field() {
global $bp, $wpdb;

 ?>
  <frameset id="conference-config">
    <h3>Configurações da conferencia</h3>
    <label for="group-is-conference">Este grupo é parte de uma conferencia?</label>
    <input id="group-is-conference" type="radio" name="group-is-conference" value="1" 
    <?php if( bp_next_ocs_group_is_conference() == 1 ){ ?>
      checked="checked"
    <?php } ?>  
    /> Sim
    <input id="group-is-conference" type="radio" name="group-is-conference" value="0"
    <?php if( bp_next_ocs_group_is_conference() == 0 ){ ?>
      checked="checked"
    <?php } ?>  
    /> Não
    
    
  
    <label for="group-conference-url">Digite a url da conferencia:</label>
    <input type="text" name="group-conference-url" id="group-conference-url" value="<?php echo bp_next_ocs_group_conference_url(); ?>" />
  </frameset>
 <?php

}

// Show the group meta in group header
function bp_next_pack_group_custom_group_field_show( $bp_next_pack_group_field_meta ) {

     echo '<br /><div class="bp_next_pack_group">'. bp_next_ocs_group_is_conference() .'</div>';
}
  
add_filter( 'bp_before_group_header_meta', 'bp_next_pack_group_custom_group_field_show' );

// save the group meta to the db
function bp_next_pack_group_custom_group_field_save( $group_id ) {
  global $bp, $wpdb;

  $plain_fields = array(
    'is-conference',
    'conference-url'     
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
If you look at line .., we are echo'ing <div class="bp_next_pack_group"> within $bp_next_pack_group_custom_group_field_show.
*/

function bp_bp_next_pack_group_insert_css() {
  ?>
  <link href="<?php bloginfo('wpurl'); ?>/wp-content/plugins/buddypress-group-skeleton-component/includes/style.css" media="screen" rel="stylesheet" type="text/css"/>
  <?php 
}
add_action('wp_head', 'bp_bp_next_pack_group_insert_css');


// configuração para criar um grupo do tipo conferencia


function bp_next_ocs_change_menus() {
  global $bp;

  $current_group_slug = $bp->groups->current_group->slug;
 
  if( bp_next_ocs_group_is_conference() ){
    //todo: adicionar uma tipagem no grupo, para nao depender de nomes nem slugs.
    //atualizar o plugin  do repo para bp docs.
    //debugar_json_console($bp->bp_options_nav[$current_group_slug]);
    $bp->bp_options_nav[$current_group_slug]['home'] = false;
    $bp->bp_options_nav[$current_group_slug]['forum']['name'] = 'Conferencia Online';
    $bp->bp_options_nav[$current_group_slug]['forums'] = false;
    $bp->bp_options_nav[$current_group_slug]['members'] = false;
    // wp events plugin
    $bp->bp_options_nav[$current_group_slug]['events'] = false;
    $bp->bp_options_nav[$current_group_slug]['documents'] = false;
    // group hierarchy plugin
    $bp->bp_options_nav[$current_group_slug]['hierarchy'] = false;
    // send invites by email page
    $bp->bp_options_nav[$current_group_slug]['send-invites'] = false;
    // group task manager plugin
    $bp->bp_options_nav[$current_group_slug]['gtm'] = false;
    
    $bp->bp_options_nav[$current_group_slug]['notifications']['name'] = 'Opções de Notificações';

  } else {
    $bp->bp_options_nav[$current_group_slug]['forums']['name'] = 'Discussões';    
  }

  // paroveitando para deixar mais amigavel ...
  $bp->bp_options_nav[$current_group_slug]['notifications']['name'] = 'Opções de Notificações';

}
add_action('bp_head', 'bp_next_ocs_change_menus', 15);

function redirect_to_forums() {
  global $bp;
  
  //$path = clean_url( $_SERVER['REQUEST_URI'] );
  $current_group_slug = $bp->groups->current_group->slug;
  $landing_url = site_url() . '/groups/' . $current_group_slug . '/';
  
  //echo $bp->bp_options_nav[$current_group_slug]['home'];
  if(bp_is_group_home() && $bp->bp_options_nav[$current_group_slug]['home'] === false){

    bp_core_redirect($landing_url . 'forums/');

  }

}
add_action( 'wp', 'redirect_to_forums' );

