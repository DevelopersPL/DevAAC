/*
    ROUTES
    (Routing all pages and hooking them to their controller)
*/
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

    $routeProvider.when('/account', {
        templateUrl: PageUrl('account'),
        controller: 'AccountController'
    });

    $routeProvider.when('/rules', {
        templateUrl: PageUrl('rules'),
        controller: 'RulesController'
    });

    $routeProvider.when('/players/online', {
        templateUrl: PageUrl('online'),
        controller: 'AccountController'
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

DevAAC.directive("markdown", function ($compile, $http) {
    var converter = new Showdown.converter();
    return {
        restrict: 'E',
        replace: true,
        link: function (scope, element, attrs) {
            if ("src" in attrs) {
                $http.get(attrs.src).then(function(data) {
                    element.html(converter.makeHtml(data.data));
                });
            } else {
                element.html(converter.makeHtml(element.text()));
            }
        }
    };
});

DevAAC.filter('markdown', function($sce) {
    var converter = new Showdown.converter();
    return function(input) {
        if(input)
            return $sce.trustAsHtml(converter.makeHtml(input))
    };
});
