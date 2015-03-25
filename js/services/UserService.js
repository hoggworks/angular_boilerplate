app.factory('UserService', function($rootScope, $http, $cookieStore, SiteService) {
    // instantiate user object
   // var stats = $rootScope.stats;
    //var logger = $rootScope.logger;
    var user = {};
    var checked = false;
//console.log($rootScope.logger);
    //logger.log('test');
    
    // check to see if an authCode is saved on the server
    if ($cookieStore.get('user')) {
        // check for validity of the code
        $http({method:"POST",
               url:SiteService.dataURL("user/check_auth_code"),
               data:{authCode:$cookieStore.get('user').authCode},
               headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
        .then(function(result) {
            if (result.data.result == 1) {
                user = result.data.user;
                user.authCode = user.code;
                delete(user.code);
                $cookieStore.put('user', result.data.user);
            } else {
                $cookieStore.put('user', null);
            }
            checked = true;
        }, function(error) {
            // error
           // logger.log('');
            $cookieStore.put('user', null);
            checked = true;
            $rootScope.openModal("error", "Unable to Verify your login status. You have now been logged out of the system.");
        });
    } else {
        checked = true;
    }
    
    var register = function(new_user, sc) 
    {
          var promise = $http({method:"POST", 
                 url:SiteService.dataURL("user/register"), 
                 data:new_user,
                 headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
          .then(function(result) {
              // successfully called server
                 if (result.data.result == 1) {
                    // user has logged in; set cookie values
                     user = result.data.user;
                     
                     $cookieStore.put('user', user);
                     
                     // redirect to user 
                    return result.data; 
                } else {
                    // user not logged in
                    //alert("Registration was unsuccessful for the following reasons: " + data.reason);
                    
                    return false;
                }
          }, function(error) {
            // error calling server
              return false;
          
          });
        
        return promise;
    }
    
    var login = function(u,p)
    {
        var data = {};
        data.u = u;
        data.p = p;   

        var promise = $http({method:"POST", 
                 url:SiteService.dataURL("user/login"), 
                 data:data,
                 headers: {'Content-Type': 'application/x-www-form-urlencoded'}}).
  success(function(data, status, headers, config) {
      console.log(data.result);
                 if (data.result == 1) {
                    // user has logged in
                     console.log('haa');
                     console.log(data.user);
                     user = data.user;
                     
                     $cookieStore.put('user', data.user);
                } else {
                    // user not logged in
                    //alert("Either your username or password were invalid. Please try again.");
                   // return 0;
                }
        })
          .error(function(data, status, headers, config) {
            //alert("Unable to login.");
            return 0;
        }).then(function (response) {
            return response.data;
        }, function(error) {
           // alert("Either your username or password were invalid. Please try again.");
            return 0;
      });
      // Return the promise to the controller
      return promise;
    }
    
    var logout = function() 
    {
        var data = {'authCode':getAuthCode()};
        var promise = $http({method:"POST", 
                             url:SiteService.dataURL("user/logout"),
                             data:data,
                             headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
        .then(function (response) {
            console.log(response);
            if (response.data.result == 1) {
                user = {};
                $cookieStore.put('user', null);  
            } 
            return response.data;
       }, function(error) {
            return false;
        }); 
        
        return promise;
    }
    
    var clearUser = function() {
        console.log("flushing user");
        user = {};
    }
    
    var recoverPassword = function(e) 
    {
        var data = {'email':e};
        var promise = $http({method:"POST", 
                             url:SiteService.dataURL("user/recover_password"),
                             data:data,
                             headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
        .then(function (response) {
            console.log('heard back');
            console.log(response);
            return response.data;
       }, function(error) {
            return false;
        }); 
        
        return promise;
    }
    
    var getUser = function() { 
        if (getAuthCode()) {
            return user;
        } else {
            return false;
        }
            
    }
    
    var update = function(data, sc)
    {
        // update user information
        var promise = $http({method:"POST", 
                 url:SiteService.buildUrl("user/update"), 
                 data:{'user':user},
                 headers: {'Content-Type': 'application/x-www-form-urlencoded'}}).
  success(function(data, status, headers, config) {
                if (data.result == 1) {
                    // hide edit screen
                   // alert("Info successfully saved.");
                     
                    sc.hideEdit();
                     
                    for (var prop in data) {
                        user[prop] = data[prop];
                    }
                    $cookieStore.put('user', user);
                     
                     // clear the login object
                    return 1; 
                } else {
                    // user not logged in
                   // alert("There was a problem: " + data.reason + " " + data.query);
                    return 0;
                }
        })
        .error(function(data, status, headers, config) {
            //alert("Unable to update your info do to a server issue. Please try again." + data.query);
            return 0;
        });
        
        return promise;
    }
    
    var logged = function() { return getUser().authCode ? true : false; }
    var admin = function(){ return getUser().permissions; }
    var getAuthCode = function() { return user.authCode ? user.authCode : false; }
    var checked = function() { return checked; }
    
    
    var testInjector = function() {
        var data = {};
     var promise = $http({method:"POST", 
                             url:SiteService.dataURL("user/test"),
                             data:data,
                             headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
        .then(function (response) {
            console.log(response);
            return response.data;
       }, function(error) {
            return false;
        }); 
        
        return promise;   
    }
    
    var checkRecoveryCode = function(c) {
           var data = {'code':c};
        
          var promise = $http({method:"POST", 
                             url:SiteService.dataURL("user/check_recovery_code"),
                             data:data,
                             headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
            .then(function (response) {
                return response.data;
            }, function(error) {
                return false;
            }); 
        
        return promise;   
    }
    
    var resetPassword = function(p, c, rc ,sc) {
         var data = {'authCode':c, 'password':p, 'recovery_code':rc};
        
          var promise = $http({method:"POST", 
                             url:SiteService.dataURL("user/reset_password"),
                             data:data,
                             headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
            .then(function (response) {
                return response.data;
            }, function(error) {
                return false;
            }); 
        
        return promise;   
    }
    
    return {
        logged:logged,
        admin:admin,
        checked:checked,
        getUser:getUser,
        login:login,
        logout:logout,
        register:register,
        update:update,
        authCode:getAuthCode,
        recoverPassword:recoverPassword,
        testInjector:testInjector,
        clearUser:clearUser,
        checkRecoveryCode:checkRecoveryCode,
        resetPassword:resetPassword

        
    }
});


