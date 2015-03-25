app.controller("AppCtrl", function($scope, $route, $location, $modal, $window, $rootScope, SiteService, UserService, Logger, Stats) {
    var stats = Stats.getInstance("app");
    var logger = Logger.getInstance("app");
    
    $rootScope.stats = Stats.getInstance("services");
    $rootScope.logger = Logger.getInstance("services");
    
    
    console.log("ha");
    
    $scope.SiteService = SiteService;
    $rootScope.UserService = UserService;
    
    $rootScope.openModal = function (modalType, msg) {
       var modalInstance = $modal.open({
            templateUrl: 'html/modals/'+modalType+'.html',
            controller: ModalInstanceCtrl,
            resolve: {
                items: function () {
                    return {msg:msg};
                }
            }
        });
       
        modalInstance.result.then(function (selectedItem) {
            $scope.selected = selectedItem;
        }, function () {
    
        }); 
    } 
});