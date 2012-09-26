<?php
/*
 Plugin Name: NEXT Events addon
Author: Caio Wilson
Description: Plugin para transformar o grupo Eventos num tipo de grupo. Remove outros menus e seta a pagina de forums como inicial do grupo e subgrupos.
License: GPLv3

Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : PLUGIN AUTHOR EMAIL)

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
 * adiciona a tab de eventos no grupo de eventos.
 */
function add_event_group_activity_tab() {
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
	}
}

add_action( 'bp_actions', 'add_event_group_activity_tab', 8 );

/**
 * cria o conteúdo do component criado.
 */
function create_event_group_content(){
	
	/* add_action('bp_template_content_header', 'bp_group_documents_display_header');
	add_action('bp_template_title', 'bp_group_documents_display_title');
	add_action('bp_template_content', 'bp_group_documents_display_content'); */
	
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
 * faz o output do component 
 */
function bp_events_display_content(){
	//bp_has_activities( bp_ajax_querystring('activity' ) .  '&search_terms=' . 'teste');
	//assim que botar o CPT para funcionar tem que fazer um custom loop aqui.
	if ( is_user_logged_in() && bp_group_is_member() ){
		locate_template( array( 'activity/post-form.php'), true );
		add_action('bp_before_activity_post_form', 'bp_events_group_modify_activity_forms');
		locate_template( array( 'activity/activity-loop.php' ), true );
	}
}

function bp_events_group_modify_activity_forms(){
	
	
	
}



function is_events_group(){
	//todo: adicionar uma tipagem no grupo, para nao depender de nomes nem slugs. talvez com a ajuda do group tags(plugin)
		//atualizar o plugin  do repo para bp docs.
		
	$current_group_slug = $bp->groups->current_group->slug;
	
	if(strpos($current_group_slug, 'eventos') === false && strpos($current_group_slug, 'eventos') != 0)//o srtpos retorna o boolean false, mas quando acha na primeira posicao retorna 0 o que faz com que a bosta do if leia errado ainda que esteja com === :/
		return false;
	else
		return true;
	
}

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