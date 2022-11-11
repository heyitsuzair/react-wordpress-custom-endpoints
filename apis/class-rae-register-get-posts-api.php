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
        wp_die();
        add_action('rest_api_init', [$this, 'register_api_endpoints']);
    }

    public function register_api_endpoints()
    {
    }
}