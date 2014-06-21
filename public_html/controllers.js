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

DevAAC.controller('WidgetController', ['$scope', '$location', 'Player', 'Server',
	function($scope, $location, Player, Server) {
        $scope.highExperience = Player.highExperience();
        $scope.info = Server.info();

        $scope.PlayerSearch = function() {
            Player.get({id: $scope.search}, function(value, responseHeaders) {
                $scope.searchMessage = '';
                $location.path('/players/' + value.name);
            }, function(httpResponse) {
                $scope.searchMessage = 'Failed to find player.';
            });
        }
    }
]);

DevAAC.controller('FooterController', ['$scope',
    function($scope) {
        $scope.year = moment().format('YYYY');
    }
]);

DevAAC.controller('NewsController', ['$scope', 'News', 'StatusMessage',
    function($scope, News, StatusMessage) {
        $scope.errorMessage = StatusMessage.error();
        $scope.successMessage = StatusMessage.success();
        $scope.newsA = News.query(function(result){
            $scope.news = result[0];
            $scope.news['date'] = moment($scope.news['date']).format('LLLL');
        });

        $scope.next = function() {
            index = $scope.newsA.indexOf($scope.news);
            if(index >= 0 && index < $scope.newsA.length - 1) {
                $scope.news = $scope.newsA[index + 1];
                $scope.news['date'] = moment($scope.news['date']).format('LLLL');
            }
        };

        $scope.nextAvailable = function() {
            index = $scope.newsA.indexOf($scope.news);
            if(index >= 0 && index < $scope.newsA.length - 1)
                return true;
        };

        $scope.previous = function() {
            index = $scope.newsA.indexOf($scope.news);
            if(index > 0 && index <= $scope.newsA.length - 1) {
                $scope.news = $scope.newsA[index - 1];
                $scope.news['date'] = moment($scope.news['date']).format('LLLL');
            }
        };

        $scope.previousAvailable = function() {
            index = $scope.newsA.indexOf($scope.news);
            if(index > 0 && index <= $scope.newsA.length - 1)
                return true;
        };
    }
]);

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

DevAAC.controller('AccountController', ['$scope', '$location', 'Player', 'vocations', 'account', 'info',
    function($scope, $location, Player, vocations, account, info) {
        $scope.creatingPlayer = 0;
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

        $scope.vocation = function(id) {
            return _.findWhere(vocations, {id: id});
        };

        for (var i = 0; i < info.allowed_vocations.length; i++)
            $scope.available_vocations.push({id: info.allowed_vocations[i], name: $scope.vocation(info.allowed_vocations[i]).name});

        $scope.createPlayer = function() {
            Player.save($scope.newPlayer, function(data, status) {
                $scope.players.push(data);
                $scope.successMessage = 'Player has been created!';
                $scope.creatingPlayer = 2;
            }, function(data, status) {
                $scope.errorMessage = 'Failed to created player. ' + data.message;
                $scope.creatingPlayer = 2;
            });
        };

        $scope.remove = function(id) {
            Player.delete({id: id}, function(data, status) {
                $scope.players = _.filter($scope.players, function(p) {return p.id != id});
                $scope.successMessage = 'Player has been deleted!';
                $scope.creatingPlayer = 2;
            }, function(data, status) {
                $scope.errorMessage = 'Failed to delete player. ' + data.message;
                $scope.creatingPlayer = 2;
            });
        }
    }
]);

DevAAC.controller('PlayerController', ['$scope', '$location', '$routeParams', 'Player', 'Server', 'vocations',
    function($scope, $location, $routeParams, Player, Server, vocations) {
        Player.get({id: $routeParams.id}, function(playerInfo) {
            $scope.player = {
                name: playerInfo.name,
                sex: playerInfo.sex ? 'male' : 'female',
                profession: _.findWhere(vocations, {id: playerInfo.vocation}),
                level: playerInfo.level,
                residence: playerInfo.town_id,
                seen: moment.unix(playerInfo.lastlogin).format('LLLL') + " → " + moment.unix(playerInfo.lastlogout).format('LLLL'),
                onlineTime: moment.duration(playerInfo.onlinetime, 'seconds').humanize()
            };
        });
    }
]);

DevAAC.controller('OnlineController', ['$scope', 'Player', 'Server', 'vocations',
    function($scope, Player, Server, vocations) {
        $scope.players = Player.queryOnline();
        $scope.loadingView = true;

        $scope.vocation = function(id) {
            return _.findWhere(vocations, {id: id});
        };
    }
]);

DevAAC.controller('GuildsController', ['$scope', 'Guild',
    function($scope, Guild) {
        $scope.guilds = Guild.query({embed: 'owner'});
    }
]);

DevAAC.controller('HousesController', ['$scope', 'House', 'Player',
    function($scope, House, Player) {
        $scope.houses = House.query(function(){
            $scope.loaded = true;
        });

        $scope.players = [];

        $scope.player = function(id) {
            if(id == 0)
                return;

            if($scope.players[id] == undefined)
                $scope.players[id] = Player.get({id: id});

            return $scope.players[id];
        }
    }
]);
