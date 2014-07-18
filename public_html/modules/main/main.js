// Main is a sub-controller of the default template

DevAAC.controller('MainController', ['$scope', 'Server',
    function($scope, Server) {
        $scope.info = Server.info();
    }
]);
