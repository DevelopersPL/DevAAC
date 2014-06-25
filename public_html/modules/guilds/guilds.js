// Module Route(s)
DevAAC.config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/guilds', {
        templateUrl: PageUrl('guilds/guilds'),
        controller: 'GuildsController'
    });
    $routeProvider.when('/guilds/:id', {
        templateUrl: PageUrl('guilds/guild'),
        controller: 'GuildController'
    });
}]);

// Module Controller(s)
DevAAC.controller('GuildsController', ['$scope', 'Guild',
    function($scope, Guild) {
        $scope.guilds = Guild.query({embed: 'owner'});
    }
]);
DevAAC.controller('GuildController', ['$scope', 'Guild', '$routeParams', '$location', 
    function($scope, Guild, $routeParams, $location) {
        $scope.guild = false;
        Guild.get({guildId: $routeParams.id}, function(guildInfo) {
            $scope.guild = {
                name: guildInfo.name,
                created: guildInfo.creationdata,
                motd: guildInfo.motd
            };
        }, function(response) {
            if (response.status === 404) {
                $location.path('/guilds');
            }
        });
    }
]);

// Module Factories(s)
DevAAC.factory('Guild', ['$resource',
    function($resource){
        return $resource(ApiUrl('guilds/:guildId'), {}, {
            get: { cache: true },
            query: { isArray: true, cache: true }
        });
    }
]);