// Module Route(s)
DevAAC.config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/about', {
        templateUrl: PageUrl('about'),
        controller: 'AboutController'
    });
}]);

// Module Controller(s)
DevAAC.controller('AboutController', ['$scope', 'Server',
    function($scope, Server) {
        $scope.vocations = Server.vocations();
        $scope.config = Server.config();
    }
]);