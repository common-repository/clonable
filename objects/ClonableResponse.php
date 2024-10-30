<?php

namespace Clonable\Objects;

class ClonableResponse {
    /* @var string $body */
    private $body;
    /* @var array $headers */
    private $headers;
    /* @var integer $code */
    private $code;
    /* @var boolean $is_error */
    private $is_connection_error;

    public function __construct($body, $headers, $code, $is_connection_error = false) {
        $this->body = $body;
        $this->headers = $headers;
        $this->code = $code;
        $this->is_connection_error = $is_connection_error;
    }

    /**
     * Get the request body
     * @return string
     */
    public function get_body() {
        return $this->body;
    }

    /**
     * Get the request headers
     * @return array
     */
    public function get_headers() {
        return $this->headers;
    }

    /**
     * Get the status code of the request
     * @return int
     */
    public function get_code() {
        return $this->code;
    }

    /**
     * Returns true when there was a connection error within the cURL request.
     * This makes more sense to check instead of the error code for the circuit breaker.
     * @return bool|mixed
     */
    public function is_connection_error() {
        return $this->is_connection_error;
    }
}