function PageUrl(module) {
    module = module.split('/');
    if (module.length == 1) return '/modules/' + module[0] + '/' + module[0] + '.html';
    else return '/modules/' + module[0] + '/' + module[1] + '.html';
}
function ApiUrl(link) {
    // window.location.host typically includes port
    return window.location.protocol + '//' + window.location.host + '/api/v1/' + link;
}

var Cookie = {
    set: function (cname, cvalue, exdays) {
        var d = new Date();
        d.setTime( d.getTime() + (exdays*24*60*60*1000) );
        var expires = 'expires=' + d.toUTCString();
        document.cookie = cname + '=' + cvalue + '; ' + expires;
    },
    get: function (cname) {
        var name = cname + '=';
        var ca = document.cookie.split(';');
        var value = false;
        for(var i=0; i<ca.length; i++) {
            var c = ca[i].trim();
            if (c.indexOf(name)==0) value = c.substring(name.length,c.length);
        }
        if (value === false || value === null || value.length < 1) return false;
        else return value;
    }
};

// Initiate DevAAC
var DevAAC = angular.module('DevAAC', ['ngRoute', 'ngResource', 'ngSanitize', 'ui.bootstrap']);

DevAAC.config(['$locationProvider', function ($locationProvider) {
    $locationProvider.html5Mode({
        enabled: true,
        rewriteLinks: true
    });
}]);

DevAAC.run(['$rootScope', '$location', 'StatusMessage', function($rootScope, $location, StatusMessage) {
    $rootScope.$on('$routeChangeStart', function(e, curr, prev) {
        if (curr.$$route && curr.$$route.resolve) {
            $rootScope.loadingView = true;
        }
    });
    $rootScope.$on('$routeChangeSuccess', function(e, curr, prev) {
        $rootScope.loadingView = false;
    });
    $rootScope.$on('$routeChangeError', function(e, curr, prev, rejection) {
        $rootScope.loadingView = false;
        console.log(e, rejection);
        StatusMessage.setError(rejection.data.message);
        $location.path('/');
    });
}]);

DevAAC.factory('authInterceptor', function() {
    return {
        request: function (config) {
            config.headers = config.headers || {};
            if(Cookie.get('DevAACToken'))
                config.headers.Authorization = 'Basic ' + Cookie.get('DevAACToken');
            return config;
        },
        response: function (response) {
            if (response.status === 401) {
                Cookie.set('DevAACToken', '', 1);
            }
            return response || $q.when(response);
        }
    };
});

DevAAC.factory('StatusMessage', function() {
    var _status = {
        success: '',
        error: '',
        notice: ''
    };
    return {
        success: function() {
            var message = _status.success;
            _status.success = '';
            return message;
        },
        setSuccess: function(msg) {
            _status.success = msg;
        },
        error: function() {
            var message = _status.error;
            _status.error = '';
            return message;
        },
        setError: function(msg) {
            _status.error = msg;
        },
        notice: function() {
            var message = _status.notice;
            _status.notice = '';
            return message;
        },
        setNotice: function(msg) {
            _status.notice = msg;
        }
    }
});

DevAAC.config(function ($httpProvider) {
    $httpProvider.interceptors.push('authInterceptor');
});

DevAAC.directive('markdown', function ($compile, $http) {
    var converter = new Showdown.converter();
    return {
        restrict: 'E',
        replace: true,
        link: function (scope, element, attrs) {
            if ("src" in attrs) {
                $http.get(attrs.src).then(function(data) {
                    element.html(converter.makeHtml(data.data));
                });
            } else {
                element.html(converter.makeHtml(element.text()));
            }
        }
    };
});

// To use: <button ng-click="action()" ng-confirm-click="Are you sure?"/>
DevAAC.directive('ngConfirmClick', [function () {
    return {
        priority: -1,
        restrict: 'A',
        link: function (scope, element, attrs) {
            element.bind('click', function (e) {
                var message = attrs.ngConfirmClick;
                if (message && !confirm(message)) {
                    e.stopImmediatePropagation();
                    e.preventDefault();
                }
            });
        }
    }
}]);

DevAAC.filter('markdown', function($sce) {
    var converter = new Showdown.converter();
    return function(input) {
        if(input)
            return $sce.trustAsHtml(converter.makeHtml(input))
    };
});

DevAAC.filter('capitalize', function () {
    "use strict";
    return function (input) {
        return input.charAt(0).toUpperCase() + input.slice(1);
    };
});

DevAAC.filter('moment', function () {
    return function(value, format) {
        return moment(value).format(format);
    };
});

DevAAC.filter('reverse', function() {
    return function(items) {
        return items.slice().reverse();
    };
});

DevAAC.filter('htmlstring', function($sce) {
    return function(input) {
        if(input)
            return $sce.trustAsHtml(input.replace(/\\n/g, "<br />"))
    };
});
