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
		$scope.player.sex = playerInfo.sex;
		$scope.player.profession = playerInfo.vocation;
		$scope.player.level = playerInfo.level;
		$scope.player.residence = playerInfo.town_id;
		$scope.player.seen = playerInfo.lastlogin + " → " + playerInfo.lastlogout;
		$scope.player.onlineTime = playerInfo.onlinetime;
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
    function($scope, $location, $routeParams, News) {
        $scope.newsA = News.query(function(result){
            $scope.news = result[0];
        });

        $scope.current = 0;

        $scope.next = function() {

        }

        $scope.previous = function() {

        }

        console.log("News controller initialized.");
    });

// GLOBALFOOTER CONTROLLER
DevAAC.controller('globalFooter', function($scope) {
    $scope.footerYear = moment().format('YYYY');
});

DevAAC.controller('userNav', function ($scope, $http, $window, Account) {
    $scope.isAuthenticated = false;
    $scope.welcome = '';
    $scope.message = '';

    $scope.Always = function() {
    	$('#loading-login-btn').button('reset');
        $scope.login = {};
    };
    
    $scope.Login = function () {
        $('#loading-login-btn').button('loading');
        var token = btoa($scope.login.username + ":" + Sha1.hash($scope.login.password));

        // Removed ajax in favor of angular xhr $http.
        // Migrated previous ajax code into the account class. (Account.authenticate)
        // Look into factories.js for "Account".
        Account.authenticate(token)
        .success(function(data, status) {
        	console.log('Login passed');
            $window.sessionStorage.token = token;
            $scope.isAuthenticated = true;
            $scope.username = data.name;
            $scope.Always();
        })
        .error(function(data, status) {
        	console.log('Login error');
            delete $window.sessionStorage.token;
            $scope.isAuthenticated = false;
            // Handle login errors here
            $scope.welcome = '';
            $scope.Always();
        });
    };

    $scope.Logout = function () {
        $scope.welcome = '';
        $scope.isAuthenticated = false;
        delete $window.sessionStorage.token;
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
		
		Account.register($scope.form.accountName, $scope.form.password, $scope.form.email)
		.success(function(data, status) {
			$scope.form.password = "";
			$scope.successMessage = "Account has been created!";
		})
		.error(function(data, status) {
			$scope.form.password = "";
			$scope.errorMessage = data.message;
		});
	}
});