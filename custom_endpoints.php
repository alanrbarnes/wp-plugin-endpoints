<?php
/*
Plugin Name:  Custom Endpoints
Description:  Plugin to create custom api endpoints
Plugin URI:   Livingwd.org
Author:       Alan Barnes
Aurthor URI:  alanrbarnes.com
Version:      1.0
Text Domain:  CustomEndpoints
Domain Path:  /languages
License:      GPL v2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.txt
*/

require_once( ABSPATH . "/wp-load.php" );


// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}
//sample path
//http://localhost:8080/krcpreview/index.php/wp-json/ce/v1/posts

add_action('rest_api_init', function() {

    /////////////////////////////////////////////////////
    //test  endpoints

    //get all posts
    register_rest_route('ce/v1', 'posts', [
        'methods' => 'GET',
        'callback' => 'ce_posts',
    ]);
    //get posts by name
    register_rest_route('ce/v1', 'posts/(?P<slug>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => 'ce_post',
    ));

    //get media in folder
    //http://localhost:8080/krcpreview/index.php/wp-json/ce/v1/media
    register_rest_route('ce/v1', 'media', [
        'methods' => 'GET',
        'callback' => 'ce_all_media',
    ]);


    /////////////////////////////////////////////////////
    //krc specific endpoints

    //get all krc classes
    //http://localhost:8080/krcpreview/index.php/wp-json/wp/v2/krc-class
    //http://localhost:8080/krcpreview/index.php/wp-json/ce/v1/krc_classes
    register_rest_route('ce/v1', 'krc_classes', [
        'methods' => 'GET',
        'callback' => 'ce_all_krc_classes',
    ]);

    //get krc class by slug
    //v<slug>
    register_rest_route('ce/v1', 'krc_classes_name/(?P<slug>[a-zA-Z0-9-_]+)', array(
        'methods' => 'GET',
        'callback' => 'ce_krc_class_name',
    ));

    //get all krc homework
    //http://localhost:8080/krcpreview/index.php/wp-json/wp/v2/krc-homework-file
    //http://localhost:8080/krcpreview/index.php/wp-json/ce/v1/krc_homework
    register_rest_route('ce/v1', 'krc_homework', [
        'methods' => 'GET',
        'callback' => 'ce_all_krc_homework',
    ]);

    //https://wordpress.stackexchange.com/questions/338682/whats-the-ptag-register-rest-api-construct-what-is-for-and-where-to-find
    // https://stackoverflow.com/questions/72667189/regex-not-working-with-wordpress-rest-api
    // https://developer.wordpress.org/rest-api/extending-the-rest-api/modifying-responses/
    // https://developer.wordpress.org/rest-api/extending-the-rest-api/routes-and-endpoints/

    register_rest_route('ce/v1', '/krc_homework_name/(?P<slug>[a-zA-Z0-9-_]+)', array(  /*(?P<id>[\d]+)', array(   ?P<{name}>{regex pattern}.*/
        'methods' => 'GET',
        'callback' => 'ce_krc_homework_category_name',
    ));

    register_rest_route('ce/v1', '/krc_homework_name1/(?P<slug>[a-zA-Z0-9-_]+)', array(  /*(?P<id>[\d]+)', array(   ?P<{name}>{regex pattern}.*/
        'methods' => 'GET',
        'callback' => 'ce_krc_homework_category_name',
    ));

    //get krc homework by class number  
    ////http://localhost:8080/krcpreview/index.php/wp-json/ce/v1/krc_homework_name/<slug>
    
    //get all krc handouts
    //http://localhost:8080/krcpreview/index.php/wp-json/wp/v2/krc-handout
    //http://localhost:8080/krcpreview/index.php/wp-json/ce/v1/krc_handouts
    register_rest_route('ce/v1', 'krc_handouts', [
        'methods' => 'GET',
        'callback' => 'ce_all_krc_handouts',
    ]);

    //get krc homework by category name
    //http://localhost:8080/krcpreview/index.php/wp-json/ce/v1/krc_handouts_name/<slug>
    register_rest_route('ce/v1', 'krc_handouts_name/(?P<slug>[a-zA-Z0-9-_]+)', array(  // /(?P<slug>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => 'ce_krc_handout_category_name',
    ));

    //end krc specific endpoints


    
    //user functions
    /*
    //get users
    register_rest_route('adeuser/v1', '/users', array(
        'methods' => 'GET',
        'callback' => array($this, 'get_users'),
    ));
    //delete user
    register_rest_route('adeuser/v1', '/user', array(
        'methods' => 'DELETE',
        'callback' => array($this, 'delete_user'),
    ));
    //user update meta
    register_rest_route('adeuser/v1', '/update_usermeta', array(
        'methods' => 'PUT',
        'callback' => array($this, 'update_usermeta'),
    ));
    */
/*
    //media upload
    register_rest_route('adeuser/v1', '/media_upload', array(
        'methods' => 'POST',
        'callback' => array($this, 'media_upload'),
    ));
*/
 });

