DevAAC.config(['$routeProvider', function($routeProvider) {
	
	$routeProvider.when('/', {
		templateUrl: PageUrl('news'),
		controller: 'NewsController'
	});

    $routeProvider.when('/about', {
        templateUrl: PageUrl('about'),
        controller: 'AboutController'
    });

    $routeProvider.when('/account/register', {
        templateUrl: PageUrl('register'),
        controller: 'RegisterController'
    });

    $routeProvider.when('/account', {
        templateUrl: PageUrl('account'),
        controller: 'AccountController',
        resolve: {
            account: function(Account) {
                return Account.factory.my().$promise;
            },
            vocations: function(Server) {
                return Server.vocations().$promise;
            },
            info: function(Server) {
                return Server.info().$promise;
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

	$routeProvider.otherwise({
        templateUrl: PageUrl('404')
	});
}]);
