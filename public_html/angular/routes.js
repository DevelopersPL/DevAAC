/*
	CONFIG
*/
function PageUrl(page) {
	return "pages/" + page + ".html";
}
function ApiUrl(link) {
	return "http://duots.dondaniello.com/duaac/" + link;
}

/*
	ROUTES
	(Routing all pages and hooking them to their controller)
*/
var app = angular.module('app', ['ngRoute']);
app.config(['$routeProvider', '$locationProvider', function($routeProvider, $locationProvider) {
	
	$routeProvider.when('/angular/', {
		templateUrl: PageUrl('news'),
		controller: 'NewsController'
	});

	$routeProvider.when('/angular/home', {
		templateUrl: PageUrl('news'),
		controller: 'NewsController'
	});

	$routeProvider.when('/angular/players/:id', {
		templateUrl: PageUrl('profile'),
		controller: 'ProfileController'
	});

	$routeProvider.when('/angular/404', {
		templateUrl: PageUrl('404')
	});

    $routeProvider.when('/angular/about', {
        templateUrl: PageUrl('about')
    });

	$routeProvider.otherwise({
		redirectTo : '/angular/404'
	});

    $locationProvider.html5Mode(true);
}]);