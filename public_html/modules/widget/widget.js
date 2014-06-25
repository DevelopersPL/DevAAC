// Module Route(s)
// ...Widget is a sub-controller of the default template and don't use a route.

// Module Controller(s)
DevAAC.controller('WidgetController', ['$scope', '$location', 'Player',
	function($scope, $location, Player) {
        $scope.highExperience = Player.highExperience();

        $scope.PlayerSearch = function() {
            Player.get({id: $scope.search}, function(value, responseHeaders) {
                $scope.searchMessage = '';
                $location.path('/players/' + value.name);
            }, function(httpResponse) {
                $scope.searchMessage = 'Failed to find player.';
            });
        }
    }
]);