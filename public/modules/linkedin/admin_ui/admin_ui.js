angular.module('adminUI.linkedin', ['ngRoute', 'mainApp']);

angular.module('adminUI.linkedin').config(function ($routeProvider) {
    $routeProvider.when('/', {
        templateUrl: '../modules/linkedin/admin_ui/admin_ui.html',
        controller: "AdminUICtrl",
        controllerAs: "vm"
    });
});

angular.module('adminUI.linkedin').controller('AdminUICtrl', AdminUICtrl);

function AdminUICtrl(windowsService, poller, $timeout, $window) {
    var vm = this;
    vm.processing = false;
    vm.timeout = false;
    vm.linkedin_canceled = false;
    vm.name = windowsService.backend_variables.name;
    vm.metadata = windowsService.backend_variables.metadata;
    vm.client_ID = windowsService.backend_variables.client_ID;
    vm.account_id = windowsService.backend_variables.account_id;
    vm.linkedin_return_URL = windowsService.backend_variables.linkedin_return_URL;
    vm.waitLogin = waitLogin;

    function waitLogin(e) {
        if (vm.form.name.$error.required) {
            e.preventDefault();
            return;
        }
        vm.processing = true;
        vm.timeout = false;
        vm.timeout_counter = 0;
        $timeout(polling, 1000);
    }

    function polling() {
        poller.poll('admin_UI_waiting', {
            account_id: windowsService.backend_variables.account_id,
            name: vm.name
        }).then(function (response) {
            $window.location.href = response.data.redirect_url;
            stopProgress();
        }).catch(function (response) {
            if (response.data.linkedin_canceled) {
                linkedinCanceledReset();
                return;
            }
            vm.timeout_counter++;
            if (vm.timeout_counter < 20) {
                $timeout(polling, 1000);
                return;
            }
            timeoutReset();
        });
    }

    function stopProgress() {
        $timeout.cancel();
        vm.processing = false;
    }

    function timeoutReset() {
        stopProgress();
        vm.timeout = true;
    }

    function linkedinCanceledReset() {
        stopProgress();
        vm.linkedin_canceled = true;
    }
}