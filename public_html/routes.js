/*
    ROUTES
    (Routing all pages and hooking them to their controller)
*/
DevAAC.config(['$routeProvider', function($routeProvider) {
	
	$routeProvider.when('/', {
		templateUrl: PageUrl('news'),
		controller: 'NewsController'
	});

    $routeProvider.when('/account/register', {
        templateUrl: PageUrl('register'),
        controller: 'RegisterController'
    });

    $routeProvider.when('/account', {
        templateUrl: PageUrl('account'),
        controller: 'AccountController',
        resolve: {
            vocations: function(Server) {
                return Server.vocations().$promise;
            }
        }
    });

    $routeProvider.when('/players/online', {
        templateUrl: PageUrl('online'),
        controller: 'OnlineController',
        resolve: {
            vocations: function(Server) {
                return Server.vocations().$promise;
            }
        }
    });

	$routeProvider.when('/players/:id', {
		templateUrl: PageUrl('player'),
		controller: 'PlayerController',
        resolve: {
            vocations: function(Server) {
                return Server.vocations().$promise;
            }
        }
	});

    $routeProvider.when('/guilds', {
        templateUrl: PageUrl('guilds'),
        controller: 'GuildsController'
    });

    $routeProvider.when('/houses', {
        templateUrl: PageUrl('houses'),
        controller: 'HousesController'
    });

    $routeProvider.when('/about', {
        templateUrl: PageUrl('about')
    });

    $routeProvider.when('/rules', {
        templateUrl: PageUrl('rules')
    });

    $routeProvider.when('/404', {
        templateUrl: PageUrl('404')
    });

	$routeProvider.otherwise({
		redirectTo : '/404'
	});
}]);
