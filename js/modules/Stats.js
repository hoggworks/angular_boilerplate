angular.module('stats.module', []).provider('Stats', [function () {
    var isEnabled = true;
    

    this.enabled = function(_isEnabled) {
        isEnabled = !!_isEnabled;
    };
    this.$get = ['SiteService', 'UserService', '$http','Logger', function(SiteService, UserService, $http, Logger) {
        this.SiteService = SiteService;
        var logger = Logger.getInstance("stats");
        var Stats = function(context) {
            this.context = context;
        };
        Stats.getInstance = function(context) {
            return new Stats(context);
        };
        
        Stats.prototype = {
            _stats: function(args) {
                if (!isEnabled) {
                    return;
                }
                
                
                var supplantData = [];
                switch (args.length) {
                    case 1:
                        data = {type:args[0],  controller:this.context, action:''};
                        break;
                    case 2:                    
                        data = {type:args[0], msg: args[1], controller:this.context, action:''};
                        break;
                    case 4:
                        supplantData = args[3];
                        data = {type:args[0], msg: args[1], controller:this.context, action:args[2], data:supplantData};
                        break;
                    case 3:
                        if (typeof args[2] === 'string') {
                            data = {type:args[0], msg: args[1], controller:this.context, action:args[2], data:[]};
                        } else {
                            supplantData = args[2];
                            data = {type:args[0], msg: args[1], controller:this.context, action:'', data:supplantData};
                        }
                        
                        break;
                }
                
                if (UserService) {
                    if (UserService.authCode()) {
                        data.authCode = UserService.authCode();
                    } else {
                        data.authCode = '';
                    }
                } else {
                    data.authCode = '';
                }
                
                
                //logger.log(data);
                if (SiteService.params().saveStatsToServer) {
                       // logger.log('data : ' + data);
                        $http({
                            method:"POST",
                            url:SiteService.dataURL("stat/add"),
                            data:data,
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                        }).then(function(response) {
                             console.log(response);
                        
                            if (response.result == 0) {
                                console.log("ERROR: Unable to save stats to server");
                            }
                        }, function (error) {
                            console.log("ERROR: Unable to save stats to server.");
                        });
                } 
            },
            add: function() {
                this._stats(arguments);
            }
        };
        return Stats;
    }];
}]);

