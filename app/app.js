var app = angular.module('toDos', ['ngRoute', 'ngAnimate', 'toaster','ui.bootstrap']);

app.config(['$routeProvider',
function ($routeProvider) {
  $routeProvider.
  when('/login', {
    title: 'Login',
    templateUrl: 'partials/login.html',
    controller: 'authCtrl'
  })
  .when('/logout', {
    title: 'Logout',
    templateUrl: 'partials/login.html',
    controller: 'logoutCtrl'
  })
  .when('/signup', {
    title: 'Signup',
    templateUrl: 'partials/signup.html',
    controller: 'authCtrl'
  })
  .when('/welcome', {
    title: 'Dashboard',
    templateUrl: 'partials/welcome.html',
    controller: 'authCtrl'
  })
  .when('/profile', {
    title: 'Profile',
    templateUrl: 'partials/profile.html',
    controller: 'authCtrl'
  })
  .when('/', {
    title: 'Login',
    templateUrl: 'partials/login.html',
    controller: 'authCtrl',
    role: '0'
  })
  .otherwise({
    redirectTo: '/login'
  });
}])
.run(function ($rootScope, $location, Data) {
  $rootScope.$on("$routeChangeStart", function (event, next, current) {
    $rootScope.authenticated = false;
    Data.get('session').then(function (results) {

      if (results.uid) {       
        $rootScope.user = {
          uid : results.uid,
          name : results.name,
          email : results.email
        }
      } else {
        var nextUrl = next.$$route.originalPath;
        if (nextUrl == '/signup' || nextUrl == '/login') {

        } else {
          $location.path("/login");
        }
      }
    });
  });
});



app.controller('projectController', function($scope, $http, Data,$element) {
  
  $scope.task={
      description: "",
      project: $scope.project.id
  };
  

  $scope.headers={
      view: "",
      edit: "hidden"
  };
  $scope.toogleheaders = function () {
    // console.log($scope.headers);
    if($scope.headers.view==""){ // update to angular ngHide in next version
      $scope.headers={
          view: "hidden",
          edit: ""
      };
    }else{
      $scope.headers={
          view: "",
          edit: "hidden"
      };
    }
  };
  $scope.updateproject = function (project) {
    $scope.toogleheaders();    
    $http.post("api/v1/updateproject",{
            project: project
        }).success(function(result){
      // console.log(result['r']); uncomment for tests
      Data.toast(result); 
            
    });

  };  

  $scope.gettasks = function (task) {
    $scope.tasks={};
    
    
    $http.get("api/v1/gettasks?project="+$scope.project.id+"&status=0").success(function(data){
      $scope.tasks.todo = data;
    });

    $http.get("api/v1/gettasks?project="+$scope.project.id+"&status=2").success(function(data){
      $scope.tasks.done = data;
    });
    
    // update in next version to angular focus
    var s=$element.find("input").length;
    if (s) {
      s=s-1;
      
      $element.find("input")[s].focus();
    }
    
   
  };  
  $scope.gettasks();

  $scope.addTask = function (task) {
    // task.project=$scope.project.id;

    $http.post("api/v1/createtask",{
            task: task
        }).success(function(result){
      // console.log(result['r']); uncomment to see results 
      Data.toast(result);      
      $scope.gettasks();
      $scope.task={
          description: "",
          project: $scope.project.id
      };;
            
    });
  };


});

app.controller('tasksController', function($scope, $http, Data, $element, Modal) {

  
  $scope.deleteTask = function (task) {
    if(confirm("Are you sure to delete this?")){
      Data.post("deletetask",{
        task:task
      }).then(function(data){
        $scope.$parent.gettasks();
      });
    }
  };

  $scope.deleteTaskModal = function (task) {
    Modal.open('Are you sure to delete this?',function(){      
      Data.post("deletetask",{
        task:task
      }).then(function(data){
        $scope.$parent.gettasks();
      });
    });
  
  };

  $scope.toggleStatus = function(task) {

    var or_status=task.status;
    if(task.status=='2'){task.status='0';}else{task.status='2';}
    
    Data.post("updatetask",{
        task: task
    }).then(function(data){        

      $scope.$parent.gettasks();
    });
  };

});

app.controller('welcomeController', function($scope, $http, Data,$location,$filter) {
  
  $scope.project={
      description: "",
      
  };
  
  $scope.getProjects =  function(){  

    $http.get("api/v1/getprojects").success(function(data){
      $scope.projects = {
          left: [],
          right: []
      };
      data=$filter('orderBy')(data, '-id');

      for (var i=0;i<data.length;i++){
          if ((i+2)%2==0) {
              $scope.projects.right.push(data[i]);
          }
          else {
              $scope.projects.left.push(data[i]);
          }
      }
      

    });
  };
  $scope.getProjects(); // Load all available projects 

  $scope.createproject = function (project) {
    Data.post('createproject', {
        project: project
    }).then(function (results) {
        Data.toast(results);
        $scope.getProjects();
        $scope.project=$scope.project={
            description: "",
            
        };;
    });
  };

  $scope.deleteproject = function (project) {
    Data.post('deleteproject', {
        project: project
    }).then(function (results) {
        Data.toast(results);
        $scope.getProjects();        
    });
  };

});

app.controller('profileController', function($scope, $http, Data,$location) {
  
  $scope.user={
    name:'',
    email:'',
    uid:'0',
    phone:''

  };
  $scope.getuser = function () {
    Data.get('getuser').then(function (results) {
        $scope.user=results;
    });
  }
  $scope.getuser();

  $scope.updateuser= function (user) {
    console.log(['user',user]);
    Data.post("updateuser",{
            user: user
        }).then(function(result){
          console.log(result);
      Data.toast(result); 
      $scope.getuser();       
    });

  };  

});



