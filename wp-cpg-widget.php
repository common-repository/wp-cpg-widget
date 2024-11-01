<?php

/*
    wp-cpg-widget.php
	
    The widget rendering and control functions

    Copyright 2009, Melanie Rhianna Lewis

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty ofq
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once('cpg_database.php');

/**
 * The maximum length of an album name in the widget options UI.
 * 
 * @var int maximum length of an album name.
 */
define(WP_CPG_MAX_ALBUM_NAME_LEN, 30);

/**
 * The album ID that represents all albums in the gallery.
 * 
 * @var int album id.
 */
define(WP_CPG_ALL_ALBUMS_AID, -1);


/**
 * Generates the HTML for the head of the page.
 * 
 * The function generates the HTML to include the plugin's style sheet and
 * the plugin's javascript file.  A theme can provide an alternative style
 * sheet for the plugin which will be used instead of the default one.
 * 
 * @since 0.02
 */
function wp_cpg_widget_head() {
	$siteurl = get_option('siteurl');
	
	if (isset($siteurl)) {
		$cssurl = $siteurl.'/wp-content/plugins/wp-cpg-widget/wp-cpg-plugin.css';
		$jsurl  = $siteurl.'/wp-content/plugins/wp-cpg-widget/wp-cpg-widget.js';
	}
	
	if (file_exists(TEMPLATEPATH.'/wp-cpg-plugin.css')) {
		$cssurl = get_bloginfo('template_url').'/wp-cpg-plugin.css';
	}
	
	echo "\n";
	if (isset($cssurl)) {
		echo '<link rel="stylesheet" href="'.$cssurl.'" type="text/css" media="screen" />';
	}
	echo "\n";
	if (isset($jsurl)) {
		echo '<script type="text/javascript" src="'.$jsurl.'"></script>';
	}
	echo "\n";
}

/**
 * Generates the HTML content for the widget.
 * 
 * The function generates the HTML content for the widget.  It uses the 
 * widget options to get the appropriate database details and query the 
 * database to generate a matrix of images.
 * 
 * @since 0.01
 */
function wp_cpg_widget_content() {
	// Get the widget options
	$widgetoptions = get_option('wp_cpg_widget_options');
	// Get the gallery as set in the widget options
	$dboptions = get_option('wp_cpg_plugin_db_'.$widgetoptions['gallery']);
	
	$url    = $dboptions['gallery_url'];
	$title  = $widgetoptions['title'];
	$width  = $widgetoptions['width'];
	$height = $widgetoptions['height'];
	$qtype  = $widgetoptions['qtype'];
	$album  = $widgetoptions['album'];
	$count  = $width * $height;
	
	if ($qtype == "random") {
		$query  = 'SELECT aid, filepath, filename, title, caption '.
				  'FROM '.$dboptions['db_table_prefix'].'pictures '.
		          ($album != WP_CPG_ALL_ALBUMS_AID ? 'WHERE aid='.$album : '').
		          ' ORDER BY RAND() LIMIT '.$count.' ';
	} else
	if ($qtype == "latest") {
		$query  = 'SELECT aid, filepath, filename, title, caption '.
				  'FROM '.$dboptions['db_table_prefix'].'pictures '.
		          ($album != WP_CPG_ALL_ALBUMS_AID ? 'WHERE aid='.$album : '').
		          ' ORDER BY pid DESC LIMIT '.$count.' ';
	}
	
	$cpgdb = new CPG_Database($dboptions['db_server'], 
	                          $dboptions['db_database'], 
	                          $dboptions['db_username'],
	                          $dboptions['db_password']);
	if ($cpgdb) {
		if ($cpgdb->hasError()) {
			$content = $cpgdb->getError();
		} else {
			$rows = $cpgdb->query($query);
			if (!$rows) {
				$content = $cpgdb->getError();
			} else {
				$idNum = 0;
				
				$content = '<div class="wp-cpg-widget"><table class="wp-cpg-widget">'."\n";
				
				for ($i = 0; $i < count($rows); $i = $i + $width) {
					$content = $content.'<tr class="wp-cpg-widget">';
					for ($j = 0; $j < $width; $j ++) {
						if (($i + $j) < count($rows)) {
							$row     = $rows[$i + $j];
							$content = $content.'<td class="wp-cpg-widget">'."\n".
							    '<a href="'.$url.'/thumbnails.php?album='.$row['aid'].'" class="wp-cpg-widget">'.
							    '<img src="'.$url.'/albums/'.$row['filepath'].'thumb_'.$row['filename'].'" '.
							    'alt="'.$row['title'].'" title="'.$row['title'].'" class="wp-cpg-widget" '.
							    'id="wp-cpg-img-'.$idNum.'" /></a>'."\n".
							    '</td>'."\n";
						}
						$idNum ++;
					}
					$content = $content.'</tr>'."\n";
					
				}
						
				$content = $content.'</table></div>'."\n".'<script type="text/javascript">'."\n";
				
				for ($idNum = 0; $idNum < count($rows); $idNum++) {
					$row     = $rows[$idNum];
					$content = $content.'wp_cpg_initPopUpImage("wp-cpg-img-'.$idNum.'","'.$url.'/albums/'.$row['filepath'].'normal_'.$row['filename'].'","'.$row['caption'].'");'."\n";
				}
				
				$content = $content.'</script>'."\n";
			}
		}
	} else {
		$content = 'Error creating DB object';
	}
		
	return $content;
}

