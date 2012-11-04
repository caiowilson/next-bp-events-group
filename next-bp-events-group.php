<?php
/*
 Plugin Name: NEXT Events addon
Author: Caio Wilson
Description: Plugin para transformar o grupo Eventos num tipo de grupo. Remove outros menus e seta a pagina de forums como inicial do grupo e subgrupos. NECESSITA DO PLUGIN buddypress-activity-plus
License: GPLv3

Copyright 2012  Caio Wilson  (email : caiowilson@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
global $title_content;
function bp_dump() {
	
	echo plugin_dir_url(__FILE__) . 'next-bp-events-group.php';
}
//add_action( 'bp_head', 'bp_dump' ); // hooks para ler bp_setup_nav bp_actions e wp e bp_setup_nav bp_head



function bp_events_js_loader(){
	$file = $_SERVER["SCRIPT_NAME"];
	$path_details=pathinfo($file);
	
	//wp_localize_script( 'titleactivityposthandler', 'MyAjax', array( 'ajaxurl' => 'http://www.next.icict.fiocruz.br/sec/activity/post/' ) );
	
	
	wp_register_script(
		'titleactivityposthandler',
		plugins_url('js/TAPHandler.js', __FILE__), 
		array('jquery')	
	);

}

add_action('wp_enqueue_scripts', 'bp_events_js_loader');



add_action('init', 'bp_events_js_loader');
add_action('wp_footer', 'print_my_script');


function print_my_script() {

	wp_print_scripts('titleactivityposthandler');
}


/**
 * adiciona as tabs dos componentse de eventos no grupo de eventos.
 */
function add_event_group_activity_tabs() {
	global $bp;

	if(bp_is_group() && is_events_group()) {
		  
/*
		bp_core_new_subnav_item(
  		array(
    		'name' => 'Buscar Trabalhos',
    		'slug' => 'activity-search',
    		'parent_slug' => $bp->groups->current_group->slug,
    		'parent_url' => bp_get_group_permalink( $bp->groups->current_group ),
    		'position' => 12,
    		'item_css_id' => 'nav-activity-search',
    		'screen_function' => 'create_event_group_content_search',
    		'user_has_access' => 1
  		)
		);
		
		if ( bp_is_current_action( 'activity-search' ) ) {
			add_action( 'bp_template_content_header', create_function( '', 'echo "' . esc_attr( 'Activity Search' ) . '";' ) );
			add_action( 'bp_template_title', create_function( '', 'echo "' . esc_attr( 'Activity Search' ) . '";' ) );
		}
    */
    bp_core_new_subnav_item(
      array(
        'name' => 'Participar',
        'slug' => 'participar',
        'parent_slug' => $bp->groups->current_group->slug,
        'parent_url' => bp_get_group_permalink( $bp->groups->current_group ),
        'position' => 12,
        'item_css_id' => 'nav-participar',
        'screen_function' => 'create_event_group_participar',
        'user_has_access' => 1
      )
    );
    
    if ( bp_is_current_action( 'participar' ) ) {
      add_action( 'bp_template_content_header', create_function( '', 'echo "' . esc_attr( 'Participar' ) . '";' ) );
      add_action( 'bp_template_title', create_function( '', 'echo "' . esc_attr( 'Participar' ) . '";' ) );
    }    
    
	}
}

add_action( 'bp_actions', 'add_event_group_activity_tabs', 8 );




/**
 * cria o conte�do do component criado.
 */
function create_event_group_content(){


	add_action('bp_template_content', 'bp_events_display_content');

	// Load the plugin template file.
	// BP 1.2 breaks it out into a group-specific template
	// BP 1.1 includes a generic "plugin-template file
	//this is a roundabout way of doing it, because I can't find a way to use bp_core_template
	//to either return a useful value or handle an array of templates
	$templates = array('groups/single/plugins.php', 'plugin-template.php');
	if (strstr(locate_template($templates), 'groups/single/plugins.php')) {
		bp_core_load_template(apply_filters('bp_core_template_plugin', 'groups/single/plugins'));
	} else {
		bp_core_load_template(apply_filters('bp_core_template_plugin', 'plugin-template'));
	}
}

