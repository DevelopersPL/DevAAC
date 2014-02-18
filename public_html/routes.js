/*
	CONFIG
*/
function PageUrl(page) {
	return "pages/" + page + ".html";
}
function ApiUrl(link) {
	return "http://duots.dondaniello.com/devaac/" + link;
}

/*
	ROUTES
	(Routing all pages and hooking them to their controller)
*/
var app = angular.module('app', ['ngRoute']);
app.config(['$routeProvider', '$locationProvider', function($routeProvider, $locationProvider) {
	
	$routeProvider.when('/', {
		templateUrl: PageUrl('news'),
		controller: 'NewsController'
	});

	$routeProvider.when('/home', {
		templateUrl: PageUrl('news'),
		controller: 'NewsController'
	});

	$routeProvider.when('/players/:id', {
		templateUrl: PageUrl('profile'),
		controller: 'ProfileController'
	});

	$routeProvider.when('/404', {
		templateUrl: PageUrl('404')
	});

    $routeProvider.when('/about', {
        templateUrl: PageUrl('about')
    });

	$routeProvider.otherwise({
		redirectTo : '/404'
	});
}]);