app.factory("Modal", ['$modal',
    function ($modal) { // This service work with modal
        var obj = {};
        var opts = {
        backdrop: true,
        backdropClick: true,
        dialogFade: false,
        keyboard: true,
        templateUrl : 'partials/modal/delete.html',
        controller : ModalInstanceCtrl,
        resolve: {} // empty storage
          };
        

        obj.open = function (title,success) {
            opts.resolve.item = function() {
                return angular.copy({title:title}); // pass title to Dialog
            }
            var modalInstance = $modal.open(opts);
            modalInstance.result.then(function(){
              //on ok button press 
              success();
            },function(){
              //on cancel button press
              // console.log("Modal Closed"); uncomment for tests
              
              
            });

        };


        return obj;
}]);

app.controller('Nav', function($scope) {
  });


var ModalInstanceCtrl = function($scope, $modalInstance, $modal, item) {
    
     $scope.item = item;
    
      $scope.ok = function () {
        $modalInstance.close();
      };
      
      $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
      };
}