/**
 * cria o conte�do do component search criado.
 */
function create_event_group_content_search(){

	add_action('bp_template_content', 'bp_events_display_content_search');

	// Load the plugin template file.
	// BP 1.2 breaks it out into a group-specific template
	// BP 1.1 includes a generic "plugin-template file
	//this is a roundabout way of doing it, because I can't find a way to use bp_core_template
	//to either return a useful value or handle an array of templates
	$templates = array('groups/single/plugins.php', 'plugin-template.php');
	if (strstr(locate_template($templates), 'groups/single/plugins.php')) {
		bp_core_load_template(apply_filters('bp_core_template_plugin', 'groups/single/plugins'));
	} else {
		bp_core_load_template(apply_filters('bp_core_template_plugin', 'plugin-template'));
	}
}

/**
 * cria o conte�do do component  compartilhar trabalho
 */
function create_event_group_participar(){


  add_action('bp_template_content', 'bp_events_display_participar');

  // Load the plugin template file.
  // BP 1.2 breaks it out into a group-specific template
  // BP 1.1 includes a generic "plugin-template file
  //this is a roundabout way of doing it, because I can't find a way to use bp_core_template
  //to either return a useful value or handle an array of templates
  $templates = array('groups/single/plugins.php', 'plugin-template.php');
  
  bp_core_load_template(apply_filters('bp_core_template_plugin', 'groups/single/plugin-evento'));    

}


/**
 * faz o output do component 
 */
function bp_events_display_content(){
	//bp_has_activities( bp_ajax_querystring('activity' ) .  '&search_terms=' . 'teste');
	//assim que botar o CPT para funcionar tem que fazer um custom loop aqui.
	if ( is_user_logged_in() && bp_group_is_member() ){
		locate_template( array( 'activity/post-form.php'), true );
		locate_template( array( 'activity/activity-loop.php' ), true );
	}
}


/**
 * faz o output do component search
 */
function bp_events_display_participar(){

	
	if ( is_user_logged_in() && bp_group_is_member() ){
		//locate_template( array( 'activity/activity-loop.php' ), true );
		$file = $_SERVER["SCRIPT_NAME"];
		$path_details=pathinfo($file);
    
		$searchterm = ( !empty($_GET['s']) ) ? $_GET['s'] : '' ;
    
		?>

  <div id="group-activity-search" class="group-activity-search">    
    <form NAME ="event-activity-search-form" METHOD ="get" ACTION = "<?php echo $path_details['basename'];  ?>">
    <label for="event-activity-search-input">Buscar: </label>
      <input TYPE = "TEXT" id="event-activity-search-input" name="s" value="<?php echo $searchterm; ?>" >    
    <INPUT TYPE = "Submit" Name = "procurar" id="event-activity-search-submit-button" VALUE = "Buscar">
    <input type="hidden" id="is-in-ocs" name="ocs" value="1" >
    </form>
  </div>
  <br/>
		
<div class="item-list-tabs no-ajax" id="subnav">
  <ul>
    <div class="feed"><a href="<?php bp_group_activity_feed_link() ?>" title="<?php _e( 'RSS Feed', 'buddypress' ); ?>"><?php _e( 'RSS', 'buddypress' ) ?></a></div>
  
    <?php do_action( 'bp_group_activity_syndication_options' ) ?>

    <li id="activity-filter-select" class="last">
      <label for="activity-filter-by"><?php _e( 'Listar :', 'buddypress' ); ?></label>
      <select>
        <option value="-1"><?php _e( 'tudo', 'buddypress' ) ?></option>
        <option value="activity_update"><?php _e( 'Show Updates', 'buddypress' ) ?></option>

        <?php if ( bp_is_active( 'forums' ) ) : ?>
          <option value="new_forum_topic"><?php _e( 'Show New Forum Topics', 'buddypress' ) ?></option>
          <option value="new_forum_post"><?php _e( 'Show Forum Replies', 'buddypress' ) ?></option>
        <?php endif; ?>

        <option value="joined_group"><?php _e( 'Show New Group Memberships', 'buddypress' ) ?></option>

        <?php do_action( 'bp_group_activity_filter_options' ) ?>
      </select>
    </li>
  </ul>
</div><!-- .item-list-tabs -->		
		
<?php do_action( 'bp_before_group_activity_content' ) ?>

<div class="activity single-group" role="main">
  <?php locate_template( array( 'activity/activity-loop.php' ), true ) ?>
</div><!-- .activity.single-group -->

<?php do_action( 'bp_after_group_activity_content' ) ?>		
	
	<?php
		/*
		if ( bp_has_activities('search_terms=' . $searchterm) ) :
			while ( bp_activities() ) : bp_the_activity();
				?><ul id="activity-stream" class="activity-list item-list">
				<?php 
				locate_template( array( 'activity/entry.php' ), true, false );
				?></ul><?php
			endwhile;
		else : ?>
			<div id="message" class="info">
			<p><?php _e( 'Sorry, there was no activity found. Please try a different filter.', 'buddypress' ) ?></p>
			</div>
			<?php 
		endif;
     */
	}
  
}


