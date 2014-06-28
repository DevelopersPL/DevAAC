// Module Route(s)
DevAAC.config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/houses', {
        templateUrl: PageUrl('houses'),
        controller: 'HousesController'
    });
    $routeProvider.when('/houses/:id', {
        templateUrl: PageUrl('houses/house'),
        controller: 'HouseController'
    });
}]);

// Module Controller(s)
DevAAC.controller('HouseController', ['$scope', 'House', 'Player', '$routeParams', '$location',
    function($scope, House, Player, $routeParams, $location) {
        $scope.isloggedin = false;
        $scope.statusmsg = {
            type: 'danger',
            msg: ''
        }

        $scope.players = Player.my({}, function(players) {
            $scope.isloggedin = true;

        }, function(response) {
            
            if (response.status === 404) {
                $scope.player = {
                    name: '[Deleted Player]'
                }
            }
        });
        House.get({id: $routeParams.id}, function(house) {
            $scope.getHouseData(house);

        }, function (response) {
            if (response.status === 404) {
                $location.path('/houses');
            }
        });
        
        $scope.bidForm = {
            player: false,
            bid: 1000,
            balance: 0,
            canbid : false
        };
        $scope.getHouseData = function(house) {
            $scope.house = house;
            // Fetch house owner
            if (house.owner > 0) {
                $scope.player = {
                    name: 'Loading...'
                }

                Player.get({id:house.owner}, function(player) {
                    $scope.player.name = player.name;

                }, function(response) {
                    if (response.status === 404) {
                        $scope.player = {
                            name: '[Deleted Player]'
                        }
                    }
                });
            }
            // Fetch higher bidder
            if (house.highest_bidder > 0) {
                Player.get({id:house.highest_bidder}, function(player) {
                    $scope.house.bidder_name = player.name;
                });
            }
        }
        $scope.createBid = function() {
            //console.log("Create Bid", $scope.bidForm);
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
                        $scope.getHouseData(house);
                    }, function(response) {
                        $scope.statusmsg.type = 'danger';
                        $scope.statusmsg.msg = response.data.message;
                    });
                }
            }
        }
        $scope.checkbalance = function(playername) {
            // Required balance to bid
            var requiredBalance = $scope.house.bid + $scope.house.rent;
            // Find player
            for (var i = 0; i < $scope.players.length; i++) {
                if ($scope.players[i].name == playername) {

                    $scope.bidForm.balance = $scope.players[i].balance;
                    if (requiredBalance < $scope.players[i].balance) {
                        $scope.bidForm.canbid = true;
                        $scope.bidForm.bid = $scope.house.bid + 100;
                    } else $scope.bidForm.canbid = false;
                    //console.log($scope.bidForm);
                    break;
                }
            }
        }
    }
]);

DevAAC.controller('HousesController', ['$scope', 'House', 'Player',
    function($scope, House, Player) {
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
        }
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