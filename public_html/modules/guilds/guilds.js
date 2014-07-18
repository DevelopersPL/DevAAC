// Module Route(s)
DevAAC.config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/guilds', {
        templateUrl: PageUrl('guilds/guilds'),
        controller: 'GuildsController'
    });
    $routeProvider.when('/guilds/:id', {
        templateUrl: PageUrl('guilds/guild'),
        controller: 'GuildController',
        resolve: {
            vocations: function(Server) {
                return Server.vocations().$promise;
            },
            guild: function(Guild, $route) {
                return Guild.get({id: $route.current.params.id}).$promise;
            }
        }
    });
}]);

// Module Controller(s)
DevAAC.controller('GuildsController', ['$scope', 'Guild',
    function($scope, Guild) {
        $scope.guilds = Guild.query({embed: 'owner'}, function() {
            $scope.loaded = true;
        });
    }
]);
DevAAC.controller('GuildController', ['$scope', '$route', '$location', 'Server', 'Guild', 'Player', 'vocations', 'guild',
    function($scope, $route, $location, Server, Guild, Player, vocations, guild) {
        $scope.guild = guild;

        $scope.vocation = function(id) {
            return _.findWhere(vocations, {id: id});
        };

        $scope.rank = function(id) {
            return _.findWhere(guild.ranks, {id: id});
        };

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
DevAAC.factory('Guild', ['$resource',
    function($resource){
        return $resource(ApiUrl('guilds/:id'), {}, {
            get: { cache: true, params: { embed: 'owner,members,invitations,ranks' } },
            query: { isArray: true, cache: true }
        });
    }
]);