/**
 * Adiciona o t�tulo ao conteudo do post no activities
 * 
 * @param mixed $content
 * @return mixed
 */
function add_title_to_events_group_activity_update_body($content){
	global $activities_template;
	
	$custom_meta_activity_title = bp_activity_get_meta($activities_template->activities[$activities_template->current_activity]->id, 'title');
	
	/* foreach ( (array)$custom_meta_activity_title as $key => $value ) {
		echo '<pre>';
		echo '<strong>' . $key . ': </strong><br />';
		print_r( $value );
		echo '</pre>';
	} */

	return //$custom_meta_act .
	'<strong>'.$custom_meta_activity_title.'</strong><br>'
	. $content ;
}

add_filter('bp_get_activity_content_body', 'add_title_to_events_group_activity_update_body');



/**
 * adiciona o titulo no meta do post na activity.
 * @param $activity
 */
function wp_ajax_add_event_title() {
	
	//global $title_content;
	//check_admin_referer( 'post_update', '_wpnonce_post_update' );
	//$title_content = $_POST['action'];
	
	
	/* else {

	bp_activity_update_meta($activity->id, 'title',  $title_content);//$_POST['activity-event-post-title'];
	} */
	/* $file = $_SERVER["SCRIPT_NAME"];
	$path_details=pathinfo($file);
	echo 'actual file = ' . $path_details['basename']; */
	
	foreach ( $_POST as $key => $value ) {
		echo '<strong>' .$key.'</strong>' . ' '  . '=' . ' '  . $value;
		echo  '<BR> ';
	} 
	//exit;
	
}

//add_action( 'wp_ajax_post_update', 'wp_ajax_add_event_title');//bp_activity_after_save


function add_title_to_activity_meta ($activity) {

	global $title_content;
	//echo 'CAIO = '.$title_content;
	/* else {*/

	bp_activity_update_meta($activity->id, 'title',  $title_content . ' testando ' );//$_POST['activity-event-post-title'];
	//} 
	/* $file = $_SERVER["SCRIPT_NAME"];
	 $path_details=pathinfo($file);
	echo 'actual file = ' . $path_details['basename']; */


}
//add_action('bp_activity_after_save', 'add_title_to_activity_meta', 10 , 1);
/* function bp_events_activity_post_data(){

	$testando = $_POST['whats-new'];
	
	echo 'OMG' . $testando;

}

add_filter('wp_ajax_post_update', 'bp_events_activity_post_data');//bp_activity_after_save,bp_actions,bp_activity_add */




/**
 * registra os hooks do componente ao hook init para edi�ao dos forms de envio
 */
function bp_events_group_add_activity_forms_hooks(){
	global $bp;
	$me = new BpfbBinder;
	if('activity' == $bp->current_action && is_events_group()){

		add_action('wp_print_scripts', array($me, 'js_plugin_url'));
		add_action('wp_print_scripts', array($me, 'js_load_scripts'));
		add_action('wp_print_styles', array($me, 'css_load_styles'));
		add_action('bp_activity_post_form_options','bp_events_group_modify_activity_forms_after');
		add_action('bp_before_activity_post_form','bp_events_group_modify_activity_forms_before');
	}

}

