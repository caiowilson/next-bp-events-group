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

function bp_dump() {
	global $bp;
	$current_group_slug = $bp->groups->current_group->slug;
	foreach ( (array)$bp as $key => $value ) {
		echo '<pre>';
		echo '<strong>' . $key . ': </strong><br />';
		print_r( $value );
		echo '</pre>';
	}
	die;
}
//add_action( 'bp_head', 'bp_dump' ); // hooks para ler bp_setup_nav bp_actions e wp e bp_setup_nav bp_head


/**
 * adiciona as tabs dos componentse de eventos no grupo de eventos.
 */
function add_event_group_activity_tabs() {
	global $bp;

	if(bp_is_group() && is_events_group()) {
		bp_core_new_subnav_item(
		array(
		'name' => 'Activity',
		'slug' => 'activity',
		'parent_slug' => $bp->groups->current_group->slug,
		'parent_url' => bp_get_group_permalink( $bp->groups->current_group ),
		'position' => 11,
		'item_css_id' => 'nav-activity',
		'screen_function' => 'create_event_group_content',
		'user_has_access' => 1
		)
		);

		if ( bp_is_current_action( 'activity' ) ) {
			add_action( 'bp_template_content_header', create_function( '', 'echo "' . esc_attr( 'Activity' ) . '";' ) );
			add_action( 'bp_template_title', create_function( '', 'echo "' . esc_attr( 'Activity' ) . '";' ) );
		}
		
		bp_core_new_subnav_item(
		array(
		'name' => 'Search',
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
	}
}

add_action( 'bp_actions', 'add_event_group_activity_tabs', 8 );




/**
 * cria o conteúdo do component criado.
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
 * cria o conteúdo do component search criado.
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
function bp_events_display_content_search(){

	
	if ( is_user_logged_in() && bp_group_is_member() ){
		//locate_template( array( 'activity/activity-loop.php' ), true );
		$file = $_SERVER["SCRIPT_NAME"];
		$path_details=pathinfo($file);
		$searchterm = @$_POST['searchterm'];
		
		?><form NAME ="event-activity-search-form" METHOD ="post" ACTION = "<?php echo $path_details['basename'];  ?>">
		
		<input TYPE = "TEXT" id="event-activity-search-input"   Name="searchterm" value="" >
		
		<INPUT TYPE = "Submit" Name = "event-activity-search-submit-button" id="event-activity-search-submit-button" VALUE = "Search Activity">
		
		</form><?php
		
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
			</div><?php 
		endif;
	}
}


/**
 * Adiciona o título ao conteudo do post no activities
 * 
 * @param mixed $content
 * @return mixed
 */
function add_title_to_events_group_activity_update_body($content){
	global $activities_template;
	
	$custom_meta_act = bp_activity_get_meta($activities_template->activities[$activities_template->current_activity]->id, 'title');
	
	/* foreach ( (array)$custom_meta_act as $key => $value ) {
		echo '<pre>';
		echo '<strong>' . $key . ': </strong><br />';
		print_r( $value );
		echo '</pre>';
	} */

	return //$custom_meta_act .
	'<strong>'.$custom_meta_act.'</strong><br>'
	. $content ;
}

add_filter('bp_get_activity_content_body', 'add_title_to_events_group_activity_update_body');


/**
 * adiciona o titulo no meta do post na activity.
 * @param $activity
 */
function add_title_to_activity_meta( $activity) {
	//global $activities_template;
	
	//$tempVar = $activities_template->activities[$activities_template->current_activity]->id + 1;
	if($_POST['activity-post-title'])
		$title_content = @$_POST['activity-post-title'];
	else
		$title_content = 'nao pegou o post';
	
	//echo phpinfo(32);
	
	
	bp_activity_update_meta($activity->id, 'title',  $title_content);//$_POST['activity-post-title']
	
}

add_action( 'bp_activity_after_save', 'add_title_to_activity_meta', 10, 1 );//testar o bp_actions //bp_activity_add funciona, mas nao tenho o id. outro = bp_activity_posted_update, bp_activity_after_save

/* function add_genre_to_activity( $content, $user_id, $activity_id ) {
	if ( strpos( $content, 'rock' ) )
		bp_activity_update_meta( $activity_id, 'genre', 'rock' );
}
add_action( 'bp_activity_posted_update', 'add_genre_to_activity', 10, 3 ); */



/**
 * registra os hooks do componente ao hook init para ediçao dos forms de envio
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

add_action('init', 'bp_events_group_add_activity_forms_hooks');



/**
 * faz o output do que vem antes do form principal da activity
 */
function bp_events_group_modify_activity_forms_before(){
	?>
	<br /><label class="add_post_label">T&iacutetulo</label>
	<input type="text" name="activity-post-title" id="activity-post-title" class="whats-new-post-title" value="<?php @$_POST['activity-post-title']?>" /><br />
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
 * Checa se o current group é de eventos, retorna bool true/false
 * @return boolean
 */
function is_events_group(){
	global $bp;
	//todo: adicionar uma tipagem no grupo, para nao depender de nomes nem slugs. talvez com a ajuda do group tags(plugin)
		//atualizar o plugin  do repo para bp docs.
		
	$current_group_slug = $bp->groups->current_group->slug;
	
	if(strpos($current_group_slug, 'eventos') === false && strpos($current_group_slug, 'eventos') != 0)//o srtpos retorna o boolean false, mas quando acha na primeira posicao retorna 0 o que faz com que a bosta do if leia errado ainda que esteja com === :/
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

	if(is_events_group()){
		
		
		$bp->bp_options_nav[$current_group_slug]['home'] = false;
		$bp->bp_options_nav[$current_group_slug]['forums']['name'] = 'testando';
		$bp->bp_options_nav[$current_group_slug]['members'] = false;
		$bp->bp_options_nav[$current_group_slug]['events'] = false;
		$bp->bp_options_nav[$current_group_slug]['documents'] = false;

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