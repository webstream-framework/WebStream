<?php

class Weather extends HttpAgent {
    private $weather, $wid, $wurl;
    
    public function get($url, $params = array(), $headers = array()) {
        $xml = simplexml_load_string(parent::get($url));
        if (parent::getStatusCode() === 200) {
            return $xml->image->url;
        }
    }
}