// Header is a sub-controller of the default template
DevAAC.controller('HeaderController', ['$scope', 'Server',
    function($scope, Server) {
        Server.info(function(i) {
            $scope.name = i.serverName;
            document.title = i.serverName;
        });
    }
]);
