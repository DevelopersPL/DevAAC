// Module Factories(s)
DevAAC.factory('Server', ['$resource',
    function($resource){
        return $resource(ApiUrl('server/:what'), {}, {
            config: { params: {what: 'config'}, isArray: true, cache: true },
            info: { params: {what: 'info'}, cache: true },
            vocations: { params: {what: 'vocations'}, isArray: true, cache: true }
        });
    }
]);
