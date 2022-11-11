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
        $latest_post = new WP_Query($args);

        $post_result = $latest_post->posts;

        return [
            'post_data' => $post_result
        ];
    }
}
new Rae_Register_Get_Posts_Api();