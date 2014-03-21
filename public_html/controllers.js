// WIDGET CONTROLLER
DevAAC.controller('WidgetController',
	function($scope, $location, Highscores, Cache
        ) {
       $scope.playersWidget = {};
       $scope.search = "";

       console.log("Widget controller initialized.");

       Highscores.experience()
       .success(function(data, status) {
          $scope.playersWidget = data;
          Cache.setPlayers(data);
      });

       $scope.PlayerSearch = function() {
          console.log("Search button clicked.", $scope.search);
          var player = Cache.findPlayerName($scope.search);
          if (player != false) {
             $location.path('/players/'+player.id);
         } else {
			// Todo: Wait for player search API.
			console.log("Player not found in cache, API for player search not done.");
		}
	}
});

// PROFILE CONTROLLER
DevAAC.controller('ProfileController',
	function($scope, $location, $routeParams, Player, Cache
        ) {
       $scope.player = {
          name: "Loading...",
          sex: "",
          profession: "",
          level: "",
          residence: "",
          seen: "",
          onlineTime: "",
          accountStatus: "N/A"
      };

	// Update view with player data
	$scope.SetPlayerData = function(playerInfo) {
		console.log(playerInfo);
		$scope.player.name = playerInfo.name;
		$scope.player.sex = playerInfo.sex ? 'male' : 'female';
		$scope.player.profession = playerInfo.vocation;
		$scope.player.level = playerInfo.level;
		$scope.player.residence = playerInfo.town_id;
		$scope.player.seen = moment.unix(playerInfo.lastlogin).format('LLLL') + " → " + moment.unix(playerInfo.lastlogout).format('LLLL');
		$scope.player.onlineTime = moment.duration(playerInfo.onlinetime, 'seconds').humanize();
	}

	console.log("Profile controller initialized.");
	
	// Check Cache for player
	$scope.data = Cache.findPlayerId($routeParams.id);
	if ($scope.data == false) {
		console.log("Player not in cache, fetching from API.");
		// Since player not found in cache, fetch it from API
		Player.get($routeParams.id)
		.success(function(data, status) {
			$scope.SetPlayerData(data);
			Cache.setPlayer(data);
		});
	} else {
		console.log("Player found in cache.");
		$scope.SetPlayerData($scope.data);
	}
});

// NEWS CONTROLLER
DevAAC.controller('NewsController',
    function($scope, $location, $routeParams, News, StatusMessage) {
        $scope.errorMessage = StatusMessage.error();
        $scope.successMessage = StatusMessage.success();
        $scope.newsA = News.query(function(result){
            $scope.news = result[0];
            $scope.news['date'] = moment($scope.news['date']).format('LLLL');
        });

        $scope.next = function() {
            index = $scope.newsA.indexOf($scope.news);
            if(index >= 0 && index < $scope.newsA.length - 1) {
                $scope.news = $scope.newsA[index + 1];
                $scope.news['date'] = moment($scope.news['date']).format('LLLL');
            }
        }

        $scope.nextAvailable = function() {
            index = $scope.newsA.indexOf($scope.news);
            if(index >= 0 && index < $scope.newsA.length - 1)
                return true;
        }

        $scope.previous = function() {
            index = $scope.newsA.indexOf($scope.news);
            if(index > 0 && index <= $scope.newsA.length - 1) {
                $scope.news = $scope.newsA[index - 1];
                $scope.news['date'] = moment($scope.news['date']).format('LLLL');
            }
        }

        $scope.previousAvailable = function() {
            index = $scope.newsA.indexOf($scope.news);
            if(index > 0 && index <= $scope.newsA.length - 1)
                return true;
        }

        console.log("News controller initialized.");
    });

// GLOBALFOOTER CONTROLLER
DevAAC.controller('globalFooter', function($scope) {
    $scope.footerYear = moment().format('YYYY');
});

DevAAC.controller('NavigationController', function ($scope, $http, $window, Account, WindowSession, $location) {
    $scope.message = '';
    $scope.account = false;
    $scope.login = {
    	username: "",
    	password: ""
    };
    $scope.online = false;
    $scope.waiting = false;
    $scope.checked = false;

    console.log("NavigationController controller is initialized.");

    $scope.Always = function() {
    	$('#loading-login-btn').button('reset');
    	$scope.login.username = "";
        $scope.login.password = "";
    };
    
    // This will log in user from cookie if it exist.
    $scope.checkCookie = function() {
    	// Check if we got cookie token from previous login, as long as we are not logged in.
    	if (!$scope.waiting && !$scope.online && !$scope.checked && Cookie.get('DevAACToken') !== false) {
    		$scope.waiting = true;
    		Account.authenticate(Cookie.get('DevAACToken'))
    		.success(function(data, status) {
    			console.log("Logged in from cookie.");
    			$scope.account = data;
    			$scope.waiting = false;
    			$scope.checked = true;
    		})
    		.error(function() {
    			console.log("Failed to authenticate from cookie.");
    			$scope.waiting = false;
    			$scope.checked = true;
    		});
    	}
    	// No point to check user if we are still waiting for API response
    	if (!$scope.waiting) return $scope.checkUser();
    }
    /* This will check if user is online
    // If online it will check if it got the account data
    // If he don't got it, fetch it.
    // If not online but have account data, remove account data.
    // So this takes care of all the neccesary flow of information regarding login/logout.
    // So this will automatically detect, set and logout user depending on available
    //  information in the Account model. */
    $scope.checkUser = function() {
    	$scope.online = WindowSession.checkToken();
    	if ($scope.online) {
    		if ($scope.account === false) {
    			$scope.account = Account.getAccount();
    		} else {
    			if ($scope.account !== false) {
    				$scope.account = false;
    			}
    		}
    	}
    	return $scope.online;
    }

    $scope.Login = function () {
        $('#loading-login-btn').button('loading');
        var token = btoa($scope.login.username + ":" + Sha1.hash($scope.login.password));

        // Removed ajax in favor of angular xhr $http.
        // Migrated previous ajax code into the account class. (Account.authenticate)
        // Look into factories.js for "Account".
        Account.authenticate(token)
        .success(function(data, status) {
        	$scope.checked = true;
            $scope.account = data;
            WindowSession.registerToken(Account.getToken());
            $scope.Always();
            $location.path('/account');
        })
        .error(function(data, status) {
        	console.log('Login error');
           WindowSession.removeToken();
           $scope.Always();
       });
    };

    $scope.Logout = function () {
        $scope.Always();
        WindowSession.removeToken();
    };
});

