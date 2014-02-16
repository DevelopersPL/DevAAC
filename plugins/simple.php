<?php
/**
 * Developer: Daniel
 * Date: 2/15/14
 * Time: 9:14 PM
 */
use App\models\Account;
use App\models\Player;

$app->map('/', function() use($app) {
    $view = $app->view();
    $view->setTemplatesDirectory('../plugins/templates');
    $req = $app->request;

    $data = array();
    $error = false;
    if($req->isPost()) {
        $data['val'] = $req->post();

        // VALIDATE ACCOUNT NAME
        if( !filter_var($req->post('account-name'), FILTER_VALIDATE_REGEXP,
            array("options" => array("regexp" => "/^[a-zA-Z]{2,12}$/"))) ) {
            $app->flashNow('account-name_class', 'has-error');
            $app->flashNow('danger', 'Account name must have 2-12 characters, only letters.');
            $error = true;
        }

        // VALIDATE CHARACTER NAME
        if( !filter_var($req->post('character-name'), FILTER_VALIDATE_REGEXP,
            array("options" => array("regexp" => "/^[a-zA-Z ]{2,12}$/"))) ) {
            $app->flashNow('character-name_class', 'has-error');
            $app->flashNow('danger', 'Character name must have 6-20 characters, only letters and space.');
            $error = true;
        }

        // VALIDATE VOCATION
        if( ! in_array($req->post('vocation'), array(1, 2, 3, 4)) ) {
            $app->flashNow('vocation_class', 'has-error');
            $error = true;
        }

        // VALIDATE SEX
        if( ! in_array($req->post('sex'), array(0, 1)) ) {
            $app->flashNow('sex_class', 'has-error');
            $error = true;
        }

        // IF VALIDATION ERROR, EXIT
        if($error)
            goto render;

        $account = Account::where('name', $req->post('account-name'))->first();

        // IF ACCOUNT EXISTS AND PASSWORD IS WRONG, EXIT
        if($account && !$account->comparePassword($req->post('password'))) {
            $app->flashNow('danger', 'This account already exists and password is not correct. Cannot add a character.
                                      Enter correct password or try a different account name.');
            $app->flashNow('password_class', 'has-error');
            goto render;
        }

        $name = ucwords(strtolower($req->post('character-name')));
        // check if character name is available
        $player = Player::where('name', $name)->first();
        if($player) {
            $app->flashNow('danger', 'This character already exists.');
            $app->flashNow('character-name_class', 'has-error');
            goto render;
        }

        // IF THE ACCOUNT EXISTS, JUMP TO CREATING PLAYER
        if($account)
            goto createcharacter;

        // VALIDATE PASSWORD ONLY IF THE ACCOUNT DOES NOT EXIST
        if( !filter_var($req->post('password'), FILTER_VALIDATE_REGEXP,
            array("options" => array("regexp" => "/^.{6,20}$/"))) ) {
            $app->flashNow('password_class', 'has-error');
            $app->flashNow('danger', 'Password must have 6-20 characters.');
            $error = true;
        }

        // VALIDATE EMAIL ONLY IF THE ACCOUNT DOES NOT EXIST
        if( !filter_var($req->post('email'), FILTER_VALIDATE_EMAIL) ) {
            $app->flashNow('email_class', 'has-error');
            $app->flashNow('danger', 'Enter valid email address');
            $error = true;
        }

        // IF VALIDATION ERROR, EXIT
        if($error)
            goto render;

        // IF ACCOUNT DOES NOT EXIST, CREATE IT NOW
        $account = App\models\Account::create( array('name' => $req->post('account-name'),
                                          'password' => $req->post('password'),
                                          'email' => $req->post('email'),
                                          'creation' => time()) );

        createcharacter:
        $player = new App\models\Player();
        $player->account()->associate($account);
        $player->name = $name;
        $player->vocation = $req->post('vocation');
        $player->sex = $req->post('sex');
        $player->town_id = 1;
        $player->level = 8;
        $player->push(); // SAVE PLAYER AND ASSOCIATED OBJECTS (ACCOUNT IN THIS CASE)

        $app->flashNow('success', 'Player '.ucwords(strtolower($req->post('character-name'))).' has been created!');
    }

    render:
    $app->render('simple.php', $data);

})->via('GET', 'POST');

return array('name' => 'Simple AAC',
             'description' => 'Dead simple one-page AAC',
             'version' => '0.1');
