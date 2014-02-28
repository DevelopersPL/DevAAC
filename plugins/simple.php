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

use DevAAC\Models\Account;
use DevAAC\Models\Player;

$meta = array('name' => 'Simple AAC',
    'description' => 'Dead simple one-page AAC',
    'version' => '0.1'
);

if( !in_array(basename(__FILE__), $DevAAC->enabled_plugins) )
    return array_merge($meta, array('enabled' => false));

$DevAAC->map(ROUTES_PREFIX.'/', function() use($DevAAC) {
    $view = $DevAAC->view();
    $view->setTemplatesDirectory('../plugins/templates');
    $req = $DevAAC->request;

    $data = array();
    $error = false;
    if($req->isPost()) {
        $data['val'] = $req->post();

        // VALIDATE CHARACTER NAME
        if( !filter_var($req->post('character-name'), FILTER_VALIDATE_REGEXP,
            array("options" => array("regexp" => "/^[a-zA-Z ]{5,20}$/"))) ) {
            $DevAAC->flashNow('character-name_class', 'has-error');
            $DevAAC->flashNow('danger', 'Character name must have 5-20 characters, only letters and space.');
            $error = true;
        }

        // VALIDATE VOCATION
        if( ! in_array($req->post('vocation'), array(1, 2, 3, 4)) ) {
            $DevAAC->flashNow('vocation_class', 'has-error');
            $error = true;
        }

        // VALIDATE SEX
        if( ! in_array($req->post('sex'), array(0, 1)) ) {
            $DevAAC->flashNow('sex_class', 'has-error');
            $error = true;
        }

        // IF VALIDATION ERROR, EXIT
        if($error)
            goto render;

        $account = Account::where('name', $req->post('account-name'))->first();

        // IF ACCOUNT EXISTS AND PASSWORD IS WRONG, EXIT
        if($account && !$account->comparePassword($req->post('password'))) {
            $DevAAC->flashNow('danger', 'This account already exists and password is not correct. Cannot add a character.
                                      Enter correct password or try a different account name.');
            $DevAAC->flashNow('password_class', 'has-error');
            goto render;
        }

        $name = ucwords(strtolower($req->post('character-name')));
        // check if character name is available
        $player = Player::where('name', $name)->first();
        if($player) {
            $DevAAC->flashNow('danger', 'This character already exists.');
            $DevAAC->flashNow('character-name_class', 'has-error');
            goto render;
        }

        // IF THE ACCOUNT EXISTS, JUMP TO CREATING PLAYER
        if($account)
            goto createcharacter;

        // VALIDATE ACCOUNT NAME ONLY IF THE ACCOUNT DOES NOT EXIST
        if( !filter_var($req->post('account-name'), FILTER_VALIDATE_REGEXP,
            array("options" => array("regexp" => "/^[a-zA-Z]{2,12}$/"))) ) {
            $DevAAC->flashNow('account-name_class', 'has-error');
            $DevAAC->flashNow('danger', 'Account name must have 2-12 characters, only letters.');
            $error = true;
        }

        // VALIDATE PASSWORD ONLY IF THE ACCOUNT DOES NOT EXIST
        if( !filter_var($req->post('password'), FILTER_VALIDATE_REGEXP,
            array("options" => array("regexp" => "/^.{6,20}$/"))) ) {
            $DevAAC->flashNow('password_class', 'has-error');
            $DevAAC->flashNow('danger', 'Password must have 6-20 characters.');
            $error = true;
        }

        // VALIDATE EMAIL ONLY IF THE ACCOUNT DOES NOT EXIST
        if( !filter_var($req->post('email'), FILTER_VALIDATE_EMAIL) ) {
            $DevAAC->flashNow('email_class', 'has-error');
            $DevAAC->flashNow('danger', 'Enter valid email address');
            $error = true;
        }

        // IF VALIDATION ERROR, EXIT
        if($error)
            goto render;

        // IF ACCOUNT DOES NOT EXIST, CREATE IT NOW
        $account = DevAAC\Models\Account::create( array('name' => $req->post('account-name'),
                                          'password' => $req->post('password'),
                                          'email' => $req->post('email'),
                                          'creation' => time()) );

        createcharacter:
        $player = new DevAAC\Models\Player();
        $player->account()->associate($account);
        $player->name = $name;
        $player->vocation = $req->post('vocation');
        $player->sex = $req->post('sex');
        $player->town_id = 1;
        $player->level = 8;
        $player->push(); // SAVE PLAYER AND ASSOCIATED OBJECTS (ACCOUNT IN THIS CASE)

        $DevAAC->flashNow('success', 'Player '.ucwords(strtolower($req->post('character-name'))).' has been created!');
    }

    render:
    $DevAAC->render('simple.php', $data);

})->via('GET', 'POST');

return array_merge($meta, array('enabled' => true));
