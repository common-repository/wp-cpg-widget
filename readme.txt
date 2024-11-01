=== Plugin Name ===
Contributors: Cyberspice
Donate link: http://www.cyberspice.org.uk/blog/wordpress-coppermine-widget/
Tags: coppermine, widget, plugin
Requires at least: 2.7.0
Tested up to: 2.7.1
Stable tag: 1.0.2

A Wordpress plug-in that supplies a widget that displays configurable images
from a Coppermine Gallery and pops up larger versions on mouse over.

== Description ==

This Wordpress plug-in supplies a widget that will display a configurable
number of images, in a configurable grid, selected in a configurable way, from 
a Coppermine Gallery.  When the pointer is placed over an image a larger 
version of the image will be displayed in a pop-up together with any title and 
description.

The plug-in supplies a settings page which allows the user to enter the 
Coppermine database details.  The widget is a fully compliant Wordpress widget
and can be configured using a form on the widget's appearance page.  

On the appearance form you can choose to select how many images to display and 
the shape of grid in which to didsplay them.  You can choose whether to 
select random, or the latest images, from either all of the images or a 
specific album.  You can also enter the text to display as the widget title.

Further details can be found at: 

http://www.cyberspice.org.uk/blog/wordpress-coppermine-widget/

== Installation ==

Installation is relatively simple and similar to other plug-ins and widgets.

1. Unzip `wp-cpg-widget.zip` in to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. In the 'Settings' menu select 'WP Coppermine' and enter the Coppermine web
server and database details on its setting page.  The settings will be 
validated but can't be edited once set.  You can reset the plug-in by
de-activating and then re-activating it in the 'Plugins' menu.
4. In the 'Appearance' menu select 'Widgets' page add to the widget to your 
sidebar(s) and use the edit form to enter the widget title text, select the 
grid layout, and the how to select images.

And that's it.

== Frequently Asked Questions ==

= How do I change the Coppermine server settings? =

At the moment the only way to change the settings is to de-activate the
plug-in (which will erase all plug-in settings) and then re-activate it.

= How do I change the style of the pop up or widget? =

If the widgets appearance form is insufficient you can edit the widget 
style sheet (wp-cpg-widget.css in the plug-in directory).  The widget
includes the style sheet as part of the blog page header.

= How is the pop-up displayed? =

The pop-up is a DIV element which is part of the widget HTML.  Normally
the DIV is hidden but its appearance and content is displayed by JavaScript
supplied by the file wp-cpg-widget.js.  The widget includes the JavaScript
as part of the blog page header.

== Screenshots ==

1. An example of the widget in use on a blog.  This is my blog at
http://www.cyberspice.org.uk/blog/
2. The plug-in settings page.  The plug-in will attempt to connect to the
server before accepting the settings.
3. The widget appearance page.

== History ==

= Revision 1.0 =

Initial version

= Revision 1.0.1 =

Bug fix - Fixes issue with pathing of style sheet and javascript files
for inclusion in to the blog page header.

= Revision 1.0.2 =

Bug fix - Use of PHP 5.x 'private' keyword changed to 'var' for PHP 4.x
compatibility.

Bug fix - Fixed FORM path when configuring the gallery database details
in settings.

Bug fix - Removed errant space from end of file that caused PHP warnings
displayed in the browser.

(Last update 19 May 2009)

