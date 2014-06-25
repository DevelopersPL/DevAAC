// Module Route(s)
DevAAC.config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/players/online', {
    	// When a module contains multiple routes, use 'moduleName/viewName' in PageUrl function.
        templateUrl: PageUrl('players/online'),
        controller: 'OnlineController',
        resolve: {
            vocations: function(Server) {
                return Server.vocations().$promise;
            }
        }
    });

	$routeProvider.when('/players/:id', {
		templateUrl: PageUrl('players/player'),
		controller: 'PlayerController',
        resolve: {
            vocations: function(Server) {
                return Server.vocations().$promise;
            }
        }
	});
}]);

// Module Controller(s)
DevAAC.controller('PlayerController', ['$scope', '$location', '$routeParams', 'Player', 'Server', 'vocations',
    function($scope, $location, $routeParams, Player, Server, vocations) {
        Player.get({id: $routeParams.id}, function(playerInfo) {
            $scope.player = {
                name: playerInfo.name,
                sex: playerInfo.sex ? 'male' : 'female',
                profession: _.findWhere(vocations, {id: playerInfo.vocation}),
                level: playerInfo.level,
                residence: playerInfo.town_id,
                seen: moment.unix(playerInfo.lastlogin).format('LLLL') + " → " + moment.unix(playerInfo.lastlogout).format('LLLL'),
                onlineTime: moment.duration(playerInfo.onlinetime, 'seconds').humanize()
            };
        });
    }
]);

DevAAC.controller('OnlineController', ['$scope', 'Player', 'Server', 'vocations',
    function($scope, Player, Server, vocations) {
        $scope.players = Player.queryOnline();
        $scope.loadingView = true;

        $scope.vocation = function(id) {
            return _.findWhere(vocations, {id: id});
        };
    }
]);

// Module Factories(s)
DevAAC.factory('Player', ['$resource',
    function($resource) {
        return $resource(ApiUrl('players/:id'), {}, {
            get: { cache: true },
            queryOnline: { params: {id: 'online', embed: 'player'}, isArray: true, cache: true },
            highExperience: { params: {sort: '-experience', limit: 5}, isArray: true, cache: true },
            my: { url: ApiUrl('accounts/my/players'), isArray: true, cache: true }
        });
    }
]);