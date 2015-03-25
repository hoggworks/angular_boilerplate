app.controller("NoUserAuthCtrl", function($scope, $window,$location, $window, Logger, Stats) {
    var stats = Stats.getInstance("nouserauth");
    var logger = Logger.getInstance("nouserauth");
    
    stats.add('unauthorized_view_user', 'User tried to look at content requiring login'); 
  
});