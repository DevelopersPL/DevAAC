// WindowSession
DevAAC.factory("WindowSession", function($http, Account, $window) {
	return {
		checkToken: function() {
			var token = Account.getToken();
			if (token !== false) return true;
			else return token;
		},
		registerToken: function(token) {
			$window.sessionStorage.token = token;
			Cookie.set('DevAACToken', token, 1);
		},
		removeToken: function() {
			delete $window.sessionStorage.token;
			Cookie.set('DevAACToken', '', 0);
			return Account.removeTokenData();
		}
	}
});

// Status message
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

// Account API
DevAAC.factory("Account", function($http, $location) {
	var accToken = false;
	var accData = false;
	var accPlayers = false;
	var isAuthenticating = false;
	return {
		register: function(name, password, email) {
			console.log("Name: "+name+". Email: "+email+". Password length: "+password.length);
			isAuthenticating = true;
			return $http({
				url: ApiUrl('accounts'),
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				data: JSON.stringify({'name': name, 'password': password, 'email': email})
			})
			.success(function (data, status) {
				accData = data;
				accToken = btoa(name + ":" + password);
				isAuthenticating = false;
				console.log(data, status);
			})
			.error(function (data, status) {
				console.log(data, status);
				isAuthenticating = false;
			});
		},
		authenticate: function(token) {
			if (!accData) isAuthenticating = true;
			return $http({
				url: ApiUrl('accounts/my'),
				method: 'GET',
				headers: { 'Authorization': "Basic " + token }
			})
			.success(function (data, status) {
				accToken = token;
				accData = data;
				isAuthenticating = false;
				console.log(data, status);
			})
			.error(function (data, status) {
				isAuthenticating = false;
				console.log(data, status);
			});
		},
		createPlayer: function(name, vocation, sex) {
			console.log("Name: "+name+". vocation: "+vocation+". gender: "+sex);
			return $http({
				url: ApiUrl('players'),
				method: 'POST',
				headers: { 'Content-Type': 'application/json', 'Authorization': "Basic " + accToken },
				data: JSON.stringify({'name': name, 'vocation': vocation, 'sex': sex})
			})
			.success(function (data, status) {
				console.log(data, status);
			})
			.error(function (data, status) {
				console.log(data, status);
			});
		},
		getToken: function() {
			return accToken;
		},
		getAccount: function() {
			return accData;
		},
		getAccountPlayers: function() {
			if (accPlayers != false) return accPlayers;
			else return $http({
				url: ApiUrl('accounts/my/players'),
				method: 'GET',
				headers: { 'Authorization': "Basic " + accToken }
			})
			.success(function (data, status) {
				console.log(data, status);
			})
			.error(function (data, status) {
				console.log(data, status);
			});
		},
		removeTokenData: function() {
			accToken = false;
		},
		removeAccountData: function() {
			accData = false;
		},
		isAuthenticating: function() {
			return isAuthenticating;
		}
	}
});

DevAAC.factory('News', ['$resource',
    function($resource){
        return $resource(ApiUrl('news'), {}, {
            get: { cache: true },
            query: { isArray:true, cache: true }
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
            highExperience: { params: {sort: '-experience', limit: 5}, isArray: true, cache: true }
        });
    }
]);

DevAAC.factory('Guild', ['$resource',
    function($resource){
        return $resource(ApiUrl('guilds/:guildId'), {}, {
            get: { cache: true },
            query: { isArray:true, cache: true }
        });
    }
]);

DevAAC.factory('House', ['$resource',
    function($resource){
        return $resource(ApiUrl('houses/:guildId'), {}, {
            get: { cache: true },
            query: { isArray:true, cache: true }
        });
    }
]);
