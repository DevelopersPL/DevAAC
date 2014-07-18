// Footer is a sub-controller of the default template
DevAAC.controller('FooterController', ['$scope',
    function($scope) {
        $scope.year = moment().format('YYYY');
    }
]);