// REGISTER CONTROLLER
DevAAC.controller('RegisterController',
	function($scope, $location, Account
        ) {
	// Scope variables
	$scope.form = {
		accountName : "",
		email : "",
		password : "",
		passwordAgain : ""
	};
	$scope.errorMessage = "";
	$scope.successMessage = "";

	console.log("Register controller initialized.");

	$scope.registerAccount = function() {
		// Reset notification
		if ($scope.errorMessage.length > 1) $scope.errorMessage = "";
		
		// Verify that passwords match
		if ($scope.form.password !== $scope.form.passwordAgain) {
			$scope.errorMessage = "Password mismatch.";
		}
		// Remove traces of password in plain format, convert it to SHA1.
		$scope.form.password = Sha1.hash($scope.form.password);
		$scope.form.passwordAgain = "";
		
		// Registering account, extending success and error with logic
		Account.register($scope.form.accountName, $scope.form.password, $scope.form.email)
		.success(function(data, status) {
			// Auto login:
			var token = btoa($scope.form.accountName + ":" + Sha1.hash($scope.form.password));
			// TODO Authenticate. (Need to remake mrWogus system)
			// Clearing password form
			$scope.form.password = "";
			$scope.successMessage = "Account has been created!";
		})
		.error(function(data, status) {
			$scope.form.password = "";
			$scope.errorMessage = data.message;
		});
	}
});

// RULES CONTROLLER
DevAAC.controller('RulesController',
	function($scope
        ) {
       console.log("Rules controller initialized.");
   });

// ACCOUNT CONTROLLER
DevAAC.controller('AccountController',
    function($scope, $interval, $location, Account, StatusMessage
        ) {
        $scope.page = 1;
        $scope.creatingCharacter = 0;
        $scope.errorMessage = "";
        $scope.successMessage = "";
        $scope.noticeMessage = "";
        $scope.account = Account.getAccount();
        $scope.players = [];
        $scope.newPlayer = {
            name: '',
            vocation: 1,
            sex: 1
        }

        console.log("Account controller initialized.");

    $scope.createPlayer = function() {
        Account.createPlayer($scope.newPlayer.name, $scope.newPlayer.vocation, $scope.newPlayer.sex)
        .success(function(data, status) {
            $scope.players.push(data);
            console.log($scope.players);
            $scope.errorMessage = "";
            $scope.successMessage = "Player has been created!";
            $scope.creatingCharacter = 2;
        })
        .error(function(data, status) {
            $scope.successMessage = "";
            $scope.errorMessage = "Failed to created character. "+data.message;
            $scope.creatingCharacter = 2;
        });
    };
    $scope.startPlayerCreation = function() {
        $scope.creatingCharacter = 1;
    };
    $scope.stopPlayerCreation = function() {
        $scope.creatingCharacter = 0;
        $scope.newPlayer = {
            name: '',
            vocation: 1,
            sex: 1
        }
    };

    $scope.showAccountInformation = function() {
        // Ready to proceed with account data.

        // Fetch player list
        Account.getAccountPlayers()
        .success(function(data, status) {
            $scope.players = data;
        });

        // Ready to display the page
        $scope.page = 2;
    };

    // If you are not logged in, throw you to home. 
    // Yeah, I really need to find a better way to authenticate the user. This async authentication is a mess.
    // Gotta learn to properly use $q promise and resolve.
    if (!$scope.account) {
        if (!Account.isAuthenticating()) {
            StatusMessage.setError('You need to login first.');
            $location.path('/home');
        } else {
            console.log("Authentication is in progress. Waiting for result.");
            var waitForLogin = $interval(function(){
                $scope.account = Account.getAccount();
                if ($scope.account != false) {
                    $interval.cancel(waitForLogin);
                    console.log("Waited and user is now logged in.");
                    $scope.showAccountInformation();
                }
            },50);
        }
    } else {
        console.log("You are already logged in. :)");
        $scope.showAccountInformation();
    }
});