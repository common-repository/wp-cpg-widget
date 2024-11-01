<?php

/*
    Plugin Name: WP Coppermine Plugin
    Plugin URI: http://www.cyberspice.org.uk/blog/wordpress-coppermine-widget/
    Description: Coppermine gallery plugin that supports a configurable sidebar widget.
    Version: 1.0.2
    Author: Cyberspice
    Author URI: http://www.cyberspice.org.uk/
	
    Copyright 2009, Melanie Rhianna Lewis

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// The widget code
require_once('wp-cpg-widget.php');

/**
 * Install the plugin by setting default options.
 *
 * The function sets the default options for the plugin and creates options
 * for a single gallery, the default gallery.  These are then stored in
 * Wordpress' options.
 *
 * The function is registered with Wordpress as an activation hook function.
 * 
 * @since 0.01
 */
function wp_cpg_plugin_activate() {

	// Set the defaults for the 'default' database.
	$newdboptions = get_option('wp_cpg_plugin_db_default');
	$newdboptions['gallery_url']     = '';
	$newdboptions['db_server']       = '';
	$newdboptions['db_database']     = '';
	$newdboptions['db_username']     = '';
	$newdboptions['db_password']     = '';
	$newdboptions['db_table_prefix'] = '';
	$newdboptions['installed']       = false;
	add_option('wp_cpg_plugin_db_default', $newdboptions);
	
	// Set the defaults for the plugin.
	$newoptions = get_option('wp_cpg_plugin_options');	
	$newoptions['installed'] = false;
	$newoptions['galleries'] = 'default';
	add_option('wp_cpg_plugin_options', $newoptions);
	
	// Set the defaults for the widget.
	$widgetopts = get_option('wp_cpg_widget_options');
	$widgetopts['gallery'] = 'default';
	$widgetopts['title']   = 'Gallery Images';
	$widgetopts['count']   = 6;
	$widgetopts['width']   = 2;
	add_option('wp_cpg_widget_options', $widgetopts);
}

/**
 * Uninstall the plugin.
 *
 * The function uninstalls the plugin and removes any options the plugin
 * has created from Wordpress' options.
 *
 * The function is registered with Wordpress as a deactivation hook function.
 * 
 * @since 0.01
 */
function wp_cpg_plugin_deactivate() {
	$options   = get_option('wp_cpg_plugin_options');
	$galleries = explode(",", $options['galleries']);
	
	for ($i = 0; $i < count($galleries); $i++) {
		delete_option('wp_cpg_plugin_db_'.$galleries[$i]);
	}
	
	delete_option('wp_cpg_plugin_options');
	delete_option('wp_cpg_widget_options');
}

/**
 * Adds the option pages to Wordpress' admin pages
 *
 * The function adds a plugin option pages to Wordpress' settings pages.
 * With the name of "WP Coppermine".
 * 
 * @since 0.01
 *
 * The function is registered as an Wordpress action for 'admin_menu'.
 */
function wp_cpg_plugin_add_pages() {
	add_options_page('WP Coppermine', 'WP Coppermine', 8, __FILE__, 'wp_cpg_plugin_options');
}

/**
 * Displays the options 
 * 
 * The function displays the options that were set in 
 * wp_cpg_plugin_install_options() in a form suitable for
 * Wordpress' settings screens.
 * 
 * @since 0.01
 * 
 * @param array $options The saved options for the plugin
 */
