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
var app = angular.module('app', ['ngRoute']).config(function($routeProvider) {
	
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

	$routeProvider.otherwise({
		redirectTo : '/404'
	});
});