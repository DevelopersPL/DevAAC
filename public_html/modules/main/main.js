// Module Route(s)
// ...Main is a sub-controller of the default template and don't use a route.

// Module Controller(s)
DevAAC.controller('MainController', ['$scope', 'Server',
    function($scope, Server) {
        $scope.info = Server.info();
    }
]);