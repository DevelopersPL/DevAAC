// Module Route(s)
// ...Navigation is a sub-controller of the default template and don't use a route.

// Module Controller(s)
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