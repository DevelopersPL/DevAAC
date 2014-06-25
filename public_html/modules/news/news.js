// Module Route(s)
DevAAC.config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/', {
		templateUrl: PageUrl('news'),
		controller: 'NewsController'
	});
}]);

// Module Controller(s)
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

// Module Factories(s)
DevAAC.factory('News', ['$resource',
    function($resource){
        return $resource(ApiUrl('news'), {}, {
            get: { cache: true },
            query: { isArray: true, cache: true }
        });
    }
]);