//  wp/v2/krc-class
// http://localhost:8080/krcpreview/index.php/wp-json/ce/v1/krc_classes
 function ce_all_krc_classes() {
    $args=[
        'numberposts' => 9999,  //access this number of posts
        'post_type' => 'krc-class'  //could change to custom post type like products
    ];

    $posts = get_posts($args);

    //for using acf getting custom field
    //get_field( "name_of_field", $post[0]->ID);

    $data = [];
    $i = 0;
    foreach($posts as $post) {
        $data[$i]['id'] = $post->ID;
        $data[$i]['title'] = $post->post_title;
        $data[$i]['content'] = $post->post_content;
        $data[$i]['slug'] = $post->post_name;
        $data[$i]['class_text'] = get_field('class_text', $post->ID);
        $data[$i]['class_video_url'] = get_field('class_video_url', $post->ID);
        $data[$i]['class_week_description'] = get_field('class_week_description', $post->ID);
        $data[$i]['class_document_1'] = get_field('class_document_1', $post->ID);
        $data[$i]['class_document_2'] = get_field('class_document_2', $post->ID);
        $data[$i]['class_document_3'] = get_field('class_document_3', $post->ID);
        // $data[$i]['featured_image']['thumbnail'] = get_the_post_thumbnail_url($post->ID, 'thumbnail');
        // $data[$i]['featured_image']['medium'] = get_the_post_thumbnail_url($post->ID, 'medium');
        // $data[$i]['featured_image']['large'] = get_the_post_thumbnail_url($post->ID, 'large');
        $i++;
    }
    return $data;
    //return 'Our awesome endpoint!';
 }

//  http://localhost:8080/krcpreview/index.php/wp-json/ce/v1/krc_classes_name/class-1
 //class-1, class-2,...
 function ce_krc_class_name($slug) {
    $args=[
        'name' => $slug['slug'],  //access this number of posts
        'post_type' => 'krc-class'  //could change to custom post type like products
    ];

    $post = get_posts($args);
    $data = [];

    $data['id'] = $post[0]->ID;
    $data['title'] = $post[0]->post_title;
    $data['content'] = $post[0]->post_content;
    $data['slug'] = $post[0]->post_name;
    $data['class_text'] = get_field('class_text', $post[0]->ID);
    $data['class_video_url'] = get_field('class_video_url', $post[0]->ID);
    $data['class_week_description'] = get_field('class_week_description', $post[0]->ID);
    $data['class_document_1'] = get_field('class_document_1', $post[0]->ID);
    $data['class_document_2'] = get_field('class_document_2', $post[0]->ID);
    $data['class_document_3'] = get_field('class_document_3', $post[0]->ID);
    return $data;

 }

