app.controller('authCtrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    //initially set those objects to null to avoid undefined error
    
    $scope.login = {};
    $scope.signup = {};
    $scope.doLogin = function (customer) {
        
        
        Data.post('login', {
            customer: customer,

        }).then(function (results) {
            // console.log(results); uncomment to see results
            Data.toast(results);
            if (results.status == "success") {
                $location.path('welcome');
            }
        });
    };
    $scope.signup = {email:'',password:'',name:'',phone:''};
    $scope.signUp = function (customer) {
        Data.post('signUp', {
            customer: customer
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('welcome');
            }
        });
    };
    

    $scope.logout = function () {
        Data.get('logout').then(function (results) {
            Data.toast(results);
            $location.path('login');
        });
    }
});