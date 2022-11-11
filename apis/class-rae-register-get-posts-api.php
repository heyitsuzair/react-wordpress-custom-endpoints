<?php

/**
 * Register Get Post API Endpoint
 * 
 * @package REST API ENDPOINTS
 */

class Rae_Register_Get_Posts_Api
{
    /**
     * Constructor
     */

    function __construct()
    {
        $this->post_type = 'post';
        $this->route = '/posts';
        add_action('rest_api_init', [$this, 'register_api_endpoints']);
    }

    public function register_api_endpoints()
    {
        /**
         * Handle The Get Posts Request: GET Request
         * 
         * Examples: http://example.com/wp-json/rae/v1/($this->route)?page_no=1
         */
        register_rest_route('rae/v1', $this->route, [
            'method' => 'GET',
            'callback' => [$this, 'retrieve_posts']
        ]);
    }

    public function retrieve_posts(WP_REST_Request $req)
    {
        $response = [];
        $params = $req->get_params();
        $post_page_no = !empty($params['page_no']) ? intval(sanitize_text_field($params['page_no'])) : '';

        // Error Handling
        $error = new WP_Error();
        $post_data = $this->get_posts($post_page_no);

        return new WP_REST_Response($post_data);
    }

    public function get_posts($page_no = 1)
    {
        $args = [
            'post_type' => $this->post_type,
            'post_status' => 'publish',
            'post_per_page' => 9,
            'fields' => 'ids',
            'orderby' => 'date',
            'paged' => $page_no,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false
        ];
        $latest_post_ids = new WP_Query($args);
        $post_result = $this->get_required_posts_data($latest_post_ids->posts);

        return [
            'post_data' => $post_result
        ];
    }
    public function get_required_posts_data($post_IDs)
    {
        $post_result = [];

        if (empty($post_IDs) && !is_array($post_IDs)) {
            return $post_result;
        }

        foreach ($post_IDs as $post_ID) {
            $author_id = get_post_field('post_author', $post_ID);
            $thumbnail_id = get_post_thumbnail_id($post_ID);

            $post_data = [];
            $post_data['id'] = $post_ID;
            $post_data['title'] = get_the_title($post_ID);
            $post_data['excerpt'] = get_the_excerpt($post_ID);
            $post_data['date'] = get_the_date('', $post_ID);
            $post_data['attachement_image'] = [
                'img_sizes' => wp_get_attachment_image_sizes($thumbnail_id),
                'img_src' => wp_get_attachment_image_src($thumbnail_id, 'full'),
                'img_srcset' => wp_get_attachment_image_srcset($thumbnail_id)
            ];
            $post_data['categories'] = get_the_category($post_ID);
            $post_data['meta'] = [
                'author_id' => $author_id,
                'author_name' => get_the_author_meta('display_name', $author_id)
            ];

            array_push($post_result, $post_data);
        }
        return $post_result;
    }
}
new Rae_Register_Get_Posts_Api();