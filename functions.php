<?php

function blanktheme_theme_support() {
    add_action('rest_api_init', 'register_rest_all_images');
    add_action('rest_api_init', 'register_rest_images');


    // ADD FEATURED IMAGE FROM POST TO THE FIELD fimg_url in REST
    function register_rest_images()
    {
        register_rest_field(array('post'),
            'fimg_url',
            array(
                'get_callback' => 'get_rest_featured_image',
                'update_callback' => null,
                'schema' => null,
            )
        );
    }

    function get_rest_featured_image($object, $field_name, $request)
    {
        if ($object['featured_media']) {
            $img = wp_get_attachment_image_src($object['featured_media'], 'app-thumb');
            return $img[0];
        }
        return false;
    }



    // ADD ALL ATTACHED IMAGES FROM POST TO THE FIELD all_images in REST
    function register_rest_all_images()
    {
        register_rest_field(array('post'),
            'all_images',
            array(
                'get_callback' => 'get_images_from_post',
                'update_callback' => null,
                'schema' => null,
            )
        );
    }

    function get_images_from_post() {
        global $post;
        $id = intval( $post->ID );
        $size = 'medium';
        $attachments = get_children( array(
            'post_parent' => $id,
            'post_status' => 'inherit',
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'order' => 'ASC',
            'orderby' => 'menu_order'
        ) );

        return $attachments;
    }
}
function blanktheme_post_thumbnails()
{
    add_theme_support('post-thumbnails');
}

add_action('after_setup_theme', 'blanktheme_post_thumbnails');

add_action( 'after_setup_theme', 'blanktheme_theme_support' );

?>