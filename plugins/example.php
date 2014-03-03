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

// THIS IS ONLY OPTIONAL, YOU CAN DEFINE PLUGIN META DATA
// THIS CAN BE ANY INFORMATION YOU WANT
$meta = array('name' => 'Example plugin',
    'description' => 'This is an example how to write plugins. It exposes /uptime. It requires shell_exec function.',
    'version' => '1.0',
    'author' => 'Don Daniello',
    'link' => 'https://github.com/DevelopersPL/DevAAC'
);

// IF THE PLUGIN IS NOT ACTIVATED IN CONFIG, THEN DISABLE IT
if( !in_array(basename(__FILE__), $DevAAC->enabled_plugins) )
    return array_merge($meta, array('enabled' => false));

// YOU CAN ADD EXTRA REQUIREMENTS FOR THE PLUGIN TO BE ENABLED
if( !function_exists('shell_exec') )
    return array_merge($meta, array('enabled' => false));

/**
 * This is an example plugin that is automatically loaded
 * You can find instance of Slim framework app as $DevAAC
 * Check out Slim documentation at http://docs.slimframework.com/
 */
$DevAAC->get(ROUTES_PREFIX.'/uptime', function() use($DevAAC) {
    $DevAAC->response->headers->set('Content-Type', 'text');
    $DevAAC->response->setBody(shell_exec('uptime'));
});

$DevAAC->get(ROUTES_API_PREFIX.'/uptime', function() use($DevAAC) {
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody(json_encode(array('uptime' => shell_exec('uptime')), JSON_PRETTY_PRINT));
});

// THIS RETURNS META DATA SPECIFIED ABOVE
return array_merge($meta, array('enabled' => true));
