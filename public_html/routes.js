/*
	CONFIG
*/
function PageUrl(page) {
	return "pages/" + page + ".html";
}
function ApiUrl(link) {
	return "http://duots.dondaniello.com/devaac/" + link;
}

function url_base64_decode(str) {
    var output = str.replace('-', '+').replace('_', '/');
    switch (output.length % 4) {
        case 0:
            break;
        case 2:
            output += '==';
            break;
        case 3:
            output += '=';
            break;
        default:
            throw 'Illegal base64url string!';
    }
    return window.atob(output); //polifyll https://github.com/davidchambers/Base64.js
}

/*
	ROUTES
	(Routing all pages and hooking them to their controller)
*/
var DevAAC = angular.module('app', ['ngRoute']);
DevAAC.config(['$routeProvider', '$locationProvider', function($routeProvider, $locationProvider) {
	
	$routeProvider.when('/', {
		templateUrl: PageUrl('news'),
		controller: 'NewsController'
	});

	$routeProvider.when('/home', {
		templateUrl: PageUrl('news'),
		controller: 'NewsController'
	});

    $routeProvider.when('/account/register', {
        templateUrl: PageUrl('register'),
        controller: 'RegisterController'
    });

    $routeProvider.when('/rules', {
        templateUrl: PageUrl('rules'),
        controller: 'RulesController'
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