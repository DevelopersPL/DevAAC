function PageUrl(page) {
	return "pages/" + page + ".html";
}
function ApiUrl(link) {
    // window.location.origin is better (includes port) but is not supported in IE
	return window.location.protocol + '//' + window.location.host + '/api/v1/' + link;
}

function base64_encode (data) {
    // From: http://phpjs.org/functions
    // +   original by: Tyler Akins (http://rumkin.com)
    // +   improved by: Bayron Guevara
    // +   improved by: Thunder.m
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Pellentesque Malesuada
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Rafał Kukawski (http://kukawski.pl)
    // *     example 1: base64_encode('Kevin van Zonneveld');
    // *     returns 1: 'S2V2aW4gdmFuIFpvbm5ldmVsZA=='
    // mozilla has this native
    // - but breaks in 2.0.0.12!
    //if (typeof this.window['btoa'] === 'function') {
    //    return btoa(data);
    //}
    var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
        ac = 0,
        enc = "",
        tmp_arr = [];

    if (!data) {
        return data;
    }

    do { // pack three octets into four hexets
        o1 = data.charCodeAt(i++);
        o2 = data.charCodeAt(i++);
        o3 = data.charCodeAt(i++);

        bits = o1 << 16 | o2 << 8 | o3;

        h1 = bits >> 18 & 0x3f;
        h2 = bits >> 12 & 0x3f;
        h3 = bits >> 6 & 0x3f;
        h4 = bits & 0x3f;

        // use hexets to index into b64, and append result to encoded string
        tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
    } while (i < data.length);

    enc = tmp_arr.join('');

    var r = data.length % 3;

    return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);

}

// Simple singleton to set and get cookies.
// (For persistent login/future "remember me" functionality)
var Cookie = {
    set: function (cname,cvalue,exdays) {
        var d = new Date();
        d.setTime(d.getTime()+(exdays*24*60*60*1000));
        var expires = "expires="+d.toGMTString();
        document.cookie = cname + "=" + cvalue + "; " + expires;
    },
    get: function (cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        var value = false;
        for(var i=0; i<ca.length; i++) {
            var c = ca[i].trim();
            if (c.indexOf(name)==0) value = c.substring(name.length,c.length);
        }
        if (value === false || value.length < 1) return false;
        else return value;
    }
};

// Initiate DevAAC
var DevAAC = angular.module('app', ['ngRoute', 'ngResource']);

// Add authentication headers to all xhr requests after login.
DevAAC.run(function($http, Account) {
    if (Account.getToken() !== false)
        $http.defaults.headers.common.Authentication = 'Basic '+Account.getToken();
});

// Add auth header to all $http request (example above)
DevAAC.factory('authInterceptor', function ($rootScope, $q, $window) {
    return {
        request: function (config) {
            config.headers = config.headers || {};
            if ($window.sessionStorage.token) {
                config.headers.Authorization = 'Basic ' + $window.sessionStorage.token;
            }
            return config;
        },
        response: function (response) {
            if (response.status === 401) {
                console.log('User is not logged in');
                // TODO: Add $rootscope.isAuthenticated globally.
            }
            return response || $q.when(response);
        }
    };
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

DevAAC.filter('markdown', function($sce) {
    var converter = new Showdown.converter();
    return function(input) {
        if(input)
            return $sce.trustAsHtml(converter.makeHtml(input))
    };
});
