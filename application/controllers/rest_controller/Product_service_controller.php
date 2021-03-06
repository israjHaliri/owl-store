<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

/**
 * This is an example of a few basic param interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Product_service_controller extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('product_service_model');

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['param_get']['limit'] = 500; // 500 requests per hour per param/key
        $this->methods['param_post']['limit'] = 100; // 100 requests per hour per param/key
        $this->methods['param_delete']['limit'] = 50; // 50 requests per hour per param/key
    }

    public function params_get()
    {
        // Users from a data store e.g. database
        // $params = [
        //     ['id_param' => 1, 'name' => 'John', 'email' => 'john@example.com', 'fact' => 'Loves coding'],
        //     ['id_param' => 2, 'name' => 'Jim', 'email' => 'jim@example.com', 'fact' => 'Developed on CodeIgniter'],
        //     ['id_param' => 3, 'name' => 'Jane', 'email' => 'jane@example.com', 'fact' => 'Lives in the USA', ['hobbies' => ['guitar', 'cycling']]],
        // ];
        $params = $this->product_service_model->m_get_product();

        $id_param = $this->get('id_param');

        // If the id_param parameter doesn't exist return all the params

        if ($id_param === NULL)
        {
            // Check if the params data store contains params (in case the database result returns NULL)
            if ($params)
            {
                // Set the response and exit
                $this->response($params, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                // Set the response and exit
                $this->response(array(
                    'status' => FALSE,
                    'message' => 'No params were found'
                ), REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            }
        }

        // Find and return a single record for a particular param.

        $id_param = (int) $id_param;

        // Valid_paramate the id_param.
        if ($id_param <= 0)
        {
            // Invalid_param id_param, set the response and exit.
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        // Get the param from the array, using the id_param as key for retreival.
        // Usually a model is to be used for this.

        $param = NULL;

        if (!empty($params))
        {
            foreach ($params as $key => $value)
            {
                if (isset($value['id_param']) && $value['id_param'] === $id_param)
                {
                    $param = $value;
                }
            }
        }
        if (!empty($param))
        {
            $this->set_response($param, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            $this->set_response(array(
                'status' => FALSE,
                'message' => 'User could not be found'
            ), REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    public function params_post()
    {
        // $this->some_model->update_param( ... );
        $message = array(
            'id_param' => 100, // Automatically generated by the model
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'message' => 'Added a resource'
        );

        $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
    }

    public function params_delete()
    {
        $id_param = (int) $this->get('id_param');

        // Valid_paramate the id_param.
        if ($id_param <= 0)
        {
            // Set the response and exit
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        // $this->some_model->delete_something($id_param);
        $message = array(
            'id_param' => $id_param,
            'message' => 'Deleted the resource'
        );

        $this->set_response($message, REST_Controller::HTTP_NO_CONTENT); // NO_CONTENT (204) being the HTTP response code
    }

}