//  http://localhost:8080/krcpreview/index.php/wp-json/ce/v1/krc_homework
 function ce_all_krc_homework() {
    $args=[
        'numberposts' => 9999,  //access this number of posts
        'post_type' => 'krc-homework-file'  //could change to custom post type like products
    ];

    $posts = get_posts($args);

    //for using acf getting custom field
    //get_field( "name_of_field", $post[0]->ID);

    $data = [];
    $i = 0;
    foreach($posts as $post) {
        $data[$i]['id'] = $post->ID;
        $data[$i]['title'] = $post->post_title;
        $data[$i]['content'] = $post->post_content;
        $data[$i]['slug'] = $post->post_name;
        $data[$i]['class_text'] = get_field('class_text', $post->ID);
        $data[$i]['class_video_url'] = get_field('class_video_url', $post->ID);
        $data[$i]['class_week_description'] = get_field('class_week_description', $post->ID);
        $data[$i]['class_document_1'] = get_field('class_document_1', $post->ID);
        $data[$i]['class_document_2'] = get_field('class_document_2', $post->ID);
        $data[$i]['class_document_3'] = get_field('class_document_3', $post->ID);
        // $data[$i]['featured_image']['thumbnail'] = get_the_post_thumbnail_url($post->ID, 'thumbnail');
        // $data[$i]['featured_image']['medium'] = get_the_post_thumbnail_url($post->ID, 'medium');
        // $data[$i]['featured_image']['large'] = get_the_post_thumbnail_url($post->ID, 'large');
        $i++;
    }
    return $data;
 }

 //Homework by class taxonomy name
//  class_1, class_2, class_3...
// http://localhost:8080/krcpreview/index.php/wp-json/ce/v1/krc_homework_name/class_1
 function ce_krc_homework_category_name($slug) {
    //$urlparams = $slug['slug'];
    $urlparams = $slug->get_url_params( 'id' );
    
    $args = [];
    $data = [];
    // $category = $urlparams['slug']; //string value of slug

    if ($urlparams) {
      if (!is_array($urlparams)) {
        $urlparams = array($urlparams);
      }

      $args = array(
        'post_type' => 'krc-homework-file',
        'posts_per_page' => 5,
        'orderby' => 'rand',
        'tax_query' => array(
            array(
                'taxonomy' => 'class-week-number',
                'field' => 'slug',  //use the slug as the id value to find tax field
                'terms' => $urlparams,  //find the find the fitness category
                'include_children' => true,  //include taxonomy children of this taxonomy
                'operator' => 'IN'  //post is in category, opposite is 'NOT IN'
            )
        )
      );
    }

    $post = get_posts($args);

    $data['id'] = $post[0]->ID;
    $data['title'] = $post[0]->post_title;
    $data['content'] = $post[0]->post_content;
    $data['slug'] = $post[0]->post_name;
    $data['class_text'] = get_field('class_text', $post[0]->ID);
    $data['class_video_url'] = get_field('class_video_url', $post[0]->ID);
    $data['class_week_description'] = get_field('class_week_description', $post[0]->ID);
    $data['class_document_1'] = get_field('class_document_1', $post[0]->ID);
    $data['class_document_2'] = get_field('class_document_2', $post[0]->ID);
    $data['class_document_3'] = get_field('class_document_3', $post[0]->ID);
    

    return $data;
 }

