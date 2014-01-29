WP plug-in for custom image size quality
========================================

Adds the ability to programmatically specify the JPG quality of individual custom image sizes in WordPress.

Perfect for keeping the filesize of retina images down, among other things.

Usage
-----

Typically, you would use this code for defining custom image sizes:

	add_image_size( 'custom-size', 500, 300, true );

This plug-in lets you do this:

	set_image_size_quality( 'custom-size', 40 );

Installation
------------

Just copy the whole "custom-image-size-quality" folder into your "wp-content/plugins" folder. Then activate the plug-in in wp-admin as usual.

Credits
-------

I found the hook approach at this informative URL:

http://wordpress.stackexchange.com/questions/74103/set-jpeg-compression-for-specific-custom-image-sizes

I then prettied it up and formed a plugin out of it. Modified the scaling to use WP's own Image Editor functionality, which will then use GD or ImageMagick (or other custom editor) based on availability and preferences.

Comments and suggestions
------------------------

Why don't you just send those to erik@stickybeat.se?
