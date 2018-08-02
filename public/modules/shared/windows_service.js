angular.module('mainApp', []);

angular.module('mainApp').factory('windowsService', function ($window) {
    var service = {};
    service.zendesk_request = $window.zendesk_request === null || $window.zendesk_request === 'null' ? undefined : $window.zendesk_request;

    //Clean up all the null and set as undefined
    service.zendesk_request = Object.keys(service.zendesk_request).reduce(function (previous, current) {
        previous[current] = service.zendesk_request[current] === 'null' || service.zendesk_request[current] === null ? undefined : service.zendesk_request[current];
        return previous;
    }, {});

    /* Example{
        'name' => NULL,
        'metadata' => NULL,
        'state' => NULL,
        'return_url' => 'https://d3v-assuresoft.zendesk.com/zendesk/channels/integration_service_instances/editor_finalizer',
        'instance_push_id' => '3cd1d2a5-aaf2-41fe-a9f1-519499605854',
        'zendesk_access_token' => '601444c6f97f74d331bdc5fb8843b245f16079511759581582227c5643771588',
        'subdomain' => 'd3v-assuresoft',
        'locale' => 'en-US',
    }*/

    return service;
});