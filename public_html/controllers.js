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
    function($scope, $location
        ) {
        console.log("News controller initialized.");
    });


// GLOBALFOOTER CONTROLLER
DevAAC.controller('globalFooter', function($scope) {
    $scope.footerYear = moment().format('YYYY');
});

DevAAC.controller('userNav', function ($scope, $http, $window) {
    $scope.isAuthenticated = false;
    $scope.welcome = '';
    $scope.message = '';

    $scope.Login = function () {
        $('#loading-login-btn').button('loading');

        $.ajax({
            url: ApiUrl('accounts/my'),
            dataType: 'json',
            async: false,
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Authorization", "Basic " + btoa($scope.login.username + ":" + $scope.login.password));
            },
            success: function (data, status, headers, config) {
                console.log('Login passed');

                $window.sessionStorage.token = btoa($scope.login.username + ":" + $scope.login.password);
                $scope.isAuthenticated = true;
                $scope.username = data.name;
            },
            error: function (data, status, headers, config) {
                console.log('Login error');

                delete $window.sessionStorage.token;
                $scope.isAuthenticated = false;

                // Handle login errors here
                $scope.welcome = '';
            }
        }).always(function () {
            $('#loading-login-btn').button('reset');
            $scope.login = {};
        });
    };

    $scope.Logout = function () {
        $scope.welcome = '';
        $scope.isAuthenticated = false;
        delete $window.sessionStorage.token;
    };
});