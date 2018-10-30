angular.module('adminUI.telegram', ['ngRoute', 'mainApp']);

angular.module('adminUI.telegram').config(function ($routeProvider) {
    $routeProvider
        .when('/', {
            templateUrl: '../modules/telegram/admin_ui/admin_ui.html',
            controller: "AdminUICtrl",
            controllerAs: "vm"
        })
});

angular.module('adminUI.telegram').controller('AdminUICtrl', AdminUICtrl);

function AdminUICtrl(windowsService, poller, $timeout, basicService, $window) {
    var vm = this;
    vm.processing = false;
    vm.timeout = false;
    vm.name = windowsService.backend_variables.name;
    vm.token = windowsService.backend_variables.token;
    vm.metadata = windowsService.backend_variables.metadata;
    vm.return_URL = windowsService.backend_variables.return_URL;
    vm.settings = {};
    vm.settings = windowsService.backend_variables.settings;

    vm.ticket_types = windowsService.backend_variables.ticket_types;
    vm.ticket_priorities = windowsService.backend_variables.ticket_priorities;
    vm.pull_mode = windowsService.backend_variables.pull_mode;
    vm.locales = windowsService.backend_variables.locales;

    vm.saveIntegration = saveIntegration;

    function saveIntegration() {
        vm.error = undefined;
        if (vm.form.$valid) {
            basicService.postRequest('admin_ui_validate_data', {
                account_id: windowsService.backend_variables.account_id,
                name: vm.name,
                token: vm.token,
                settings: vm.settings
            }).then(function (response) {
                console.log(response);
                if (!!response.data.save_url) {
                    $window.location.href = response.data.save_url;
                }
            }).catch(function (error) {
                console.log(error);
                vm.error = error.data.message;
                $window.scrollTo(0, 0);
            })
        }
        else {
            $window.scrollTo(0, 0);
        }
    }
}