app.directive('fallbackHide', function () {
 var fallbackSrc = {
    link: function postLink(scope, iElement, iAttrs) {
      iElement.bind('error', function() {
        angular.element(this).attr("src", "images/spacer.gif");
      });
    }
   }
  
   return fallbackSrc;
});


app.directive('ensureUniqueLogin', ['$http', 'UserService', 'SiteService', function($http, UserService, SiteService) {
  return {
    require: 'ngModel',
    link: function(scope, ele, attrs, c) {

      scope.$watch(attrs.ngModel, function() {
        if (UserService) {
            var at = '';
            if (UserService.getUser()) {
                at = UserService.getUser().authToken;
            }
            $http({
                method: 'POST',
                url: SiteService.dataURL("user/check_unique"),
                data: {'type':attrs.ngModel, 'val': scope.form.login.$viewValue, 'token':at}
            }).success(function(data, status, headers, cfg) {
              //  console.log(data.isUnique + " : " + data.query);
                c.$setValidity('unique', data.isUnique);
            }).error(function(data, status, headers, cfg) {
               // console.log('false');
                c.$setValidity('unique', false);
            });
        }
      });
    }
  }
}]);


app.directive('ensureUniqueEmail', ['$http', 'UserService', 'SiteService', function($http, UserService, SiteService) {
    
  return {
    require: 'ngModel',
    link: function(scope, ele, attrs, c) {

      scope.$watch(attrs.ngModel, function() {
          
        if (UserService) {
            var at = '';
            
            if (UserService.getUser()) {
                at = UserService.getUser().authToken;
            }
            $http({
                method: 'POST',
                url: SiteService.dataURL("user/check_unique"),
                data: {'type':attrs.ngModel, 'val': scope.form.user_email.$viewValue, 'token':at}
            }).success(function(data, status, headers, cfg) {
                console.log(data.isUnique + " : " + data.query);
                c.$setValidity('unique', data.isUnique);
            }).error(function(data, status, headers, cfg) {
                console.log('false' + " : " + data.cfg);
                c.$setValidity('unique', false);
            });
        }
      });
    }
  }
}]);
