/*
	FACTORIES
	Static classes you import and use in controllers and other factories.
*/

// This will cache players to avoid unneccesary HTTP requests
// Cache object
DevAAC.factory("Cache", function($http, $location) {
	var players = false;

	return {
		// Returns player from cache or false.
		findPlayerId: function(player_id) {
			if (players != false) {
				for (var i = 0; i < players.length; i++) {
					if (players[i].id == player_id) return players[i];
				}
			}
			return false;
		},
		findPlayerName: function(player_name) {
			if (players != false) {
				for (var i = 0; i < players.length; i++) {
					if (players[i].name.toLowerCase() == player_name.toLowerCase()) return players[i];
				}
			}
			return false;
		},
		// Set cache with array of players
		setPlayers: function(data) {
			if (players == false) {
                players = data;
			} else {
				// Add any player in this array that is not in cache
				for (var i = 0; i < data.length; i++) {
					var result = false;
					for (var x = 0; x < players.length; x++) {
						if (players[x].id == data[i].id) result = players[x];
					}
					if (result == false) {
						players.push(data[i]);
					}
				}
			}
			return true;
		},
		setPlayer: function(playerdata) {
			console.log("Set player data to cache:", playerdata);
			if (players == false) {
				players = new Array();
			}
			players.push(playerdata);
			return true;
		}
	}
});

// Highscore API (etc fetch top players)
DevAAC.factory("Highscores", function($http, $location) {
	return {
		experience: function() {
			return $http({
				url: ApiUrl('topplayers'),
				method: 'GET',
				headers: { 'Content-Type': 'application/json' }
				//data: JSON.stringify({year: yearString})
			})
			.success(function (data, status) {
				console.log(data, status);
			})
			.error(function (data, status) {
				console.log(data, status);
			});
		}
	}
});

// Player API
DevAAC.factory("Player", function($http, $location, Cache) {
	return {
		get: function(player_id) {
			return $http({
				url: ApiUrl('players/'+player_id),
				method: 'GET',
				headers: { 'Content-Type': 'application/json' }
				//data: JSON.stringify({year: yearString})
			})
			.success(function (data, status) {
				console.log(data, status);
			})
			.error(function (data, status) {
				console.log(data, status);
			});
		}
	}
});


/*
 $http.get(ApiUrl('api/command')).success(function (data, status, headers, config) {
    // success handle
 }).error(function (data, status, headers, config) {
    // error handle
 });
 */
// Add auth header to all $http request (example above)
DevAAC.factory('authInterceptor', function ($rootScope, $q, $window) {
    return {
        request: function (config) {
            config.headers = config.headers || {};
            if ($window.sessionStorage.token) {
                config.headers.Authorization = 'Basic ' + $window.sessionStorage.token;
            }
            return config;
        },
        response: function (response) {
            if (response.status === 401) {
                console.log('User is not logged in');
                // TODO: Add $rootscope.isAuthenticated globally.
            }
            return response || $q.when(response);
        }
    };
});

DevAAC.config(function ($httpProvider) {
    $httpProvider.interceptors.push('authInterceptor');
});