//add_action('init', 'bp_events_group_add_activity_forms_hooks');



/**
 * faz o output do que vem antes do form principal da activity
 */
function bp_events_group_modify_activity_forms_before(){
	?>
	<br /><label class="add_post_label">T&iacutetulo</label>
	<input type="text" name="activity-event-post-title" id="activity-event-post-title" class="whats-new-post-title" value="<?php @$_POST['activity-event-post-title']?>" /><br />
	<?php

}

/**
 * faz o output do que vem depois do form principal da activity
 */
function bp_events_group_modify_activity_forms_after(){
	?>
	<input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />
	<input type="hidden" id="whats-new-post-in" name="whats-new-post-in" value="<?php bp_group_id(); ?>" />
	<?php 
}




/**
 * Checa se o current group � de eventos, retorna bool true/false
 * @return boolean
 */
function is_events_group(){
	global $bp;
	//todo: adicionar uma tipagem no grupo, para nao depender de nomes nem slugs. talvez com a ajuda do group tags(plugin)
		//atualizar o plugin  do repo para bp docs.
		
	$current_group_slug = $bp->groups->current_group->slug;
	
  if( !empty( $_POST['ocs'] ) && $_POST['ocs'] ){
    return true;
  } 
  
	if(strpos($current_group_slug, 'eventos') === false &&
	   strpos($current_group_slug, 'eventos') != 0)//o srtpos retorna o boolean false, mas quando acha na primeira posicao retorna 0 o que faz com que a bosta do if leia errado ainda que esteja com === :/
		return false;
	else
		return true;
	
}

/**
 * modifica o menu dos grupos de eventos
 */
function bp_groups_remove_menus_from_events() {
	global $bp;

	$current_group_slug = $bp->groups->current_group->slug;

	if( is_events_group() ){
		
		//var_dump($bp->bp_options_nav[$current_group_slug]);
		
		$bp->bp_options_nav[$current_group_slug]['home']['name'] = 'Compartilhar trabalho';
    $bp->bp_options_nav[$current_group_slug]['forum'] = false;
		//$bp->bp_options_nav[$current_group_slug]['members'] = false;
		$bp->bp_options_nav[$current_group_slug]['events'] = false;
		$bp->bp_options_nav[$current_group_slug]['documents'] = false;
    $bp->bp_options_nav[$current_group_slug]['hierarchy'] = false;
    
    $bp->bp_options_nav[$current_group_slug]['invite-anyone']['name'] = 'Convidar para o evento';
    $bp->bp_options_nav[$current_group_slug]['notifications']['name'] = 'Notificações';
	}

}
add_action('bp_head', 'bp_groups_remove_menus_from_events', 15);//hook ideal para modificar o menu dos groups do buddypress



/* function redirect_to_forums() {
	global $bp;

	//$path = clean_url( $_SERVER['REQUEST_URI'] );
	$current_group_slug = $bp->groups->current_group->slug;
	$landing_url = site_url() . '/groups/' . $current_group_slug . '/';

	//echo $bp->bp_options_nav[$current_group_slug]['home'];
	if(bp_is_group_home() && $bp->bp_options_nav[$current_group_slug]['home'] === false && is_events_group()){


		bp_core_redirect($landing_url . 'forums/');

	}



}
 */
//add_action( 'wp', 'redirect_to_forums' );


/* function bp_groups_documents_search_list(){
 if($_POST['bp-documents-search-name'] != ''){

$template = new BP_Group_Documents_Template();

foreach ($template->document_list as $document_params) {
$document = new BP_Group_Documents($document_params['id'], $document_params);

if($document->name == $_POST['bp-documents-search-name']){
echo "Arquivos encontrados com esse nome: </br>

<a href='" . $document->get_url() . "' >" . $document->name . '</a> ' ;
}
}
}
} */

//add_filter('bp_group_documents_display', 'bp_groups_documents_search_list');


?>