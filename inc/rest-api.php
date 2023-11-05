<?php
function crem_createRoutes() {
    add_action( 'rest_api_init', function () {
            $rest_end_points = array(
                "Get Events"=> array(
                    "method" => "GET",
                    "callbak" => "crem_get_single_post",
                    "endpoint" => "show"
                ),
                "Create Event"=> array(
                    "method" => "POST",
                    "callbak" => "crem_create_event",
                    "endpoint" => "create"
                ),
                "Update Event"=> array(
                    "method" => "PUT",
                    "callbak" => "crem_updatebook_callback",
                    "endpoint" => "update"
                ),
                "Delet Event"=> array(
                    "method" => "DELETE",
                    "callbak" => "crem_delete_post_callback",
                    "endpoint" => "delete"
                ),
                "List Event"=> array(
                    "method" => "GET",
                    "callbak" => "crem_get_events",
                    "endpoint" => "allevents"
                ),
                "List Event"=> array(
                    "method" => "GET",
                    "callbak" => "crem_postbydate_callback",
                    "endpoint" => "list"
                ),

            );         
foreach ($rest_end_points as $subject => $rest_api_element){  
    register_rest_route( "events/v1",$rest_api_element['endpoint'], array(
        'methods' => $rest_api_element['method'],
        'callback' => $rest_api_element['callbak'],
        'permission_callback' => function() {
      return current_user_can('edit_others_posts');
    },
    ));
}
 });                    
}
crem_createRoutes();

function crem_create_event($data) {
    $new = array(
    'post_type' => 'event',
    'post_title' => sanitize_text_field($data['title']),
    'post_content' => wp_kses_post($data['description']),
    'post_status' => 'publish'
);
$post_id = wp_insert_post( $new );
    if( $post_id ){
        wp_set_object_terms($post_id, $data['category'], 'event_category');
        update_post_meta( $post_id, 'event_start_date', sanitize_text_field( $data['start']) );
        update_post_meta( $post_id, 'event_end_date', sanitize_text_field( $data['end'] ) );
        echo "Post successfully published!";
    } 
    else {
        echo "Something went wrong, try again.";
    }
}
function crem_updatebook_callback($request){
    
     $post_id = $request['id'];
     if (empty($post_id)) {
        return new WP_REST_Response('Post ID is required.', 400);
    }
    if (!current_user_can('edit_post', $post_id)) {
        return new WP_REST_Response('You do not have permission to delete this post.', 403);
    }
    if (get_post_type($post_id) == 'event') {
    $result =  wp_update_post(array(
            'ID' => $post_id,
            'post_title' => sanitize_text_field($request['title']),
            'post_content' => wp_kses_post($request['description']),
        ));
    if(!empty($request['start']) || !empty($request['end']) || $request['category']){
        wp_set_object_terms($post_id, $request['category'], 'event_category');
        update_post_meta( $post_id, 'event_start_date', sanitize_text_field( $request['start']) );
        update_post_meta( $post_id, 'event_end_date', sanitize_text_field( $request['end'] ) );

    }
        return new WP_REST_Response('Event updated successfully.', 200);
    }

     if ($result === false) {
        return new WP_REST_Response('Failed to update the Event.', 500);
    }

}
function crem_delete_post_callback($request) {
    $post_id = $request['id'];
    if (empty($post_id)) {
        return new WP_REST_Response('Post ID is required.', 400);
    }
    if (!current_user_can('delete_post', $post_id)) {
        return new WP_REST_Response('You do not have permission to delete this post.', 403);
    }
 if (get_post_type($post_id) == 'event') {
    $result = wp_delete_post($post_id, true);
     return new WP_REST_Response('Post deleted successfully.', 200);
 }
    if ($result === false) {
        return new WP_REST_Response('Failed to delete the post.', 500);
    }
   
}
function crem_get_single_post($request){
$post_id = $request['id'];
$content_post = get_post($post_id);
$content = $content_post->post_content;
$startdate = get_post_meta( $post_id, 'event_start_date', true );
$enddate = get_post_meta( $post_id, 'event_end_date', true );
$data = array(
            "id"=>$post_id,
            "content"=>$content,
            "startdate"=>$startdate,
            "enddate"=>$enddate

   );

return $data;
}
function crem_get_events() {
    $events = get_posts(array('post_type' => 'event'));
    return $events;
}

function crem_postbydate_callback($request){
$date = $request['date'];
$args = array(  'post_type' => 'event',
    'posts_per_page' => -1,
    'meta_key' => 'event_start_date',
    'orderby' => 'meta_value_num',
    'order' => 'ASC',
    'meta_query' => array(
        array(
            'key' => 'event_start_date',
            'value' => $date,
            'compare' => '>=',
        )
    )
);
$query = new WP_Query($args);
if ($query->have_posts()) :
    while ($query->have_posts()) :
        $query->the_post();
        $data[] =  array(
         "id"=>get_the_ID(),
          "title"=>get_the_title(),
          "content"=>get_the_content(),
          "startdate"=>get_post_meta( get_the_ID(), 'event_start_date', true ),
          "enddate"=>get_post_meta( get_the_ID(), 'event_end_date', true )


   );
    endwhile;
    wp_reset_postdata();
else :
    return new WP_REST_Response('No Event Found.', 200);
endif;
return $data;
}