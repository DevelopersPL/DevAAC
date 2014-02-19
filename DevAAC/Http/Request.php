<?php
/**
 * Developer: Daniel
 * Date: 2/19/14
 * Time: 12:58 PM
 */

namespace DevAAC\Http;

class Request extends \Slim\Http\Request
{
    /**
     * This is similar to parent's params() but also includes params parsed with ContentTypes middleware
     * The order of preference is: Body, POST/PUT/PATCH/DELETE, GET
     *
     * @param  string           $key
     * @param  mixed            $default Default return value when key does not exist
     * @return array|mixed|null
     */
    public function getAPIParam($key = null, $default = null)
    {
        $body = $this->getBody();

        if(!$key)
            if(is_array($body))
                return array_merge($body, $this->get(), $this->post());
            else
                return array_merge($this->get(), $this->post());

        if( is_array($body) && array_key_exists($key, $body) )
            return $body[$key];

        elseif( $this->post($key) )
            return $this->post($key);

        elseif( $this->get($key) )
            return $this->get($key);

        else
            return $default;
    }
}