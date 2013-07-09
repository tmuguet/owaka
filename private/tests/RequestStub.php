<?php

class RequestStub extends Request
{

    public static function factory($uri = TRUE, $client_params = array(), $allow_external = TRUE,
            $injected_routes = array())
    {
        // If this is the initial request
        if (!Request::$initial) {
            if (isset($_SERVER['SERVER_PROTOCOL'])) {
                $protocol = $_SERVER['SERVER_PROTOCOL'];
            } else {
                $protocol = HTTP::$protocol;
            }

            if (isset($_SERVER['REQUEST_METHOD'])) {
                // Use the server request method
                $method = $_SERVER['REQUEST_METHOD'];
            } else {
                // Default to GET requests
                $method = HTTP_Request::GET;
            }

            if (!empty($_SERVER['HTTPS']) AND filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN)) {
                // This request is secure
                $secure = TRUE;
            }

            if (isset($_SERVER['HTTP_REFERER'])) {
                // There is a referrer for this request
                $referrer = $_SERVER['HTTP_REFERER'];
            }

            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                // Browser type
                RequestStub::$user_agent = $_SERVER['HTTP_USER_AGENT'];
            }

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                // Typically used to denote AJAX requests
                $requested_with = $_SERVER['HTTP_X_REQUESTED_WITH'];
            }

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND isset($_SERVER['REMOTE_ADDR']) AND in_array($_SERVER['REMOTE_ADDR'],
                                                                                                        Request::$trusted_proxies)) {
                // Use the forwarded IP address, typically set when the
                // client is using a proxy server.
                // Format: "X-Forwarded-For: client1, proxy1, proxy2"
                $client_ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                RequestStub::$client_ip = array_shift($client_ips);

                unset($client_ips);
            } elseif (isset($_SERVER['HTTP_CLIENT_IP']) AND isset($_SERVER['REMOTE_ADDR']) AND in_array($_SERVER['REMOTE_ADDR'],
                                                                                                        Request::$trusted_proxies)) {
                // Use the forwarded IP address, typically set when the
                // client is using a proxy server.
                $client_ips = explode(',', $_SERVER['HTTP_CLIENT_IP']);

                RequestStub::$client_ip = array_shift($client_ips);

                unset($client_ips);
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                // The remote IP address
                RequestStub::$client_ip = $_SERVER['REMOTE_ADDR'];
            }

            if ($method !== HTTP_Request::GET) {
                // Ensure the raw body is saved for future use
                $body = file_get_contents('php://input');
            }

            if ($uri === TRUE) {
                // Attempt to guess the proper URI
                $uri = RequestStub::detect_uri();
            }

            $cookies = array();

            if (($cookie_keys = array_keys($_COOKIE))) {
                foreach ($cookie_keys as $key) {
                    $cookies[$key] = Cookie::get($key);
                }
            }

            // Create the instance singleton
            RequestStub::$initial = $request = new RequestStub($uri, $client_params, $allow_external, $injected_routes);

            // Store global GET and POST data in the initial request only
            $request->protocol($protocol)
                    ->query($_GET)
                    ->post($_POST);

            if (isset($secure)) {
                // Set the request security
                $request->secure($secure);
            }

            if (isset($method)) {
                // Set the request method
                $request->method($method);
            }

            if (isset($referrer)) {
                // Set the referrer
                $request->referrer($referrer);
            }

            if (isset($requested_with)) {
                // Apply the requested with variable
                $request->requested_with($requested_with);
            }

            if (isset($body)) {
                // Set the request body (probably a PUT type)
                $request->body($body);
            }

            if (isset($cookies)) {
                $request->cookie($cookies);
            }
        } else {
            $request = new RequestStub($uri, $client_params, $allow_external, $injected_routes);
        }

        return $request;
    }

    public function setParam($key, $value)
    {
        $this->_params[$key] = $value;
    }
}