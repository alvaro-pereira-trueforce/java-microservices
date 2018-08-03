angular.module('mainApp', []);

angular.module('mainApp').factory('windowsService', function ($window) {
    var service = {};
    service.backend_variables = $window.backend_variables === null || $window.backend_variables === 'null' ? undefined : $window.backend_variables;

    //Clean up all the null and set as undefined
    service.backend_variables = Object.keys(service.backend_variables).reduce(function (previous, current) {
        previous[current] = service.backend_variables[current] === 'null' || service.backend_variables[current] === null ? undefined : service.backend_variables[current];
        return previous;
    }, {});

    /* Example{
                    'account_id' => null,
                    'linkedin_return_URL' => env('APP_URL') . '/linkedin/admin_ui',
                    'client_ID' => env('LINKEDIN_CLIENT_ID'),
                    'name' => null,
                    'metadata' => null
    }*/

    return service;
});


angular.module('mainApp').factory('poller', function ($http) {
    return {
        poll: function (api, data) {
            return $http.post(api, data);
        }

    }
});