function wp_cpg_plugin_display_options($options) {
	$dboptions = get_option('wp_cpg_plugin_db_default');
	
	echo "<div class=\"wrap\"><h2>WP Coppermine Default Gallery Options</h2>";
	echo '<table class="form-table">';

	// Database server
	echo '<tr valign="top"><th scope="row">Gallery URL</th>';
	echo '<td><span><strong>'.$dboptions['gallery_url'].'</strong></span>';
	echo '<br />The URL for the Coppermine Gallery.</td></tr>';
	
	// Database server
	echo '<tr valign="top"><th scope="row">Database server</th>';
	echo '<td><span><strong>'.$dboptions['db_server'].'</strong></span>';
	echo '<br />The domain name for the Coppermine database server.</td></tr>';
	
	// Database name
	echo '<tr valign="top"><th scope="row">Database name</th>';
	echo '<td><span><strong>'.$dboptions['db_database'].'</strong></span>';
	echo '<br />The name of the Coppermine database.</td></tr>';
	
	// Database user name
	echo '<tr valign="top"><th scope="row">The user name</th>';
	echo '<td><span><strong>'.$dboptions['db_username'].'</strong></span>';
	echo '<br />The Coppermine database user name.</td></tr>';
	
	// Database user name
	echo '<tr valign="top"><th scope="row">The password</th>';
	echo '<td><span><strong>**********</strong></span>';
	echo '<br />The Coppermine database user password.</td></tr>';

	// Table name prefix
	echo '<tr valign="top"><th scope="row">The table prefix</th>';
	echo '<td><span><strong>'.$dboptions['db_table_prefix'].'</strong></span>';
	echo '<br />The prefix to the Coppermine table names.</td></tr>';
	
	// Close table
	echo '</table>';
	echo '<p>To change these settings de-activate the plug-in and re-activate.</p>';
	echo "</div>";
}

/**
 * Sets the options.
 * 
 * Sets the options namely the default database settings.  The function
 * displays and handles a form that is displayed as part of Wordpress'
 * settings screens.
 * 
 * @since 0.01
 * 
 * @param array $options The saved options for the plugin
 */
