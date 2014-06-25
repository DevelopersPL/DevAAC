// Module Route(s)
// ...Footer is a sub-controller of the default template and don't use a route.

// Module Controller(s)
DevAAC.controller('FooterController', ['$scope',
    function($scope) {
        $scope.year = moment().format('YYYY');
    }
]);