app.controller("NoAdminAuthCtrl", function($scope, $window,$location, $window, Logger, Stats) {
    var stats = Stats.getInstance("noadminauth");
    var logger = Logger.getInstance("noadminauth");
    
    stats.add('unauthorized_view_admin', 'User tried to look at content requiring admin'); 
  
});