//  http://localhost:8080/krcpreview/index.php/wp-json/ce/v1/krc_handouts
 function ce_all_krc_handouts() {
    $args=[
        'numberposts' => 9999,  //access this number of posts
        'post_type' => 'krc-handout'  //could change to custom post type like products
    ];

    $posts = get_posts($args);

    //for using acf getting custom field
    //get_field( "name_of_field", $post[0]->ID);

    $data = [];
    $i = 0;
    foreach($posts as $post) {
        $data[$i]['id'] = $post->ID;
        $data[$i]['title'] = $post->post_title;
        $data[$i]['content'] = $post->post_content;
        $data[$i]['slug'] = $post->post_name;
        $data[$i]['class_text'] = get_field('class_text', $post->ID);
        $data[$i]['class_video_url'] = get_field('class_video_url', $post->ID);
        $data[$i]['class_week_description'] = get_field('class_week_description', $post->ID);
        $data[$i]['class_document_1'] = get_field('class_document_1', $post->ID);
        $data[$i]['class_document_2'] = get_field('class_document_2', $post->ID);
        $data[$i]['class_document_3'] = get_field('class_document_3', $post->ID);
        // $data[$i]['featured_image']['thumbnail'] = get_the_post_thumbnail_url($post->ID, 'thumbnail');
        // $data[$i]['featured_image']['medium'] = get_the_post_thumbnail_url($post->ID, 'medium');
        // $data[$i]['featured_image']['large'] = get_the_post_thumbnail_url($post->ID, 'large');
        $i++;
    }
    return $data;
 }

  //  https://support.advancedcustomfields.com/forums/topic/query-posts-via-taxonomy-field/
//   https://wordpress.stackexchange.com/questions/244702/wp-query-tax-query-on-acf-post-object
// https://omarshishani.medium.com/how-to-use-query-parameters-in-your-wordpress-rest-api-route-endpoint-%EF%B8%8F-b325fece1de1

// http://localhost:8080/krcpreview/index.php/wp-json/ce/v1/krc_handouts_name/fitness
//categories: gear, gear, handouts_misc, injury_prevention, motivation, nutrition, running, seasonal_gear, shoes, water, winter_running, workouts_of_the_week
 function ce_krc_handout_category_name(WP_REST_Request $slug) {
    $urlparams = $slug->get_url_params( 'id' );
    $args = [];
    $data = [];
    // $category = $urlparams['slug']; //string value of slug

    if ($urlparams) {
      if (!is_array($urlparams)) {
        $urlparams = array($urlparams);
      }

      $args = array(
        'post_type' => 'krc-handout',
        'posts_per_page' => 5,
        'orderby' => 'rand',
        'tax_query' => array(
            array(
                'taxonomy' => 'krc-document-category',
                'field' => 'slug',  //use the slug as the id value to find tax field
                'terms' => $urlparams,  //find the find the fitness category
                'include_children' => true,  //include taxonomy children of this taxonomy
                'operator' => 'IN'  //post is in category, opposite is 'NOT IN'
            )
        )
      );
    }

    $post = get_posts($args);

    $data['id'] = $post[0]->ID;
    $data['title'] = $post[0]->post_title;
    $data['content'] = $post[0]->post_content;
    $data['slug'] = $post[0]->post_name;
    $data['class_text'] = get_field('class_text', $post[0]->ID);
    $data['class_video_url'] = get_field('class_video_url', $post[0]->ID);
    $data['class_week_description'] = get_field('class_week_description', $post[0]->ID);
    $data['class_document_1'] = get_field('class_document_1', $post[0]->ID);
    $data['class_document_2'] = get_field('class_document_2', $post[0]->ID);
    $data['class_document_3'] = get_field('class_document_3', $post[0]->ID);

    return $data;
    }
    

 
 



// add_action( 'wp_enqueue_scripts', 'custom_script_style_adding_function' ); 

// function custom_script_style_adding_function() {
//     // wp_enqueue_script( 'my-js', get_stylesheet_directory_uri() . '/script.js' );
//     wp_register_style( 'Font_Awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' );
//     wp_enqueue_style('Font_Awesome');
//         wp_register_style( 'Bootstrap', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/js/bootstrap.min.js' );
//     wp_enqueue_style('Bootstrap');
// }

// add_action('wp_head', 'add_code_on_body_open');

