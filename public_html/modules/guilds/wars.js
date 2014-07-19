// Module Route(s)
DevAAC.config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/guilds/wars', {
        templateUrl: PageUrl('guilds/wars'),
        controller: 'WarsController'
    });

    $routeProvider.when('/guilds/wars/:id', {
        templateUrl: PageUrl('guilds/war'),
        controller: 'WarController',
        resolve: {
            vocations: function(Server) {
                return Server.vocations().$promise;
            },
            war: function(War, $route) {
                return War.get({id: $route.current.params.id}).$promise;
            }
        }
    });
}]);

// Module Controller(s)
DevAAC.controller('WarsController', ['$scope', 'War',
    function($scope, War) {
        $scope.wars = War.query(function() {
            $scope.loaded = true;
        });
    }
]);

DevAAC.controller('WarController', ['$scope', '$route', '$location', 'Server', 'War', 'Player', 'vocations', 'war',
    function($scope, $route, $location, Server, War, Player, vocations, war) {

        $scope.war = war;

        $scope.players = [];
        $scope.player = function(id) {
            if(id == 0)
                return;

            if($scope.players[id] == undefined)
                $scope.players[id] = Player.get({id: id});

            return $scope.players[id];
        };
    }
 ]);

// Module Factories(s)
DevAAC.factory('War', ['$resource',
    function($resource) {
        return $resource(ApiUrl('guilds/wars/:id'), {}, {
            get: { cache: true, params: { embed: 'kills' } },
            query: { isArray: true, cache: true }
        });
    }
]);