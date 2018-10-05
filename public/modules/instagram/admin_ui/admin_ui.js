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
    vm.tags = windowsService.backend_variables.tags;
    vm.selected_ticket_type = windowsService.backend_variables.ticket_type;
    vm.selected_ticket_priority = windowsService.backend_variables.ticket_priority;

    vm.ticket_types = [];
    vm.ticket_priorities = [];

    vm.waitLogin = waitLogin;
    vm.saveIntegration = saveIntegration;

    var save_URL;
    vm.pages = undefined;
    vm.selected_page = undefined;
    vm.expires = undefined;

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
            vm.expires = Math.round(response.data.expires / 60 / 60 / 24);
            vm.ticket_types = response.data.ticket_types;
            vm.ticket_priorities = response.data.ticket_priorities;
            vm.selected_ticket_type = response.data.ticket_type;
            vm.selected_ticket_priority = response.data.ticket_priority;
            vm.tags = response.data.tags;
            vm.pages.forEach(function (value) {
                if (value.id === response.data.selected_page) {
                    vm.selected_page = value;
                    vm.isASavedIntegration = true;
                }
            });
            stopProgress();
            $window.scrollTo(0, 0);
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

    vm.receiveOldPost = false;

    function saveIntegration() {
        vm.error = undefined;
        if (vm.form.$valid) {
            if (vm.receiveOldPost === false) {
                vm.old_post_number = undefined;
            }
            basicService.postRequest('admin_ui_validate_page', {
                page_information: vm.selected_page,
                account_id: windowsService.backend_variables.account_id,
                tags: vm.tags,
                ticket_type: vm.selected_ticket_type,
                ticket_priority: vm.selected_ticket_priority,
                old_post_number: vm.old_post_number
            }).then(function (response) {
                $window.location.href = response.data.redirect_url;
            }).catch(function (error) {
                vm.error = error.data.message;
                $window.scrollTo(0, 0);
            })
        }
        else {
            $window.scrollTo(0, 0);
        }
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
        $window.scrollTo(0, 0);
    }
}