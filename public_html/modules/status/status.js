// Module Factories(s)
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