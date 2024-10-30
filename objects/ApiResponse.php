<?php

namespace Clonable\Objects;

class ApiResponse {
    /* @var int $code */
    private $code;

    /* @var mixed $response */
    private $response;

    /**
     * Constructor for ApiResponse class.
     * @param $code
     * @param $response
     * @param $message
     */
    public function __construct($code, $response) {
        $this->code = $code;
        $this->response = $response;
    }

    /**
     * Return the code property.
     * @return int
     */
    public function get_code() {
        return $this->code;
    }

    /**
     * Makes sure the response is always in a consistent format.
     * @return mixed
     */
    public function get_response() {
        // if the response is 200 or the error are correctly set, then return the response.
        if ($this->code == 200 || isset($this->response['errors'])) {
            return $this->response;
        }

        // otherwise put the response message into an error bag
        $errors = array();
        $errors['errors']['message'][0] = "{$this->response['message']} Please use the API key issued to the main account of the clone.";
        return $errors;
    }

    public function get_response_message() {
        return $this->response['message'];
    }
}