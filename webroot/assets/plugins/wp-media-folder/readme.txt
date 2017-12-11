=== Media test ===
Tags: media, folder
Requires at least: 3.5.1
Tested up to: 4.6.0
Stable tag: 3.8.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WP media Folder is a WordPress plugin that enhance the WordPress media manager by adding a folder manager inside.

== Description ==

If you were struggling with files and you didn't know how to organize them... 
It's over! With WP Media Folder life is easy, you can manage files, images from the native Wordpress media manager. 
YES, It's true! We did in the media manager of WordPress a file manager where you can drag and drop images and files so easely. 
I can not tell more just watch our demo and please try it to make your own idea.

Stop searching for an image through thousand of media, just navigate like you do on your desktop file manager.

= Changelog = 

= 3.8.6 =
Media rename don't apply
Replace PNG file keep transparency background
Auto insert image in folder feature

= 3.8.5 =
SQL get count post
Undefined function get_userdata error

= 3.8.4 =
Import meta size and file type on large image
Some folder not displayed in tree folder
Duplicate folder when double click on the folder tree

= 3.8.3 =
Compatibility with WordPress theme customizer
Open PDF file new window

= 3.8.2 =
User permissions not correctly checked

= 3.8.1 =
Add cloud configuration documentation link in settings
JoomUnited updater compatible with new WordPress 4.6 shiny updates

= 3.8.0 =
Embed pdf from media library option
Add settings to connect Google Drive and Dropbox (for the addon)
Speed optimization
Duplicate replace button when saving parameters

= 3.7.0 =
ImageRecycle Image compression integration in parameters
Lightbox on single image, as an option
Display the direct number files in a folder
Conflict with CFS image plugin (Custom Field Suite)

= 3.6.0 =
Option to disable by default the JS called on frontend (for frontend page builders)
Whole code optimization regarding plugin performance
SQL query optimization regarding plugin performance
Sanitize all elements prints on frontend (XSS)
Update folder and tree design
Drag and drop when edit multiple selection
Button insert link not working on some specific configuration
Masonry gallery display on small screen size
Duplicate folder when using an access restriction by user role

= 3.5.6 =
Conflict with Autoptimize plugin
Display folder tree in custom media frame
Load script in page table on multiple site

= 3.5.5 =
Clean CSS & JS on frontend
CSS layout when filter and ordering feature is disabled
Display folder per user
Server folder import with uppercase file extension

= 3.5.4 =
Setting Animation for slider gallery
Compatiblility with Cornerstone plugin
Install / blank white screen

= 3.5.3 =
Load jQuery on frontend to be conpatible with public side edition plugins
Compatiblity with WP Sweep plugin
Make WPMF work with all plugins that use Media Library in front-end

= 3.5.2 =
Make media folder work with svg images
Display limitation of post and folder by user role
Remove filter wp_generate_attachment_metadata when regenerate thumbnail

= 3.5.1 =
fix FTP Import doesn't show directories

= 3.5.0 =
Media access: limit access by user role (a folder per user role)
Possibility to duplicate a media
Possibility drag'n drop a media in the current folder from desktop
Possibility to replace all file types, not just images (zip, pdf...)
Compatibility/work with with ACF
Compatibility/work with Beaver builder
Compatibility/work with Site Origine page builder
Compatibility/work with Themify builder
Compatibility/work with Live composer page builder
fix sync media and import ftp with file name has special characters
compatibility with Beaver Builder , Live composer page builder ...
replace other file than image

= 3.4.2 =
Fix image conflict style with YoImages plugin
unbind click when drag folder
update langguages

= 3.4.1 =
Fix image replacer

= 3.4.0 =
Regenerate thumbnails tool in parameters
Add process bar when use FTP import, allow massive import
Sync external media
Sort images by title and date in gallery
DIVI builder compatibility
Remove css background for image replacement

= 3.3.6 =
FTP import
Folder stay opened when called from multiple media views

= 3.3.5 =
Update file size when file replace is complete
Portfolio theme JS wrong calculation when resizing the screen

= 3.3.4 =
conflict with RokSprocket plugin
conflict with WP Table Manager plugin

= 3.3.3 =
fix error when active plugin on multisite
fix conflict with Gleam theme
fix conflict with retina 2x plugin

