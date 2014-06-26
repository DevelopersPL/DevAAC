// Module Route(s)
// ...Widget is a sub-controller of the default template and doesn't use a route.

// Module Controller(s)
DevAAC.controller('WidgetController', ['$scope', '$location', 'Player',
	function($scope, $location, Player) {
        $scope.highExperience = Player.highExperience();

        $scope.goToPlayer = function() {
            Player.get({id: $scope.search}, function(value) {
                $scope.searchError = '';
                $location.path('/players/' + value.name);
            }, function(httpResponse) {
                $scope.searchError = 'Player not found!';
            });
        };

        $scope.findPlayers = function(name) {
            return Player.query({q: name, limit: 10, fields: 'name'}).$promise;
        };
    }
]);