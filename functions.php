<?php

function blanktheme_theme_support() {
   
    /*Display  tag names*/ 
   add_action('rest_api_init', 'register_rest_custom_tag_names');
    function register_rest_custom_tag_names()
    {  register_rest_field(array('post'),
            'readable_tag_names',
            array(
                'get_callback' => 'get_custom_tag_names',
                'update_callback' => null,
                'schema' => null,
            )
        );
    }
   
    function get_custom_tag_names() {
        global $post;
        $readable_tags=[]; 
        $posttags = get_the_tags($post->ID);
        if ($posttags) {
        foreach($posttags as $tag) {
        array_push($readable_tags,$tag->name);
       }
      }
       return $readable_tags;
    }
        
    /* Custom Rest API */
     add_action('rest_api_init', 'register_rest_all_images2');
     add_action('rest_api_init', 'register_rest_images2');
    
    //FEAT IMG CALL
     function register_rest_images2()
    {
        register_rest_field(array('post'),
            'fimg_url2',
            array(
                'get_callback' => 'get_rest_featured_image2',
                'update_callback' => null,
                'schema' => null,
            )
        );
    }

    // FEAT IMG FNCT
    
    function get_rest_featured_image2()
    {
        global $post;
        $id = intval( $post->ID );
        if( has_post_thumbnail( $id ) ){
            $thum['thumbnail'] = get_the_post_thumbnail_url( $id, 'thumbnail' );
            $thum['medium'] = get_the_post_thumbnail_url( $id, "medium" );
            $thum['large'] = get_the_post_thumbnail_url( $id, "large" );
            $thum['medium_large'] = get_the_post_thumbnail_url( $id, "medium_large" );
            $thum['full'] = get_the_post_thumbnail_url( $id, "full" );
            return $thum;
        }
        else{
		      $custom_thumbnail = get_post_meta( $id, 'custom_thumbnail', true );
			   if ( $custom_thumbnail != "" ){
				   $thum['thumbnail'] = $thum['medium'] = $thumb['large'] = $thum['medium_large'] = $thum['full'] = $custom_thumbnail;
				   return $thum;
			   }
	     }
     return false; 
    }
    // ALL IMG CALL
    
    function register_rest_all_images2()
    {
        register_rest_field(array('post'),
            'all_images2',
            array(
                'get_callback' => 'get_images_from_post2',
                'update_callback' => null,
                'schema' => null,
            )
        );
    }
   
    // ALL IMG FNCT
  
 function get_images_from_post2() {
        global $post;
        $id = intval( $post->ID );
             $attachments = get_children( array(
            'post_parent' => $id,
            'post_status' => 'inherit',
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'order' => 'ASC',
            'orderby' => 'menu_order'
        ) );

        $images = array();
        $i=0;
        foreach($attachments as $image) {
            $images[$i]['thumbnail']=wp_get_attachment_image_url($image->ID, 'thumbnail');
            $images[$i]['medium']=wp_get_attachment_image_url($image->ID, 'medium');
            $images[$i]['full']=wp_get_attachment_image_url($image->ID, 'full');
            $images[$i]['large']=wp_get_attachment_image_url($image->ID, 'large');
            $images[$i]['medium_large']=wp_get_attachment_image_url($image->ID, 'medium_large');
            $i++;
        }

        return $images;
    }

   /* Vlad chiar spera ca nu a stricat nimic :)) */ 
    
    

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
        $size = 'large';
        $attachments = get_children( array(
            'post_parent' => $id,
            'post_status' => 'inherit',
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'order' => 'ASC',
            'orderby' => 'menu_order'
        ) );

        $images = array();

        foreach($attachments as $image) {
            array_push($images, wp_get_attachment_image_url($image->ID, $size));
        }

        return $images;
    }
}
function blanktheme_post_thumbnails()
{
    add_theme_support('post-thumbnails');
}

/*function add_cors_http_header(){
    header("Access-Control-Allow-Origin: *");
}
add_action('init','add_cors_http_header');*/
function add_custom_headers() {

    add_filter( 'rest_pre_serve_request', function( $value ) {
        header( 'Access-Control-Allow-Headers: Authorization, X-WP-Nonce,Content-Type, X-Requested-With');
        header( 'Access-Control-Allow-Origin: *' );
        header( 'Access-Control-Allow-Methods: GET' );
        header( 'Access-Control-Allow-Credentials: true' );

        return $value;
    } );
}
add_action( 'rest_api_init', 'add_custom_headers', 15 );

add_action('after_setup_theme', 'blanktheme_post_thumbnails');

add_action( 'after_setup_theme', 'blanktheme_theme_support' );

?>
