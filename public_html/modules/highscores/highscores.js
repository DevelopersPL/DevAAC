// Module Route(s)
DevAAC.config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/highscores', {
    	// When a module contains multiple routes, use 'moduleName/viewName' in PageUrl function.
        templateUrl: PageUrl('highscores'),
        controller: 'HighscoresController',
        resolve: {
            vocations: function(Server) {
                return Server.vocations().$promise;
            }
        }
    });
}]);

// Module Controller(s)
DevAAC.controller('HighscoresController', ['$scope', 'Player', 'Server', 'vocations',
    function($scope, Player, Server, vocations) {
        $scope.order = 'level';
        $scope.$watch('order', function(val) {
            $scope.loaded = false;
            $scope.players = Player.query({sort: '-'+val+',-level', limit: 100}, function(val) {
                $scope.loaded = true;
            });
        });

        $scope.players = Player.query(function(val){
            $scope.loaded = true;
            return {sort: '-'+val+',-level', limit: 100};
        });

        $scope.vocation = function(id) {
            return _.findWhere(vocations, {id: id});
        };
    }
]);
