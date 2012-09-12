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

function bp_groups_remove_menus_from_events() {
	global $bp;

	$current_group_slug = $bp->groups->current_group->slug;

	if(!(strpos($current_group_slug, 'eventos') === false)){
		//todo: adicionar uma tipagem no grupo, para nao depender de nomes nem slugs.
		//atualizar o plugin  do repo para bp docs.
		
		$bp->bp_options_nav[$current_group_slug]['home'] = false;
		$bp->bp_options_nav[$current_group_slug]['forums']['name'] = 'testando';
		$bp->bp_options_nav[$current_group_slug]['members'] = false;
		$bp->bp_options_nav[$current_group_slug]['events'] = false;
		$bp->bp_options_nav[$current_group_slug]['documents'] = false;

	}

}
add_action('bp_head', 'bp_groups_remove_menus_from_events', 15);// em bp_actions é possivel acessar o forums mas nao o docs.


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