<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>OTS</title>

    <!-- Bootstrap 3: http://getbootstrap.com/getting-started/#download -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">

    <style>
        body {
            padding-top: 20px;
            padding-bottom: 20px;
        }

        .jumbotron {
            text-align: center;
            border-bottom: 1px solid #e5e5e5;
        }

        .header {
            border-bottom: 1px solid #e5e5e5;
            margin-bottom: 30px;
        }

        .header h3 {
            padding-bottom: 19px;
            margin-top: 0;
            margin-bottom: 0;
            line-height: 40px;
        }

        .footer {
            padding-top: 19px;
            color: #777;
            border-top: 1px solid #e5e5e5;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 col-md-6 col-md-offset-3">

            <div class="jumbotron">
                <h1>Hello!</h1>
                <p>Here you can easily create your account and a character! If you already have an account,
                fill in your current details and a new character will be added to your account!</p>
            </div>

            <?php
            if($flash['success'])
                echo '<div class="alert alert-success">' . $flash['success'] . '</div>';
            if($flash['info'])
                echo '<div class="alert alert-info">' . $flash['info'] . '</div>';
            if($flash['danger'])
                echo '<div class="alert alert-danger">' . $flash['danger'] . '</div>';
            ?>

            <form class="form-horizontal" role="form" method="post">
                <div class="form-group <?=$flash['email_class']?>">
                    <label for="email" class="col-sm-3 control-label">Email</label>
                    <div class="col-sm-9">
                        <input type="email" class="form-control" name="email" id="email" value="<?=@$val['email']?>" placeholder="Email (leave empty if only adding character)" autofocus>
                    </div>
                </div>
                <div class="form-group <?=$flash['account-name_class']?>">
                    <label for="account-name" class="col-sm-3 control-label">Account name</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="account-name" id="account-name" value="<?=@$val['account-name']?>" placeholder="Account name" required minlength="2" maxlength="12">
                    </div>
                </div>
                <div class="form-group <?=$flash['password_class']?>">
                    <label for="password" class="col-sm-3 control-label">Password</label>
                    <div class="col-sm-9">
                        <input type="password" class="form-control" name="password" id="password" value="<?=@$val['password']?>" placeholder="Password" required minlength="6" maxlength="20">
                    </div>
                </div>
                <hr />
                <div class="form-group <?=$flash['character-name_class']?>">
                    <label for="character-name" class="col-sm-4 control-label">Character name</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="character-name" id="character-name" value="<?=@$val['character-name']?>" placeholder="Character name" required minlength="6" maxlength="20">
                    </div>
                </div>
                <div class="form-group <?=$flash['sex_class']?>">
                    <label for="sex" class="col-sm-4 control-label">Sex</label>
                    <div class="col-sm-4">
                        <div class="radio">
                            <label>
                                <input type="radio" name="sex" id="sex" value="1" <?php echo isset($val['sex'])? ($val['sex'] === '1' ? 'checked' : '') : 'checked' ?>>
                                Male
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="sex" id="sex" value="0" <?php echo @$val['sex'] === '0' ? 'checked' : '' ?>>
                                Female
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group <?=$flash['vocation_class']?>">
                    <label for="vocation" class="col-sm-4 control-label">Vocation</label>
                    <div class="col-sm-4">
                        <div class="radio">
                            <label>
                                <input type="radio" name="vocation" id="vocation" value="1" <?php echo isset($val['vocation'])? ($val['vocation']==1 ? 'checked' : '') : 'checked' ?>>
                                Sorcerer
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="vocation" id="vocation" value="2" <?php echo @$val['vocation'] == 2 ? 'checked' : '' ?>>
                                Druid
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="vocation" id="vocation" value="3" <?php echo @$val['vocation'] == 3 ? 'checked' : '' ?>>
                                Paladin
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="vocation" id="vocation" value="4" <?php echo @$val['vocation'] == 4 ? 'checked' : '' ?>>
                                Knight
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12 text-center">
                        <button type="submit" class="btn btn-primary btn-lg">Create account & character!</button>
                    </div>
                </div>
            </form>

            <div class="footer">
                <p>&copy; <?=date('Y')?> developers.pl</p>
            </div>

        </div>
    </div>
</div>
</body>
</html>
