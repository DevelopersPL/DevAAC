// Module Route(s)
// ...Header is a sub-controller of the default template and don't use a route.

// Module Controller(s)
DevAAC.controller('HeaderController', ['$scope', 'Server',
    function($scope, Server) {
        Server.info(function(i) {
            $scope.name = i.serverName;
            document.title = i.serverName;
        });
    }
]);