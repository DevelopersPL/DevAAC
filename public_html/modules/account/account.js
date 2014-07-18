// Module Route(s)
DevAAC.config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/account', {
    	// When a module contains multiple routes, use 'moduleName/viewName' in PageUrl function.
        templateUrl: PageUrl('account/account'),
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

    $routeProvider.when('/account/register', {
        templateUrl: PageUrl('account/register'),
        controller: 'RegisterController'
    });
}]);

// Module Controller(s)
DevAAC.controller('RegisterController', ['$scope', '$location', 'Account',
	function($scope, $location, Account) {
        $scope.form = {
            name : '',
            email : '',
            password : '',
            passwordAgain : ''
        };
        $scope.errorMessage = '';

        $scope.registerAccount = function() {
            $scope.errorMessage = '';

            if ($scope.form.password !== $scope.form.passwordAgain)
                return $scope.errorMessage = "Passwords don't match!";

            Account.register($scope.form).$promise.then(
                function(data) {
                    $scope.form.name = '';
                    $scope.form.email = '';
                    $scope.form.password = '';
                    $scope.form.passwordAgain = '';
                    $location.path('/account');
                },
                function(error) {
                    console.log(error);
                    $scope.errorMessage = error.statusText + ': ' + error.data.message;
                }
            );
        }
    }
]);

DevAAC.controller('AccountController', ['$scope', '$location', '$cacheFactory', 'Account', 'Player', 'vocations', 'account', 'info',
    function($scope, $location, $cacheFactory, Account, Player, vocations, account, info) {
        $scope.creatingPlayer = false;
        $scope.errorMessage = '';
        $scope.successMessage = '';
        $scope.account = account;
        $scope.players = Player.my();
        $scope.available_vocations = [];
        $scope.newPlayer = {
            name: '',
            vocation: 1,
            sex: 1
        };
        $scope.pwd = {
            password : '',
            passwordAgain : ''
        };

        $scope.vocation = function(id) {
            return _.findWhere(vocations, {id: id});
        };

        for (var i = 0; i < info.allowed_vocations.length; i++)
            $scope.available_vocations.push({id: info.allowed_vocations[i], name: $scope.vocation(info.allowed_vocations[i]).name});

        $scope.createPlayer = function() {
            Player.save($scope.newPlayer, function(data) {
                $scope.players.push(data);
                $scope.successMessage = 'Player has been created!';
                $scope.errorMessage = '';
                $scope.creatingPlayer = false;
                $cacheFactory.get('$http').remove(ApiUrl('accounts/my/players'));
            }, function(error) {
                $scope.successMessage = '';
                $scope.errorMessage = 'Failed to created player. ' + error.data.message;
                $scope.creatingPlayer = false;
            });
        };

        $scope.remove = function(id) {
            Player.delete({id: id}, function(data, status) {
                $scope.players = _.filter($scope.players, function(p) {return p.id != id});
                $scope.successMessage = 'Player has been deleted!';
                $scope.errorMessage = '';
                $scope.creatingPlayer = false;
                $cacheFactory.get('$http').remove(ApiUrl('accounts/my/players'));
            }, function(error) {
                $scope.successMessage = '';
                $scope.errorMessage = 'Failed to delete player. ' + error.data.message;
                $scope.creatingPlayer = false;
            });
        };

        $scope.changePassword = function() {
            if ($scope.pwd.password !== $scope.pwd.passwordAgain)
                return $scope.errorMessage = "Passwords don't match!";

            Account.factory.update({id: $scope.account.id}, $scope.pwd, function(data) {
                $scope.successMessage = 'Password has been updated!';
                $scope.errorMessage = '';
                $scope.changingPassword = false;
                Cookie.set('DevAACToken', Account.generateToken($scope.account.name, $scope.pwd.password), 7);
            }, function(error) {
                $scope.successMessage = '';
                $scope.errorMessage = 'Failed to change password. ' + error.data.message;
                $scope.changingPassword = false;
            });
        }
    }
]);

// Module Factories(s)
DevAAC.factory('Account', ['$http', '$resource', '$cacheFactory',
    function($http, $resource, $cacheFactory) {
        var token;

        return {
            generateToken: function(username, password) {
                var p = new jsSHA(password, 'TEXT');
                return btoa(username + ':' + p.getHash('SHA-1', 'HEX'));
            },
            register: function(account) {
                var self = this;
                return this.factory.save(account, function (data, status) {
                    Cookie.set('DevAACToken', self.generateToken(account.name, account.password), 7);
                });
            },
            authenticate: function(account, password) {
                token = this.generateToken(account, password);
                return $http({
                    url: ApiUrl('accounts/my'),
                    method: 'GET',
                    headers: { Authorization: 'Basic ' + token }
                })
                .success(function (data, status) {
                    Cookie.set('DevAACToken', token, 7);
                });
            },
            logout: function() {
                Cookie.set('DevAACToken', '', 1);
                $cacheFactory.get('$http').removeAll();
            },
            factory: $resource(ApiUrl('accounts/:id'), {}, {
                my: { params: {id: 'my'}, cache: true },
                get: { cache: true },
                query: { isArray: true, cache: true },
                update: { method: 'PUT' },
                recover: { url: ApiUrl('accounts/my/lost'), method: 'POST' }
            })
        }
    }
]);