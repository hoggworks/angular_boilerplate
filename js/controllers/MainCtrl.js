// controller for the main page
app.controller("MainCtrl", function($scope, $route, $location, $modal, $window, $rootScope, SiteService, UserService, Logger, Stats) {
    
    
    $scope.UserService = UserService;
    $scope.SiteService = SiteService;
    var stats = Stats.getInstance("main");
    var logger = Logger.getInstance("main");
    
    if ($location.search().rc) {
        // a recovery code has been provided
        // check with the db to see if it's valid
        $scope.UserService.checkRecoveryCode($location.search().rc)
        .then(function(response) {
            if (response.result == 1) {
                $rootScope.temp_auth = response.authCode;
                $rootScope.openModal("password_reset", '');   
            } else if (response.result == 0 && response.reason == 0) {
                $rootScope.openModal("error", "The code you provided could not be located in our system.");
            } else if (response.result == 0 && response.reason == 1) {
                $rootScope.openModal("error", "The code you provided has expired.");
            } else if (response.result == 0 && response.reason == 2) {
                $rootScope.openModal("error", "This recovery code has already been used.");
            }
            
            console.log(response);
              
        }, function(error) {
            $rootScope.openModal("error", "Either the code you provided wasn't valid, or we were unable to verify it. Please try again.");
        });
    }
    
});