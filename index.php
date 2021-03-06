<?php

/*
Plugin Name: Custom Image Size Quality
Plugin URI: https://github.com/StickyBeat/custom-image-size-quality
Description: Adds the ability to programmatically specify the JPG quality of individual custom image sizes in WordPress. Perfect for keeping the filesize of retina images down, among other things.
Version: 1.1
Author: Erik Gustavsson
Author URI: http://stickybeat.se
License: MIT
*/



function set_image_size_quality( $name, $quality ){

    global $_image_size_qualities;

    if( !isset( $_image_size_qualities ) ){

        #add_action('added_post_meta','check_size_quality_action', 10, 4 ); // old approach

        add_filter('update_post_metadata','check_size_quality_filter', 10, 5 ); // new approach

        $_image_size_qualities = array();
    }

    $_image_size_qualities[ $name ] = $quality;
}

function check_size_quality_filter( $null, $attach_id, $meta_key, $attach_meta, $prev_value ){

    check_size_quality( $meta_key, $attach_meta );
}

function check_size_quality_action( $meta_id, $attach_id, $meta_key, $attach_meta ){

    check_size_quality( $meta_key, $attach_meta );
}

function check_size_quality( $meta_key, $attach_meta ){

    if( $meta_key != '_wp_attachment_metadata' ){
        return;
    }

    $original_file = @$attach_meta['file'];

    if( !is_string( $original_file ) ){
        return;
    }

    $original_pathinfo = pathinfo( $original_file );

    $sizes = @$attach_meta['sizes'];

    if( !is_array( $sizes ) ){
        return;
    }

    $original_filename = $original_pathinfo['basename'];

    $upload_dir = wp_upload_dir();
    $upload_dir = $upload_dir['basedir'] . '/' . $original_pathinfo['dirname'];

    $original_path = $upload_dir.'/'.$original_filename;

    $image_editor = wp_get_image_editor( $original_path );

    if ( is_wp_error( $image_editor ) ){
        return;
    }

    global $_wp_additional_image_sizes;
    global $_image_size_qualities;


    $resizes_by_quality = array();

    foreach( $sizes as $size_name => $size ){

        $quality = $_image_size_qualities[ $size_name ];

        if( !$quality ){
            continue;
        }

        $size_mimetype = @$size['mime-type'];

        if( $size_mimetype != 'image/jpeg' ){
            continue;
        }

        $size_filename = @$size['file'];
        $size_path = $upload_dir.'/'.$size_filename;

        $width = $size['width'];
        $height = $size['height'];

        $crop = @$_wp_additional_image_sizes[ $size_name ]['crop'];
        $crop = ( $crop == true );

        if( !isset( $resizes_by_quality[ $quality ] ) ){
            $resizes_by_quality[ $quality ] = array();
        }

        $resizes_by_quality[ $quality ][] = array(
            'width' => $width,
            'height' => $height,
            'crop' => $crop,
            'path' => $size_path,
            );
    }

    foreach( $resizes_by_quality as $quality => $resizes ){

        $image_editor->set_quality( $quality );
        $image_editor->multi_resize( $resizes );
    }

}