function wp_cpg_plugin_install_options($options) {
	$dboptions = get_option('wp_cpg_plugin_db_default');
	
	if ($_POST['wp_cpg_options_submit']) {

		$newdboptions = $dboptions;
		$newdboptions['gallery_url'] = $_POST["gallery_url"];
		$newdboptions['db_server'] = strip_tags(stripslashes($_POST["db_server"]));
		$newdboptions['db_database'] = strip_tags(stripslashes($_POST["db_database"]));
		$newdboptions['db_username'] = strip_tags(stripslashes($_POST["db_username"]));
		$newdboptions['db_password'] = strip_tags(stripslashes($_POST["db_password"]));
		$newdboptions['db_table_prefix'] = strip_tags(stripslashes($_POST["db_table_prefix"]));
		
		// Validate options
		$dboptions['gallery_url'] = $newdboptions['gallery_url'];
		
		// Check connection
		$dbh = mysql_connect($newdboptions['db_server'], 
		                     $newdboptions['db_username'], 
		                     $newdboptions['db_password']);
		if (!$dbh) {
			$connect_error = true;
			$connect_msg   = mysql_error();
		} else {
			// These options are okay
			$dboptions['db_server']   = $newdboptions['db_server'];
			$dboptions['db_username'] = $newdboptions['db_username'];
			$dboptions['db_password'] = $newdboptions['db_password'];
			
			// Check we have access to the database
			if (!mysql_select_db($newdboptions['db_database'], $dbh)) {
				$use_error = true;
				$use_msg   = mysql_error();
			} else {
				// This option is okay
				$dboptions['db_database'] = $newdboptions['db_database'];
				
				// Check the table prefix is okay
				$result = mysql_query('SELECT * FROM '.$newdboptions['db_table_prefix'].'pictures LIMIT 1;');
				if (!$result) {
					$table_error = true;
					$table_msg   = mysql_error();
				} else {
					mysql_free_result($result);
							
					// Update database options
					$newdboptions['installed'] = true;
					update_option('wp_cpg_plugin_db_default', $newdboptions);
					
					// Update gallery options
					$options['installed'] = true;
					update_option('wp_cpg_plugin_options', $options);
					
					// Show the options
					wp_cpg_plugin_display_options($options);
					return;
				}
			}
		}
	}
	
	if ($connect_error) {
		echo '<div><h2>Failed to connect to database server!</h2>';
		echo '<p>This probably means that the server name or user name or password are incorrect! ';
		echo 'The server returned the error displayed below:</p>';
		echo '<p>'.$connect_msg.'</p></div>';
	} else if ($use_error) {
		echo '<div><h2>Failed to connect to database!</h2>';
		echo '<p>This probably means that the database name or your user does not have access privileges! ';
		echo 'The server returned the error displayed below:</p>';
		echo '<p>'.$use_msg.'</p></div>';
	} else if ($table_error) {
		echo '<div><h2>Failed to read from table!</h2>';
		echo '<p>This probably means that table prefix string is incorrect! ';
		echo 'The server returned the error displayed below:</p>';
		echo '<p>'.$use_msg.'</p></div>';
	}
	
	// Start default gallery details form
	echo '<form name="default_gallery" method="post" action="'.get_bloginfo('wpurl').'/wp-admin/options-general.php?page=wp-cpg-widget/wp-cpg-plugin.php" onsubmit="wp_cpg_validate();">';
	echo "<div class=\"wrap\"><h2>WP Coppermine Default Gallery Options</h2>";
	echo '<table class="form-table">';

	// Database server
	echo '<tr valign="top"><th scope="row">Gallery URL</th>';
	echo '<td><input type="text" name="gallery_url" value="'.$dboptions['gallery_url'].'" size="60"></input>';
	echo '<br />The URL for the Coppermine Gallery.</td></tr>';
	
	// Database server
	echo '<tr valign="top"><th scope="row">Database server</th>';
	echo '<td><input type="text" name="db_server" value="'.$dboptions['db_server'].'" size="60"></input>';
	echo '<br />The domain name for the Coppermine database server.</td></tr>';
	
	// Database name
	echo '<tr valign="top"><th scope="row">Database name</th>';
	echo '<td><input type="text" name="db_database" value="'.$dboptions['db_database'].'" size="60"></input>';
	echo '<br />The name of the Coppermine database.</td></tr>';
	
	// Database user name
	echo '<tr valign="top"><th scope="row">The user name</th>';
	echo '<td><input type="text" name="db_username" value="'.$dboptions['db_username'].'" size="60"></input>';
	echo '<br />The Coppermine database user name.</td></tr>';
	
	// Database user name
	echo '<tr valign="top"><th scope="row">The password</th>';
	echo '<td><input type="password" name="db_password" value="'.$dboptions['db_password'].'" size="60"></input>';
	echo '<br />The Coppermine database user password.</td></tr>';

	// Table name prefix
	echo '<tr valign="top"><th scope="row">The table prefix</th>';
	echo '<td><input type="text" name="db_table_prefix" value="'.$dboptions['db_table_prefix'].'" size="60"></input>';
	echo '<br />The prefix to the Coppermine table names.</td></tr>';
	
	// Close form
	echo '</table>';
	echo '<input type="hidden" name="wp_cpg_options_submit" value="true"></input>';
	echo '<p class="submit"><input type="submit" value="Save"></input></p>';
	echo "</div>";
	echo '</form>';
}

/**
 * Display the plugin's option page.
 *
 * This function display's the plugin's options page when the appropriate
 * 'tab' in Wordpress' settings page is selected.  If any of the submit
 * buttons have been pressed the options are updated appropriately.
 * 
 * @since 0.01
 */
function wp_cpg_plugin_options() {
	// Get the plugin options and gallery names
	$options = get_option('wp_cpg_plugin_options');
	if (!$options['installed']) {
		wp_cpg_plugin_install_options($options);
	} else {
		wp_cpg_plugin_display_options($options);
	}
}

// ADD THE ACTIONS

// Add the options pages to the admin menu
add_action('admin_menu', 'wp_cpg_plugin_add_pages');

// Initialisation
register_activation_hook( __FILE__, 'wp_cpg_plugin_activate');

// Uninitialisation
register_deactivation_hook( __FILE__, 'wp_cpg_plugin_deactivate');

?>
