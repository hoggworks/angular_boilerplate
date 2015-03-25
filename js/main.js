// cfg object - this is where site-wide variables are stored
cfg = {};

cfg.check_server = false;
cfg.dataDirectory = "data/";
cfg.logging = true;
cfg.stats = true;
cfg.saveLogToServer = true;
cfg.saveLogInfoToServer = false;
cfg.saveStatsToServer = true;
cfg.loginRequired = ['user'];
cfg.adminRequired = ['admin'];
cfg.adminPermissions = 1;

// initial app declaration
var app = angular.module("app", ["ngRoute", "ngCookies", "ngSanitize", "ui.bootstrap", "logger.module", "stats.module"]);

// routing stub
app.config(function($routeProvider) {
    $routeProvider
    .when('/',
    {
        templateUrl: "html/home.html",
        controller: "MainCtrl"
    })
    .when('/admin',
    {
        templateUrl: "html/admin.html",
        controller: "AdminCtrl"
    })
    .when('/user',
    {
        templateUrl: "html/user.html",
        controller:"UserCtrl"
    })
    .otherwise(
    {
        templateUrl:"html/error.html",
        controller: "ErrorCtrl"
    })
});

app.factory('httpInterceptor', function ($q, $rootScope) {
    return {
        request: function (config) {
            if (!config.data) {
                config.data = {};
            }
           // console.log($rootScope.UserService.logged());
            if ($rootScope.UserService.logged() && !config.data.authCode) {
                config.data.authCode = $rootScope.UserService.authCode();
            }
            //logIt('- Modify request');
            return config || $q.when(config);
        },
        
        response: function (config) {
            
            if (config.data.status == "401") {
               // console.log("Unauthorized");
                $rootScope.UserService.clearUser();
                $rootScope.openModal("login", "Your login has expired. Please log back into the site.");
            }
            
            return config || $q.when(config);
        }
    };
});

app.config(function ($httpProvider) {
    $httpProvider.interceptors.push('httpInterceptor');
}); 

app.config(['LoggerProvider', function(LoggerProvider) {
	   			LoggerProvider.enabled(cfg.logging);
	   		}])

app.config(['StatsProvider', function(StatsProvider) {
	   			StatsProvider.enabled(cfg.stats);
	   		}])

var checkedInterval;

// conditionally redirect routes based on login status
app.run( function($rootScope, $location, UserService) {
    // register listener to watch route changes
    $rootScope.$on( "$routeChangeStart", function(event, next, current) {
        return false;
        checkedInterval = setInterval(function() {
            if (UserService.checked()) {
                checkRoute(next, UserService);        
                clearInterval(checkedInterval);
            }
        }, 50);
    });
 })

function checkRoute(next, UserService)
{
  if ((!UserService.logged() && needLogin(next.templateUrl))) {
            // user doesn't have permissions; trap route change and redirect
          
            if (stripRoute(next.templateUrl) != "login") {
                next.templateUrl = "html/no_auth_user.html";
                next.controller = "NoUserAuthCtrl";
            } else {
                // going to login
                // although, as currently written (as of Feb 12), login only occurs via modal
                // so nobody attempt a direct login
            }
        } else {
            if (UserService.admin() != cfg.adminPermissions && needAdmin(next.templateUrl)) {
                if (stripRoute(next.templateUrl) != "login") {
                    next.templateUrl = "html/no_auth_admin.html";
                    next.controller = "NoAdminAuthCtrl";
                } else {
                    // going to login
                    // although, as currently written (as of Feb 12), login only occurs via modal
                    // so nobody attempt a direct login
                }   
            }
        }    
}

function needLogin(c) {
    var stripped = stripRoute(c);
                            
    for (var i = 0; i<cfg.loginRequired.length; i++) {
        if (cfg.loginRequired[i] == stripped) {
            
            return true;
        }
    }
}


function needAdmin(c) {
    var stripped = stripRoute(c);
                            
    for (var i = 0; i<cfg.adminRequired.length; i++) {
        if (cfg.adminRequired[i] == stripped) {
            return true;
        }
    }
}

function stripRoute(c) {
    if (c) {
        var stripped = c.substr((c.indexOf("/",0)+1), (c.length-c.indexOf("/",0)));
        return stripped.substr(0,stripped.indexOf(".html",0));
    } else {
        return '';
    }
}
    