= 3.3.1 =
Update filter layout to fit new WP 4.4 admin CSS
Portfolio gallery style is not loading proper thumbnail size
Clean CSS & JS from portfolio gallery theme
Update Material-Design-Iconic-Font
Use current_user_can to check user rights for importer from FTP

= 3.3.0 =
Rename file on upload with a pattern
Remove a folder with all it's media inside (as an option)
File insertion, remove file on clicking on the cross
Gallery lightbox going to top of the screen

= 3.2.0 =
Search option to search in current folder or in the whole media library
Possibility to setup an image as folder cover
.pot laguage file for translators

= 3.1.0 =
Single file insertion design

= 3.0.7 =
AJAX automatic reload
Get url lightbox not work
Register taxonomy in back-end and front-end

= 3.0.6 =
Include the automatic updater

= 3.0.5 =
New file type in import tool
Defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );
Remove file github=checked.php
Warning and get role
Change general settings title

= 3.0.4 =
New file type in import tool
Search attachment on all folders function
Same variable name
Optimize code when active plugin

= 3.0.3 =
duplicate #jao
js conflict with wp-table-manager plugin

= 3.0.2 =
.js error when adding media into post

= 3.0.1 =
WordPress 4.3 compatibility
Compatibility with plugin with WPML plugin
Slider @ single column don't load the good image size
Image disappear when using the bulk select
Upload file to folder in list view
Check page when using move_file

= 3.0.0 =
Import media and folder structure from folder/sub-folder from your server
Style settings in 4 tabs
Image size not selected properly in masonry theme
Single Jquery load
Style gallery conflict with WPML plugin
Set 'wpmf-category' is default

= 2.4.1 =
Error script and performance
Auto insert gallery from folder
Update title when replace image
Auto insert gallery from folder
Style in screen ipad
Import nextgen gallery

= 2.4.0 =
Possibility to override a media with another one (replace media)
Move a parent folder into one of its subfolders
Change name $_SESSION['child'] to $_SESSION['wpmf_child']
Conflict style with Advanced Custom Fields plugin

= 2.3.0 =
Possibility to drag'n drop media in left column folder tree
Style broken in right to left language
Enqueue style gallery when the gallery is not empty
Change image on hover
Error in the french file

= 2.2.0 =
Media filtering by image dimension
Filtering by media type (zip, image, pdf,...)
Media filtering by media weight
Define custom weight and dimension to be applied in media filtering
Small and large view of media
Sorting folders by name and ID
Sorting media by date
Sorting media by title
Sorting media by size
Sorting media by file type
Save user sorting and ordering using cookies
Possibility to disable the feature
Spanish and German languages

= 2.1.0 =
Localization standard files (English and french included)

= 2.0.0 =
Own media display restriction
Admin option to filter own media with session
Firefox display
Default gallery theme broken in some themes
Alert display when create same folder with same name

= 1.3.1 =
Use backbone js to create progress bar when upload attachment
Style conflict with enhanced media library pro
Error : images after upload vanished
JS conflict MailPoet Plugin
Reset query when delete folder
Support right to left language
Use $wpdb->prefix.'table_name' instead use wp_ prefix
Sanitize sql function
Slider theme disappear when select size = 'large' or 'fullsize'

= 1.3.0 =
NextGEN gallery importer
Change config text and add NextGEN sync button

= 1.2.1 =
Possibility to disable gallery feature
Use svg icon for button next and prev
Theme conflict WP Latest Posts plugin
Random order selected by default
Custom link in gallery broken
Custom _blank link in portfolio gallery
When lightbox open , double click to load next/previous image in portfolio theme
Random order is broken when active Advanced Custom Fields plugin
Auto insert image from folder in Page

= 1.2.0 =
Gallery function: masonry
Gallery function: portfolio
Gallery function: slider
Override default WordPress gallery function with new parameters and lightbox
Parameter view for custom image size choice
Parameter for gallery display

= 1.1.3 =
WordPress 4.2 compatibility, in some case only folders are loaded, not images

= 1.1.2 =
Progress bar disappear on image upload
Date filter disappear in the media popup from an article

= 1.1.1 =
JS and CSS compatibility with theme builder

= 1.1.0 =
Folder tree on left part

= 1.0.3 to 1.0.4 =
JS error and style

= 1.0.2 =
Custom taxonomy for folder
Import post into new categories
JS error on post page which are not articles or posts or pages

= 1.0.1 =
Fix backend display, the folder are going over media parameters

= 1.0.0 =
Initial release version 

