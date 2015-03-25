// this controller is invoked when a user attempts to launch a route that doesn't exist
app.controller("ErrorCtrl", function($scope, UserService, $window,$location, $window, Logger, Stats) {
    var stats = Stats.getInstance("error");
    var logger = Logger.getInstance("error");
});