angular.module('logger.module', []).provider('Logger', [function () {
    var isEnabled = true;
    
    

    this.enabled = function(_isEnabled) {
        isEnabled = !!_isEnabled;
    };
    this.$get = ['$log', 'SiteService', 'UserService', '$http', function($log, SiteService, UserService, $http) {
        this.SiteService = SiteService;
        var Logger = function(context) {
            this.context = context;
        };
        Logger.getInstance = function(context) {
            return new Logger(context);
        };
        Logger.supplant = function(str, o) {
            return str.replace(
                /\{([^{}]*)\}/g,
                function (a, b) {
                    var r = o[b];
                    return typeof r === 'string' || typeof r === 'number' ? r : a;
                }
            );
        };
        Logger.getFormattedTimestamp = function(date) {
           return Logger.supplant('{0}:{1}:{2}:{3}', [
                date.getHours(),
                date.getMinutes(),
                date.getSeconds(),
                date.getMilliseconds()
            ]);
        };
        Logger.prototype = {
            _log: function(originalFn, args) {
                if (!isEnabled) {
                    return;
                }
  
                
                var data;
                var now  = Logger.getFormattedTimestamp(new Date());
                var message = '', supplantData = [];
                switch (args.length) {
                    case 1:
                        message = Logger.supplant("{0} - {1}: {2}", [ now, this.context, args[0] ]);
                        data = {type:originalFn, msg: args[0], controller:this.context, action:'', happened:now};
                        break;
                    case 3:
                        supplantData = args[2];
                        message = Logger.supplant("{0} - {1}::{2}(\'{3}\')", [ now, this.context, args[0], args[1] ]);
                        data = {type:originalFn, msg: args[0], controller:this.context, action:args[1], data:supplantData, happened:now};
                        break;
                    case 2:
                        if (typeof args[1] === 'string') {
                            message = Logger.supplant("{0} - {1}::{2}(\'{3}\')", [ now, this.context, args[0], args[1] ]);
                            data = {type:originalFn, msg: args[0], controller:this.context, action:args[1], data:[], happened:now};
                        } else {
                            supplantData = args[1];
                            message = Logger.supplant("{0} - {1}: {2}", [ now, this.context, args[0] ]);
                            data = {type:originalFn, msg: args[0], controller:this.context, action:'', data:supplantData, happened:now};
                        }
                        
                        break;
                }
                
                if (UserService && UserService.authCode()) {
                    data.authCode = UserService.authCode();
                } else {
                    data.authCode = '';
                }
                
                $log[originalFn].call(null, Logger.supplant(message, supplantData));
                
                if (SiteService.params().saveLogToServer) {
                    if (((originalFn == "log" || originalFn == "info") && SiteService.params().saveLogInfoToServer) || (originalFn != "log" && originalFn != "info")) {
                        $http({
                            method:"POST",
                            url:SiteService.dataURL("log"),
                            data:data,
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                        }).then(function(response) {
                             console.log(response);
                        
                            if (response.result == 1) {
                                console.log("ERROR: Unable to save log to server");
                            }
                        }, function (error) {
                            console.log("ERROR: Unable to save log to server.");
                        });
                    }
                } 
            },
            log: function() {
                this._log('log', arguments);
            },
            info: function() {
                this._log('info', arguments);
            },
            warn: function() {
                this._log('warn', arguments);
            },
            debug: function() {
                this._log('debug', arguments);
            },
            error: function() {
                this._log('error', arguments);
            }
        };
        return Logger;
    }];
}]);