// function add_code_on_body_open() {
//     echo '<style>.socialIcons {height: 30px; }</style>
//         <div class="w-100 p-1" style="background-color: #222; height: 30px;padding-top: 10px; padding-bottom: 10px; padding-right: 40px;"> 
//             <!--<a href="http://twitter.com/DrBillWinston" target="_blank" rel="noopener" style="color: #fff;float: right;visibility: hidden; padding: 0 1.5em;" class="float-right" href=""><img src="./wp-content/uploads/2022/04/twitter.png" alt="Twitter" class="socialIcons"></a>-->
//             <a href="http://twitter.com/DrBillWinston" target="_blank" rel="noopener" style="color: #fff;float: right; padding: 0 1.5em;" class="float-right" href=""><img src="./wp-content/uploads/2022/04/twitter.png" alt="Twitter" class="socialIcons"> </a>
//             <a href="http://facebook.com/billwinstonministries" target="_blank" rel="noopener" style="color: #fff;float: right; padding: 0 1.5em;" class="float-right" href=""><img src="./wp-content/uploads/2022/04/facebook.png" alt="Facebook" class="socialIcons"> </a>
//             <a href="https://www.instagram.com/drbillwinston/" target="_blank" rel="noopener" style="color: #fff;float: right; padding: 0 1.5em;" class="float-right" href=""><img src="./wp-content/uploads/2022/04/instagram.png" alt="instagram" class="socialIcons"> </a>
//             <a href="https://www.youtube.com/drbillwinston" target="_blank" rel="noopener" style="color: #fff;float: right; padding: 0 1.5em;" class="float-right" href=""><img src="./wp-content/uploads/2022/04/youtube.png" alt="Youtube" class="socialIcons"> </a>
//             <!--<a href="https://www.youtube.com/drbillwinston" target="_blank" rel="noopener" style="color: #fff;visibility: hidden;" class="float-right" href=""><img src="./wp-content/uploads/2022/04/youtube.png" alt="Youtube" class="socialIcons"> </a>-->
//         </div>';
// }





