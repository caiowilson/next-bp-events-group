<?php
/*
 Plugin Name: next-bp-events-group
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

/**
 * Carrega os recursos que precisam estar no inicio no load do WP
 * 
 */
function next_bp_events_group_init() {
  require( dirname( __FILE__ ) . '/next-bp-events-group.php' );

}
add_action('init', 'next_bp_events_group_init');


/**
 * Inicia a parte do Buddypress do plugin garantindo que os recursos do bp estão
 * carregados
 */
function next_bp_events_group_bp_init() {
  require( dirname( __FILE__ ) . '/next-bp-events-group-type.php' );
  require( dirname( __FILE__ ) . '/next-bp-events-group-config.php' );
}
add_action( 'bp_include', 'next_bp_events_group_bp_init' );







