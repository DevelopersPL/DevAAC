// Module Route(s)
DevAAC.config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/houses', {
        templateUrl: PageUrl('houses'),
        controller: 'HousesController',
        resolve: {
            info: function(Server) {
                return Server.info().$promise;
            }
        }
    });
    $routeProvider.when('/houses/:id', {
        templateUrl: PageUrl('houses/house'),
        controller: 'HouseController',
        resolve: {
            info: function(Server) {
                return Server.info().$promise;
            },
            house: function(House, $route) {
                return House.get({id: $route.current.params.id}).$promise;
            }
        }
    });
}]);

// Module Controller(s)
DevAAC.controller('HouseController', ['$scope', '$routeParams', '$location', 'House', 'Player', 'Server', 'info', 'house',
    function($scope, $routeParams, $location, House, Player, Server, info, house) {
        $scope.isLoggedIn = false;
        $scope.statusmsg = {
            type: 'danger',
            msg: ''
        };
        $scope.info = info;
        $scope.house = house;
        $scope.ends = moment(house.bid_end).fromNow();

        // Fetch house owner
        if(house.owner) {
            $scope.owner = {
                name: 'Loading...'
            };

            $scope.owner = Player.get({id:house.owner}, function(){}, function(response) {
                if (response.status === 404)
                    $scope.player = {
                        name: '[Deleted Player]'
                    }
            });
        }

        if(house.highest_bidder)
            $scope.highest_bidder = Player.get({id: house.highest_bidder}, function(){}, function(response) {
                if (response.status === 404)
                    $scope.highest_bidder = {
                        name: '[Deleted Player]'
                    }
            });

        $scope.bidForm = {
            player: false,
            bid: $scope.house.bid + $scope.info.houses_bid_raise,
            balance: 0,
            canBid : false
        };

        $scope.players = Player.my({}, function(players) {
            $scope.isLoggedIn = true;
            try {
                $scope.bidForm.player = _.find($scope.players, function(p) {
                    return (p.balance >= $scope.bidForm.bid + $scope.house.rent)
                }).name;
            } catch(e) {}
            $scope.checkBalance($scope.bidForm.player);
        });

        $scope.createBid = function() {
            // Find player
            for (var i = 0; i < $scope.players.length; i++) {
                if ($scope.players[i].name == $scope.bidForm.player) {
                    var bid = {
                        id: $scope.house.id,
                        player_id: $scope.players[i].id,
                        bid: $scope.bidForm.bid
                    };
                    var house = House.get({id: bid.id});
                    house.$bid({id: bid.id, player_id: bid.player_id, bid: bid.bid}, function(house) {
                        $scope.statusmsg.type = 'success';
                        $scope.statusmsg.msg = 'You now have the highest pledge on this house!';
                    }, function(response) {
                        $scope.statusmsg.type = 'danger';
                        $scope.statusmsg.msg = response.data.message;
                    });
                }
            }
        };

        $scope.checkBalance = function(playername) {
            // Required balance to bid
            var requiredBalance = $scope.house.bid + $scope.house.rent;
            // Find player
            for (var i = 0; i < $scope.players.length; i++) {
                if ($scope.players[i].name == playername) {
                    $scope.bidForm.balance = $scope.players[i].balance;
                    $scope.bidForm.canBid = (requiredBalance < $scope.players[i].balance);
                    break;
                }
            }
        };
    }
]);

DevAAC.controller('HousesController', ['$scope', 'House', 'Player', 'Server', 'info',
    function($scope, House, Player, Server, info) {
        $scope.info = info;
        $scope.houses = House.query(function(){
            $scope.loaded = true;
        });

        $scope.order = 'size';
        $scope.orderReverse = true;
        $scope.players = [];

        $scope.player = function(id) {
            if(id == 0)
                return;

            if($scope.players[id] == undefined)
                $scope.players[id] = Player.get({id: id});

            return $scope.players[id];
        };

        $scope.fromNow = function(time) {
            return moment(time).fromNow();
        };
    }
]);

// Module Factories(s)
DevAAC.factory('House', ['$resource',
    function($resource){
        return $resource(ApiUrl('houses/:id'), {}, {
            get: { cache: true },
            query: { isArray: true, cache: true },
            bid: { url: ApiUrl('houses/:id/bid'), method: 'POST' }
        });
    }
]);