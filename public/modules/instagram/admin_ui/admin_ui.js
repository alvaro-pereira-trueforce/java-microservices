angular.module('adminUI.instagram', ['ngRoute', 'mainApp']);

angular.module('adminUI.instagram').config(function ($routeProvider) {
    $routeProvider
        .when('/', {
            templateUrl: '../modules/instagram/admin_ui/admin_ui.html',
            controller: "AdminUICtrl",
            controllerAs: "vm"
        })
});

angular.module('adminUI.instagram').controller('AdminUICtrl', AdminUICtrl);

function AdminUICtrl(windowsService, poller, $timeout, basicService, $window) {
    var vm = this;
    vm.processing = false;
    vm.timeout = false;
    vm.facebook_canceled = false;
    vm.name = windowsService.backend_variables.name;
    vm.metadata = windowsService.backend_variables.metadata;
    vm.client_ID = windowsService.backend_variables.client_ID;
    vm.account_id = windowsService.backend_variables.account_id;
    vm.return_URL = windowsService.backend_variables.return_URL;
    vm.waitLogin = waitLogin;
    vm.saveIntegration = saveIntegration;

    var save_URL;
    vm.pages = undefined;
    vm.selected_page = undefined;

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
            save_URL = response.data.redirect_url;
            vm.pages = response.data.pages;
            stopProgress();
        }).catch(function (response) {
            if (response.data.facebook_canceled) {
                CanceledReset();
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

    function saveIntegration() {
        vm.error = undefined;
        basicService.postRequest('admin_ui_validate_page', {
            page_information: vm.selected_page,
            account_id: windowsService.backend_variables.account_id
        }).then(function (response) {
            $window.location.href = response.data.redirect_url;
        }).catch(function (error) {
            vm.error = error.data.message;
        })
    }

    function stopProgress() {
        $timeout.cancel();
        vm.processing = false;
    }

    function timeoutReset() {
        stopProgress();
        vm.timeout = true;
    }

    function CanceledReset() {
        stopProgress();
        vm.facebook_canceled = true;
    }
}