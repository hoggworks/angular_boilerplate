// Please note that $modalInstance represents a modal window (instance) dependency.
// It is not the same as the $modal service used above.

var ModalInstanceCtrl = function ($scope, $modalInstance, items, $rootScope, $location, UserService, Logger, Stats) {
    var stats = Stats.getInstance("modal");
    var logger = Logger.getInstance("modal");
    
    $scope.UserService = UserService;
    $scope.items = items;
    $scope.login = {};
    $scope.login.user = "";
    $scope.login.password = "";
    $scope.login.error_msg = "";
    $scope.login.error = false;
    
    $scope.newUser = {};
    $scope.newUser.email = '';
    $scope.newUser.login = '';
    $scope.newUser.password = '';
    $scope.newUser.name = '';
    
    $scope.recover = {};
    $scope.recover.email = '';
    $scope.recover.status = '';
    
    $scope.reset = {};
    $scope.reset.password = '';
    $scope.reset.confirm = '';
    

    $scope.ok = function () {
        $modalInstance.close($scope.selected.item);
    };
    
    $scope.errorOk = function() {
      $modalInstance.dismiss('cancel');  
    }
    
    $scope.alertOk = function() {
        $modalInstance.dismiss('cancel');
    }

    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
    
    $scope.submitLogin = function() {
        var loginStat = 0;
        UserService.login($scope.login.user, $scope.login.password).then(function(d) {
            if (d.user && d.user.authCode > '') {
                $scope.login.user = "";
                $scope.login.password = "";
                $scope.login.error_msg = "";
                $modalInstance.dismiss('cancel');
                $scope.login.error = false;
            } else {
                $scope.login.error_msg = "Either your password or username was incorrect. Please try jagain.";
                $scope.login.error = true;
            }
        });
    }
    
    $scope.submitRegister = function() {
        UserService.register($scope.newUser, $scope).then(function(d) {
            //console.log("Callback " + d);
            if (d.result == 1) {
                $scope.newUser = {};
                $scope.newUser.email = '';
                $scope.newUser.login = '';
                $scope.newUser.password = '';
                $scope.newUser.name = '';
                $scope.login.error = false;
                $rootScope.openModal("alert", "You have been successfully registered. Thanks!");
                $modalInstance.dismiss('cancel');
            } else {
                $scope.newUser.error_msg = "There was an error registering. Please try again.";
                $scope.newUser.error = true;
            }
            
        });
    }
    
     $scope.submitRecover = function() {
        UserService.recoverPassword($scope.recover.email, $scope).then(function(d) {
            if (d.result == 1) {
                $scope.recover.error = false;
               // $scope.recover.email = '';
                $scope.recover.done = true;
                $scope.recover.status = 'An email has been sent to the specified address with information on how to reset your password.';
            } else {
                $scope.recover.status = d.reason;
            }
        });
    }
     
     $scope.submitReset = function() {
        if ($scope.reset.password.length < 6) {
            $scope.reset.status = 'Your new password must be at least 6 characters long.';
            return;
        } 
         
        if ($scope.reset.password != $scope.reset.confirm) {
            $scope.reset.status = 'New Password and Confirm don\'t match.';
            return;
        }
         
        UserService.resetPassword($scope.reset.password, $rootScope.temp_auth, $location.search().rc, $scope).then(function(d) {
            if (d.result == 1) {
                $rootScope.temp_auth = '';
               // $scope.recover.email = '';
                $scope.reset.done = true;
                delete $location.$$search.rc;
                $location.$$compose();
                $scope.reset.status = 'Your password has been reset. Please click the Login button below to log in';
            } else {
                $scope.recover.status = d.reason;
            }
        });
    }
     
     $scope.showLoginFromReset = function() {
         $rootScope.openModal("login", "");
         $modalInstance.dismiss('cancel');
     }
    
    $scope.cancelLogin = function() {
        console.log("cancel login");
        $modalInstance.dismiss('cancel');
    }
    
    $scope.showRegister = function() {
        console.log("show register");
        $rootScope.openModal("register", "");
        $modalInstance.dismiss('cancel');
    }
    
    $scope.showRecoverPassword = function() {
        console.log("show recover password");
        $rootScope.openModal("recover_password", "");
        $modalInstance.dismiss('cancel');
    }
    
    $scope.cancelRegister =function() {
        console.log("cancel register");
        $modalInstance.dismiss('cancel');
    }
}
//);