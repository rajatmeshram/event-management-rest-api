<?php
function crem_create_event_post_type() {
    register_post_type('event',
        array(
            'labels' => array(
                'name' => __('events'),
                'singular_name' => __('event'),
            ),
            'public' => true,
            'show_in_rest' => true, // Enable REST API support
            'supports' => array('title', 'editor', 'author', 'thumbnail','custom-fields'),
        )
    );
}
add_action('init', 'crem_create_event_post_type');
function crem_taxonomies_event() {
  $labels = array(
    'name'              => _x( 'Event Categories', 'taxonomy general name' ),
    'singular_name'     => _x( 'Event Category', 'taxonomy singular name' ),
    'search_items'      => __( 'Search Event Categories' ),
    'all_items'         => __( 'All Event Categories' ),
    'parent_item'       => __( 'Parent Event Category' ),
    'parent_item_colon' => __( 'Parent Event Category:' ),
    'edit_item'         => __( 'Edit Event Category' ), 
    'update_item'       => __( 'Update Event Category' ),
    'add_new_item'      => __( 'Add New Event Category' ),
    'new_item_name'     => __( 'New Event Category' ),
    'menu_name'         => __( 'Event Categories' ),
  );
  $args = array(
    'labels' => $labels,
    'hierarchical' => false,
  );
  register_taxonomy( 'event_category', 'event', $args );
}

add_action( 'init', 'crem_taxonomies_event', 0 );

function crem_event_metabox() {
add_meta_box( 'event_start_date', 'Event Start End date', 'crem_display_start_date_meta_box', 'event', 'side' );
}
add_action( 'admin_init', 'crem_event_metabox' );

function crem_display_start_date_meta_box( $data ) {
    $is_value = esc_html( get_post_meta( $data->ID, 'event_start_date', true ) );
    $end_value = esc_html( get_post_meta( $data->ID, 'event_end_date', true ) );
    ?>
    <div class="start-date"> 
    <span class="title">Start Date</span>
    <span class="content">
        <label for="startDate">
            <input type="datetime-local" name="start_date" id="startDate"  value="<?php echo $is_value; ?>" />
        </label>
    </span>
</div>
<div class="end-date"> 
 <span class="title">End Date</span>
    <span class="content">
        <label for="EndDate">
            <input type="datetime-local" name="end_date" id="endDate"  value="<?php echo $end_value; ?>" />
        </label>
    </span> 
</div>
    <?php
}

function crem_update_startdate( $post_id ) {

       if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( $parent_id = wp_is_post_revision( $post_id ) ) {
        $post_id = $parent_id;
    }
    update_post_meta( $post_id, 'event_start_date', sanitize_text_field( $_POST['start_date'] ) );
    update_post_meta( $post_id, 'event_end_date', sanitize_text_field( $_POST['end_date'] ) );
}
add_action( 'save_post', 'crem_update_startdate' );