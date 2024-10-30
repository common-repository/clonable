<?php

namespace Clonable\Objects;

class CurlBuilder {
    private $curl;

    public function __construct($url) {
        $this->curl = curl_init($url);
    }

    public function set_request_type($request_type) {
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $request_type);
        return $this;
    }

    public function set_post_fields($post_fields) {
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post_fields);
        return $this;
    }

    public function set_standard_headers($site_url, $max_timeout = 15) {
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $max_timeout);
        curl_setopt($this->curl, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);           // get body response
        curl_setopt($this->curl, CURLOPT_HEADER, true);                   // get header response
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);          // don't care about SSL
        curl_setopt($this->curl, CURLOPT_RESOLVE, ["$site_url:443:" . ClonableConfig::SERVER_IP]);
        return $this;
    }

    public function set_header_response(&$headers) {
        // @codingStandardsIgnoreStart
        curl_setopt($this->curl, CURLOPT_HEADERFUNCTION,
            function($curl, $header) use (&$headers) {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) // ignore invalid headers
                    return $len;

                $headers[strtolower(trim($header[0]))][] = trim($header[1]);

                return $len;
            }
        );
        // @codingStandardsIgnoreEnd
        return $this;
    }

    public function set_http_headers($request_headers) {
        $headers = array();
        $request_headers['Accept-Encoding'] = 'gzip';
        foreach ($request_headers as $key => $header) {
            if (strtolower($key) == 'content-length') {
                // Let curl do this
                continue;
            }
            $headers[] = "$key: $header";
        }
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
        return $this;
    }

    public function build() {
        return $this->curl;
    }
}