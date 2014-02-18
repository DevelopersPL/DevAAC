/*
	FACTORIES
	Static classes you import and use in controllers and other factories.
*/

// This will cache players to avoid unneccesary HTTP requests
// Cache object
app.factory("Cache", function($http, $location) {
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
app.factory("Highscores", function($http, $location) {
	return {
		experience: function() {
			return $http({
				url: ApiUrl('players'),
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
app.factory("Player", function($http, $location, Cache) {
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

/* Empty success call: return { success: function(func) { func();}} */