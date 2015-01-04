// Main is a sub-controller of the default template

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