function ce_all_media() {
    global $wpdb;

    $get = $wpdb->get_results(" 
                SELECT 
                    posts.*, rm.id as folder_id, rm.slug as folder_name
                FROM 
                    ".$wpdb->prefix."posts AS posts 
                LEFT JOIN 
                    ".$wpdb->prefix."realmedialibrary_posts AS rmp
                ON 
                    posts.ID = rmp.attachment
                LEFT JOIN 
                    ".$wpdb->prefix."realmedialibrary AS rm
                ON
                    rm.id = rmp.fid
                Where 
                    posts.post_type = 'attachment' 
                ");

    $query_images_args = array(
        'post_type'      => 'attachment',
        'Folder'         => 'news',
        'post_mime_type' => 'image',
        'post_status'    => 'inherit',
        'posts_per_page' => - 1,
    );
    
    $query_images = new WP_Query( $query_images_args );
    
    $images = array();
    foreach ( $query_images->posts as $image ) {
        $images[] = wp_get_attachment_url( $image->ID );
    }
    return $get;
 }



// Example function to access all posts
function ce_posts() {
    $args=[
        'numberposts' => 9999,  //access this number of posts
        'post_type' => 'post'  //could change to custom post type like products
    ];

    $posts = get_posts($args);

    //for using acf getting custom field
    //get_field( "name_of_field", $post[0]->ID);

    $data = [];
    $i = 0;
    foreach($posts as $post) {
        $data[$i]['id'] = $post->ID;
        $data[$i]['title'] = $post->post_title;
        $data[$i]['content'] = $post->post_content;
        $data[$i]['slug'] = $post->post_name;
        $data[$i]['featured_image']['thumbnail'] = get_the_post_thumbnail_url($post->ID, 'thumbnail');
        $data[$i]['featured_image']['medium'] = get_the_post_thumbnail_url($post->ID, 'medium');
        $data[$i]['featured_image']['large'] = get_the_post_thumbnail_url($post->ID, 'large');
        $i++;
    }
    return $data;
    //return 'Our awesome endpoint!';
 }

 // Example function to access one post by slug name
 function ce_post($slug) {
    $args=[
        'name' => $slug['slug'],  //access this number of posts
        'post_type' => 'post'  //could change to custom post type like products
    ];

    $post = get_posts($args);

    $data['id'] = $post[0]->ID;
    $data['title'] = $post[0]->post_title;
    $data['content'] = $post[0]->post_content;
    $data['slug'] = $post[0]->post_name;
    $data['featured_image']['thumbnail'] = get_the_post_thumbnail_url($post[0]->ID, 'thumbnail');
    $data['featured_image']['medium'] = get_the_post_thumbnail_url($post[0]->ID, 'medium');
    $data['featured_image']['large'] = get_the_post_thumbnail_url($post[0]->ID, 'large');

    return $data;
    //return $slug['slug'];
 }



 ////////////////////////////////////////////////
 //user functions
  //https://github.com/adeleyeayodeji/wordpress-image-upload-api

 /*
    //media_upload
    function media_upload()
    {

        $files = $_FILES;
        //return $files;  //testing in postman

        //return $this->uploadFile();

        $uploadedfile = $files['file'];
        $upload_overrides = array('test_form' => false);
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
        if($movefile && !isset($movefile['error'])) {
            return $movefile;
        } else {
            return $movefile['error'];
        }
    }*/

    //async-upload is name of key for upload of file
    /*function uploadFile()
    {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        //upload only images and files with the following extensions
        $file_extension_type = array('jpg', 'jpeg', 'jpe', 'gif', 'png', 'bmp', 'tiff', 'tif', 'ico', 'zip', 'pdf', 'docx');
        $file_extension = strtolower(pathinfo($_FILES['async-upload']['name'], PATHINFO_EXTENSION));
        if (!in_array($file_extension, $file_extension_type)) {
            return wp_send_json(
                array(
                    'success' => false,
                    'data'    => array(
                        'message'  => __('The uploaded file is not a valid file. Please try again.'),
                        'filename' => esc_html($_FILES['async-upload']['name']),
                    ),
                )
            );
        }

        $attachment_id = media_handle_upload('async-upload', null, []);

        if (is_wp_error($attachment_id)) {
            return wp_send_json(
                array(
                    'success' => false,
                    'data'    => array(
                        'message'  => $attachment_id->get_error_message(),
                        'filename' => esc_html($_FILES['async-upload']['name']),
                    ),
                )
            );
        }

        if (isset($post_data['context']) && isset($post_data['theme'])) {
            if ('custom-background' === $post_data['context']) {
                update_post_meta($attachment_id, '_wp_attachment_is_custom_background', $post_data['theme']);
            }

            if ('custom-header' === $post_data['context']) {
                update_post_meta($attachment_id, '_wp_attachment_is_custom_header', $post_data['theme']);
            }
        }

        $attachment = wp_prepare_attachment_for_js($attachment_id);
        if (!$attachment) {
            return wp_send_json(
                array(
                    'success' => false,
                    'data'    => array(
                        'message'  => __('Image cannot be uploaded.'),
                        'filename' => esc_html($_FILES['async-upload']['name']),
                    ),
                )
            );
        }

        return wp_send_json(
            array(
                'success' => true,
                'data'    => $attachment,
            )
        );
    }*/

/*
    //update_usermeta
    function update_usermeta(WP_REST_Request $request)
    {
        $billing_prefix = "billing_";
        $shipping_prefix = "shipping_";
        $user_id = $request->get_param('user_id');

        //check if id is empty
        if (empty($user_id)) {
            return new WP_Error('error', 'User ID is empty', array('status' => 400));
        }
        //check if user exist
        $user = get_user_by('id, $user_id');
        if(empty($user)) {
            return new WP_Error('error', 'User does not exist', array('status' => 400));
        }
        //update bulk billing and shipping info
        $bulkdata = $request->get_params();
        //unset user_id
    }*/