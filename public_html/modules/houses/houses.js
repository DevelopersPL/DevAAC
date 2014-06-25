// Module Route(s)
DevAAC.config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/houses', {
        templateUrl: PageUrl('houses'),
        controller: 'HousesController'
    });
}]);

// Module Controller(s)
DevAAC.controller('HousesController', ['$scope', 'House', 'Player',
    function($scope, House, Player) {
        $scope.houses = House.query(function(){
            $scope.loaded = true;
        });

        $scope.order = 'size';
        $scope.orderReverse = true;
        $scope.players = [];

        $scope.player = function(id) {
            if(id == 0)
                return;

            if($scope.players[id] == undefined)
                $scope.players[id] = Player.get({id: id});

            return $scope.players[id];
        }
    }
]);

// Module Factories(s)
DevAAC.factory('House', ['$resource',
    function($resource){
        return $resource(ApiUrl('houses/:guildId'), {}, {
            get: { cache: true },
            query: { isArray: true, cache: true }
        });
    }
]);
