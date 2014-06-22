DevAAC.factory("StatusMessage", function() {
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

DevAAC.factory('Account', ['$http', '$resource', '$cacheFactory',
    function($http, $resource, $cacheFactory) {
        var token;

        return {
            generateToken: function(username, password) {
                var p = new jsSHA(password, 'TEXT');
                return btoa(username + ':' + p.getHash('SHA-1', 'HEX'));
            },
            register: function(account) {
                return this.factory.save(account, function (data, status) {
                    Cookie.set('DevAACToken', this.generateToken(account.name, account.password), 7);
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
                update: { method: 'PUT' }
            })
        }
    }
]);

DevAAC.factory('News', ['$resource',
    function($resource){
        return $resource(ApiUrl('news'), {}, {
            get: { cache: true },
            query: { isArray: true, cache: true }
        });
    }
]);

DevAAC.factory('Server', ['$resource',
    function($resource){
        return $resource(ApiUrl('server/:what'), {}, {
            config: { params: {what: 'config'}, isArray: true, cache: true },
            info: { params: {what: 'info'}, cache: true },
            vocations: { params: {what: 'vocations'}, isArray: true, cache: true }
        });
    }
]);

DevAAC.factory('Player', ['$resource',
    function($resource) {
        return $resource(ApiUrl('players/:id'), {}, {
            get: { cache: true },
            queryOnline: { params: {id: 'online', embed: 'player'}, isArray: true, cache: true },
            highExperience: { params: {sort: '-experience', limit: 5}, isArray: true, cache: true },
            my: { url: ApiUrl('accounts/my/players'), isArray: true, cache: true }
        });
    }
]);

DevAAC.factory('Guild', ['$resource',
    function($resource){
        return $resource(ApiUrl('guilds/:guildId'), {}, {
            get: { cache: true },
            query: { isArray: true, cache: true }
        });
    }
]);

DevAAC.factory('House', ['$resource',
    function($resource){
        return $resource(ApiUrl('houses/:guildId'), {}, {
            get: { cache: true },
            query: { isArray: true, cache: true }
        });
    }
]);
