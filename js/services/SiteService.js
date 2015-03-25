app.factory('SiteService', function($http, $cookieStore,  $rootScope) {
    var p = {};

    for (var prop in cfg) {
       // console.log(prop);
       p[prop] = cfg[prop];
    }

    // check root scope's defined cfg object (set in main.js) to see if variables are saved on the db
    if ($rootScope.cfg && $rootScope.cfg.check_server) {
        // make http call here
        
        
        // update params object if possible
    }
    
    var params = function() {
        return p;
    }
    
    var dataURL = function(u) {
        //console.log(p.dataDirectory + u);
        return p.dataDirectory + u;   
        
    }
    
    return {
        params:params,
        dataURL:dataURL
    }
});