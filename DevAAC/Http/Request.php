<?php
/**
 * DevAAC
 *
 * Automatic Account Creator by developers.pl for TFS 1.0
 *
 *
 * LICENSE: Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE
 * FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @package    DevAAC
 * @author     Daniel Speichert <daniel@speichert.pl>
 * @author     Wojciech Guziak <wojciech@guziak.net>
 * @copyright  2014 Developers.pl
 * @license    http://opensource.org/licenses/MIT MIT
 * @version    master
 * @link       https://github.com/DevelopersPL/DevAAC
 */

namespace DevAAC\Http;

use InputErrorException;

class Request extends \Slim\Http\Request
{
    /**
     * This is similar to parent's params() but also includes params parsed with ContentTypes middleware
     * The order of preference is: Body, POST/PUT/PATCH/DELETE, GET
     *
     * @param  string           $key
     * @param  mixed            $default Default return value when key does not exist
     * @return array|mixed|null
     * @throws InputErrorException
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

        elseif( $default !== null )
            return $default;

        else
            throw new InputErrorException('API parameter "' . $key . '" is missing.', 400);
    }
}
