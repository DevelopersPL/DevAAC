// WIDGET CONTROLLER
app.controller('WidgetController', 
	function($scope, $location, Highscores, Cache
) {
	$scope.playersWidget = {};
	$scope.login = {
		username: "",
		password: ""
	};
	$scope.search = "";

	console.log("Widget controller initialized.");

	Highscores.experience()
	.success(function(data, status) {
		$scope.playersWidget = data.players;
		Cache.setPlayers(data.players);
	});

	$scope.Login = function() {
		console.log("Login button clicked.", $scope.login.username, $scope.login.password);
	}
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
app.controller('ProfileController', 
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
			$scope.SetPlayerData(data.players);
			Cache.setPlayer(data.players);
		});
	} else {
		console.log("Player found in cache.");
		$scope.SetPlayerData($scope.data);
	}
});

// NEWS CONTROLLER
app.controller('NewsController', 
	function($scope, $location
) {
	console.log("News controller initialized.");
});