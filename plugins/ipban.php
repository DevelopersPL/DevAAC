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

use DevAAC\Models\IpBan;

$meta = array('name' => 'IP Ban',
    'description' => 'Disallows access to users who are IP banned. APC user cache is recommended for performance.',
    'version' => '0.1',
    'author' => 'Don Daniello',
    'link' => 'https://github.com/DevelopersPL/DevAAC'
);

/*
 * This plugin strongly benefits from APC user cache!
 */

if( !in_array(basename(__FILE__), $DevAAC->enabled_plugins) )
    return array_merge($meta, array('enabled' => false));

// http://docs.slimframework.com/#How-to-Use-Hooks
$DevAAC->hook('slim.before', function () use ($DevAAC) {
    $req = $DevAAC->request;
    $apc = false;

    if(extension_loaded('apc') && ini_get('apc.enabled'))
    {
        $apc = true;
        $objname = 'ipban_'.$req->getIp();
    }

    if($apc && apc_fetch($objname))
    {
        $DevAAC->halt(403, 'Your IP address is banned.');
    }
    else
    {
        $ipban = IpBan::find( ip2long($req->getIp()) );
        if($ipban)
        {
            $DevAAC->halt(403, 'Your IP address is banned.');
            if($apc)
                apc_store($objname, true, 10 * 60);
                // THE INFORMATION WILL BE IN CACHE FOR 10 MINUTES SO WE CAN REJECT REQUESTS WITHOUT RUNNING ANY SQL QUERIES
        }
    }
});

return array_merge($meta, array('enabled' => true));
