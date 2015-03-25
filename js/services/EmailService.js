app.factory('EmailService', function($http, $cookieStore, SiteService, UserService, ErrorService, StatsService) {
    var user = {};

    user.logged = 0;
    
   
    var sendEmail = function(type) 
    {
        // assemble http object
        
        // add promise
        
        
        // respond with error if necessary
        
        // save stat
        
        
        return true;
    }
    
    return {
       sendEmail:sendEmail
    }
});