/**
 * Initialises the widget.
 * 
 * The function initialises the widget registering a widget rendering
 * function and a widget options control function with Wordpress.
 * 
 * @since 0.01
 */
function wp_cpg_widget_init() {
	// Check that widget support exists
	if (!function_exists('register_sidebar_widget'))
		return;

	/**
	 * Creates the HTML for the widget.
	 * 
	 * The function creates the HTML for the widget which comprises part
	 * of the page of the blog.
	 * 
	 * @since 0.01
	 * 
	 * @param $args The widget arguments passed by coppermine.
	 */
	function wp_cpg_widget($args) {
		extract($args);
		
		// Get the widget options
		$widgetoptions = get_option('wp_cpg_widget_options');
	?>
<div id="wp-cpg-popup"></div>
<?php echo $before_widget; ?>
<?php echo $before_title . $widgetoptions['title'] . $after_title; ?>
<?php echo wp_cpg_widget_content(); ?>
<?php echo $after_widget; ?>
	<?php
	}
	
	/**
	 * Returns an array of all the albums for a gallery.
	 * 
	 * The function returns an array of all the albums for a gallery.  It
	 * returns an array of associative arrays comprising 'aid', 'title'
	 * and 'description' for the gallery.  It returns false if the query
	 * failed.
	 * 
	 * @since 0.05
	 * 
	 * @param $dbprefix The database prefix string.
	 * 
	 * @return bool|array An array of albums or false.
	 */
	function wp_cpg_widget_getalbums($dboptions) {
		$cpgdb = new CPG_Database($dboptions['db_server'], 
		                          $dboptions['db_database'], 
		                          $dboptions['db_username'],
		                          $dboptions['db_password']);
		if ($cpgdb) {
			if ($cpgdb->hasError()) {
				return false;
			} else {
				$rows = $cpgdb->query('SELECT aid,title FROM '.$dboptions['db_table_prefix'].'albums;');
				if (!$rows) {
					return false;
				} else {
					return $rows;
				}
			}
		}
	}
	
	/**
	 * Returns an HTML SELECT element listing all the albums in a gallery.
	 * 
	 * The function returns a string comprising the HTML required to display
	 * a SELECT element listing all the albums in a gallery.  It includes
	 * 'ALL' at the top of the list and will display, as selected, the one
	 * selected in the options.  It returns false on error.
	 * 
	 * @since 0.05
	 * 
	 * @param $options The widget options.
	 * @param $dboptions The gallery database options.
	 * 
	 * @return string An HTML SELECT as a string.
	 */
	function wp_cpg_widget_generateAlbumSelect($options, $dboptions) {
		$rows = wp_cpg_widget_getalbums($dboptions);
		$html = '<p><label for="wp_cpg_widget_album">Albums'.
		    '<select  class="widefat" id="wp_cpg_widget_album" name="wp_cpg_widget_album">'.
		    '<option value="'.WP_CPG_ALL_ALBUMS_AID.'" label="ALL" '.
		    ($options['album'] == -1 ? 'selected' : '').'>ALL</option>';
		
		if ($rows) {
			for ($i = 0; $i < count($rows); $i++) {
				$row   = $rows[$i];
				$title = $row['title'];
				
				if (strlen($title) > WP_CPG_MAX_ALBUM_NAME_LEN) {
					$title = substr($title, 0, WP_CPG_MAX_ALBUM_NAME_LEN).'...';
				}
				
				$html  = $html.'<option value="'.$row['aid'].'" label="'.$title.'" '.
				    ($options['album'] == $row['aid'] ? 'selected' : '').'>'.
				    $title.'</option>';
			}
		}
		
		return $html.'</select></p>';
	}
	
	/**
	 * Displays and handles the widget options
	 * 
	 * The function displays and handles changes in the widget options.
	 * Currently this is only the title but it will be expanded in 
	 * subsequent versions.
	 * 
	 * @since 0.01
	 */
	function wp_cpg_widget_control() {
		// Get the widget options
		$options = $newoptions = get_option('wp_cpg_widget_options');
		// Get the gallery as set in the widget options
		$dboptions = get_option('wp_cpg_plugin_db_'.$options['gallery']);
		
		if ($_POST['wp_cpg_widget_submit']) {
			$width  = strip_tags(stripslashes($_POST['wp_cpg_widget_width']));
			$height = strip_tags(stripslashes($_POST['wp_cpg_widget_height']));
			
			$newoptions['title'] = strip_tags(stripslashes($_POST['wp_cpg_widget_title']));
			$newoptions['qtype'] = strip_tags(stripslashes($_POST['wp_cpg_widget_qtype']));
			$newoptions['album'] = strip_tags(stripslashes($_POST['wp_cpg_widget_album']));
			
			// Check for empty / silly row and column values.
			$newoptions['width']  = $width > 0 ? $width   : 1;
			$newoptions['height'] = $height > 0 ? $height : 1;
		}
		
		if ($options != $newoptions) {
			$options = $newoptions;
			update_option('wp_cpg_widget_options', $options);
		}
		
		$albumSelect = wp_cpg_widget_generateAlbumSelect($options, $dboptions);
		
		echo '<p><label for="wp_cpg_widget_title">Title<input class="widefat" id="wp_cpg_widget_title" ';
		echo 'name="wp_cpg_widget_title" type="text" value="'.$options['title'].'" /></label></p>';
		echo '<p><label for="wp_cpg_widget_width">Columns<input class="widefat" id="wp_cpg_widget_width" ';
		echo 'name="wp_cpg_widget_width" type="text" value="'.$options['width'].'" /></label></p>';
		echo '<p><label for="wp_cpg_widget_height">Rows<input class="widefat" id="wp_cpg_widget_height" ';
		echo 'name="wp_cpg_widget_height" type="text" value="'.$options['height'].'" /></label></p>';
		echo '<p><label for="wp_cpg_widget_qtype">Select images...<select class="widefat" id="wp_cpg_widget_qtype" ';
		echo 'name="wp_cpg_widget_qtype">';
		echo '<option value="latest" label="Latest" '.($options['qtype'] == 'latest' ? 'selected' : '').'>';
		echo 'Latest images added</option>';
		echo '<option value="random" label="Randomly" '.($options['qtype'] == 'random' ? 'selected' : '').'>';
		echo 'Random images</option>';
		echo '</select><p>';
		
		echo $albumSelect;
		
		echo '<input type="hidden" id="wp_cpg_widget_submit" name="wp_cpg_widget_submit" value="true" />';
	}

	register_sidebar_widget('WP Coppermine', wp_cpg_widget);
	register_widget_control('WP Coppermine', wp_cpg_widget_control);
}

// ADD THE ACTIONS

// Delay plugin execution until sidebar is loaded
add_action('widgets_init', 'wp_cpg_widget_init');
// Function to add style sheet to the blog page
add_action('wp_head', 'wp_cpg_widget_head');
?>
