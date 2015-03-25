app.controller("MenuCtrl", function($scope, $rootScope, $window, $location, Logger, UserService, SiteService, Stats) {
    var stats = Stats.getInstance("menu");
    var logger = Logger.getInstance("menu");
    $scope.UserService = UserService;
    $scope.SiteService = SiteService;
    
    $scope.goTo = function (w) {
        if (w) {
            stats.add('clicked_home');
        }
        $location.path(w);
    }
    
    $scope.goToYourPage = function() {
        $location.path("/you");
    }
    
    $scope.processLogout = function() {
      $scope.UserService.logout()
        .then(function(response) {
          //  console.log('Did I logout? ' + response.result);
            if (response.result != 1) {
                $rootScope.openModal("error", "Unable to log out. Please try again.");
            }
        }, function(error) {
            $rootScope.openModal("error", "Unable to log out. Please try again.");
        });   
    }
    
    $scope.goToAdmin = function() {
        $location.path("/admin");
    }
    
    $scope.showLogin = function() {
        $rootScope.openModal("login", "");
    }
  
});