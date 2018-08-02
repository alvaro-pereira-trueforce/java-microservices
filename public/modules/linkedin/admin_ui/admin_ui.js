angular.module('adminUI.linkedin', ['ngRoute', 'mainApp']);

angular.module('adminUI.linkedin').config(function ($routeProvider) {
    $routeProvider.when('/', {
        templateUrl: '../modules/linkedin/admin_ui/admin_ui.html',
        controller: "AdminUICtrl",
        controllerAs: "vm"
    });
});

angular.module('adminUI.linkedin').controller('AdminUICtrl', AdminUICtrl);

function AdminUICtrl(windowsService) {
    var vm = this;
    vm.name = windowsService.zendesk_request.name;
    vm.manifest = windowsService.zendesk_request.manifest;
}