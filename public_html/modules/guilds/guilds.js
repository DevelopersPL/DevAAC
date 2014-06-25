// Module Route(s)
DevAAC.config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/guilds', {
        templateUrl: PageUrl('guilds'),
        controller: 'GuildsController'
    });
}]);

// Module Controller(s)
DevAAC.controller('GuildsController', ['$scope', 'Guild',
    function($scope, Guild) {
        $scope.guilds = Guild.query({embed: 'owner'});
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