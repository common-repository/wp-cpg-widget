/*
	Javascript support for WP Coppermine Widget

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

/**
 * The WP_CPG_Widget class comprises a bundle of global property values and 
 * methods.  They are encapsulated in a class for management and namespacing
 * reasons.  There will only ever be one instance of the class.
 * 
 * @since 0.05 (with this name).
 */
function WP_CPG_Widget() {
	/**
	 * The id for the popup
	 */ 
	this.popupid = 'wp-cpg-popup';

	/**
	 * The timeout value (1 second)
	 */
	this.timeout = 1000;
	
	/**
	 * The current timeout event.  null means no current timeout.
	 */
	this.timeoutevt = null;
	
	/**
	 * Returns the popup element.
	 * 
	 * This method returns the DOM element that comprises the pop-up DIV.
	 * 
	 * @return the element that comprises the pop-up DIV.
	 */
	this.getPopupElement = function() {
		return document.getElementById(this.popupid);
	}
	
	/**
	 * Arms the timeout.
	 * 
	 * This method arms the timeout function so that it calls the specified
	 * callback function after the timeout.  This is used to hide the popup
	 * after a specified period of inactivity.
	 * 
	 * @param func The timeout callback function
	 */
	this.armTimeout = function(func) {
		this.timeoutevt = setTimeout(func, this.timeout);
	}
	
	/**
	 * Cancels the timeout.
	 * 
	 * This method cancels an outstanding timeout and clears the event value.
	 */
	this.cancelTimeout = function() {
		if (this.timeoutevt) {
			clearTimeout(this.timeoutevt);
			this.timeoutevt = null;
		}
	}
	
	/**
	 * Clears the timeout event.
	 * 
	 * This method clears an outstanding timeout event.  It does not cancel
	 * the event (call cancelTimeout() for that).  Just clears the event value.  
	 * This method should be called in a timeout callback.
	 */
	this.clearTimeout = function() {
		this.timeoutevt = null;
	}
}

/**
 * Hides an image popup.
 * 
 * This function hides an image popup 'window'.  Typically the method is 
 * passed as an argument to the setTimeout() function and called after the
 * timeout period has passed.
 * 
 * @since 0.02
 */
function wp_cpg_popUpCallback() {
	// Hide the popup
	wp_cpg_widget.getPopupElement().setAttribute('style','visibility: hidden;');
	// Reset the timeout
	wp_cpg_widget.clearTimeout();
}

/**
 * Shows an image popup.
 * 
 * This function shows an image popup 'window'.  The position of the popup is
 * relative to the pointer position.  Typically the method is used as a
 * onmouseover event handler.
 * 
 * @since 0.02
 * 
 * @param e The mouseover event (or null on Microsoft).
 * 
 * @return false
 */
function wp_cpg_popUpMouseOver(e) {
	// Handle Microsoft's window.event system
	var evt = (e) ? (e) : (window.event);
	
	// Get the div that is to be our popup
	var popup = wp_cpg_widget.getPopupElement();
	
	// Clear any outstanding timeout
	wp_cpg_widget.cancelTimeout();

	// Render the popup
	popup.innerHTML = this.toHTML();
	
	// Calculate the offsets
	var off_left = Math.round(this.normalImg.width / 2);
	var off_top  = Math.round(this.normalImg.height / 2);
	
	// Show the popup
	popup.setAttribute('style','visibility:visible;' +
			                   'left:50%;' + 
			                   'top:50%;' +
			                   'margin-left:' + (-off_left) + 'px;' +
			                   'margin-top:' + (-off_top) + 'px');
		
	// Prevent bubbing of events.
	return false;
}

/**
 * Sets a timer to hide the image popup after a timeout period.
 * 
 * The function sets a timer to hide the image popup after a timeout period.
 * Typically this method is used as the mouseout event handler.
 * 
 * @since 0.02
 */
function wp_cpg_popUpMouseOut() {
	wp_cpg_widget.armTimeout(wp_cpg_popUpCallback);
}

/**
 * Returns the HTML for the image popup.
 * 
 * The function returns the HTML content for the image popup based upon the
 * properties of the associated object, typically an image.
 * 
 * @since 0.02
 * 
 * @return an HTML string.
 */
function wp_cpg_toHTML() {
	var html = '<div id="wp-cpg-popup-border"><div id="wp-cpg-popup-content">';
	
	html += '<img src="'+ this.normalImg.src + '" id="wp-cpg-popup-img" ';
	if (this.title != '') {
		html += 'alt="' + this.title + '" title="' + this.title + '"';
	}
	html += ' />';
	if (this.title != '') {
		html += '<h1 id="wp-cpg-popup-title">' + this.title + '</h1>';
	}	
	if (this.caption && this.caption != '') {
		html += '<p id="wp-cpg-popup-caption">' + this.caption + '</p>';
	}
	
	return html + '</div></div>';
}

/**
 * Initialises an image object for the image popup.
 * 
 * The function initialise an image object for the image popup.  The function
 * extends the image object with the specified id with properties that
 * comprise the specified 'normal' image and caption value.  It also adds
 * the wp_cpg_toHTML() as the toHTML() method; and wp_cpg_popUpMouseOver() and
 * wp_cpg_popUpMouseOut() as the onmouseover and onmouseout event handlers.
 * 
 * @since 0.02
 * 
 * @param id The image id.
 * @param normalSrc The URL for the 'normal' version of the image.
 * @param caption The verbose description of the image.
 */
function wp_cpg_initPopUpImage(id, normalSrc, caption) {
	// Get the image element
	var img = document.getElementById(id);
	if (img) {
		// Create a new image object and set the source to be normalSrc
		// This will cache the 'normal' image for us.
		img.normalImg	  = new Image();
		img.normalImg.src = normalSrc;
		// The detailed description
		img.caption       = caption;
		// Additional methods
		img.toHTML        = wp_cpg_toHTML;
		img.onmouseover   = wp_cpg_popUpMouseOver;
		img.onmouseout    = wp_cpg_popUpMouseOut;
	}
}

/**
 * The global instance of WP_CPG_Widget().
 */
var wp_cpg_widget = new WP_CPG_Widget();
