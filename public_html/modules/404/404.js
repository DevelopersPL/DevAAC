// Module Route(s)
DevAAC.config(['$routeProvider', function($routeProvider) {
    $routeProvider.otherwise({
        templateUrl: PageUrl('404')
	});
}]);

// Module Controller(s)
// ...404 don't have a controller yet.