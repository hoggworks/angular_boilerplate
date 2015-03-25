app.controller("UserCtrl", function($scope, $window,$location, $window, Logger, Stats, UserService) {
    var stats = Stats.getInstance("user");
    var logger = Logger.getInstance("user");
    $scope.UserService = UserService;
    
    $scope.inter = setInterval(function() { 
        if ($scope.UserService.logged()) {
            clearInterval($scope.inter); 
            $scope.init(); 
        } else {
            
        }
    }, 50);
    
    $scope.init = function() {
        // init functions for this controller   
    }
  
});