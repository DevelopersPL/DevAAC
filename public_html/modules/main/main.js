DevAAC.controller('MainController', ['$scope', 'Server', 'Account',
    function($scope, Server, Account) {
        $scope.info = Server.info();

        $scope.isLoggedIn = function() {
            return Cookie.get('DevAACToken');
        };

        $scope.lostPassword = function () {
            $('#loading-lostpw-btn').button('loading');

            Account.factory.recover({email: $('#inputEmail').val()}, function() {
                $('#recoverResponse').removeClass('hidden').addClass('text-success')
                    .text('Your account name and new password has been sent to your e-mail address.');

                $('#loading-lostpw-btn').remove();
            }, function(response) {

                message = response.statusText;
                if (response.data.message != undefined)
                    message = response.data.message;

                $('#recoverResponse').removeClass('hidden').addClass('text-danger').text(message);
                $('#loading-lostpw-btn').button('reset');
            });
        };
    }
]);

DevAAC.controller('HeaderController', ['$scope', 'Server',
    function($scope, Server) {
        Server.info(function(i) {
            $scope.name = i.serverName;
            document.title = i.serverName;
        });
    }
]);

DevAAC.controller('NavigationController', ['$scope', '$location', 'Account',
    function ($scope, $location, Account) {
        $scope.form = {
            name: '',
            password: ''
        };

        $scope.isActive = function(route) {
            return route === $location.path();
        };

        $scope.isLoggedIn = function() {
            return Cookie.get('DevAACToken');
        };

        if($scope.isLoggedIn())
            $scope.account = Account.factory.my();

        $scope.Login = function () {
            $('#loading-login-btn').button('loading');
            Account.authenticate($scope.form.name, $scope.form.password)
                .success(function(data, status) {
                    $location.path('/account');
                    $scope.form.password = '';
                    $scope.account = Account.factory.my();
                })
                .error(function(data, status) {
                    $('#loading-login-btn').button('reset');
                    $scope.form.password = '';
                });
        };

        $scope.Logout = function () {
            Account.logout();
            $location.path('/');
            $scope.account = null;
        };
    }
]);

DevAAC.controller('FooterController', ['$scope',
    function($scope) {
        $scope.year = moment().format('YYYY');
    }
]);

DevAAC.controller('WidgetController', ['$scope', '$location', 'Player',
    function($scope, $location, Player) {
        $scope.highExperience = Player.highExperience();

        $scope.goToPlayer = function() {
            Player.get({id: $scope.search}, function(value) {
                $scope.searchError = '';
                $location.path('/players/' + value.name);
            }, function(httpResponse) {
                $scope.searchError = 'Player not found!';
            });
        };

        $scope.findPlayers = function(name) {
            return Player.query({q: name, limit: 10, fields: 'name'}).$promise;
        };
    }
]);
