myApp.directive('ngNodeRightCourse', ['$http', '$rootScope',ngNodeRightCourse]);

function ngNodeRightCourse($http){
    
    var directive = {
        restrict: 'EA',
        controller: NodeRightCourseController,
        link: linkFunc
    };

    return directive;

    function linkFunc(scope, el, attr, ctrl){
        var config = drupalSettings.ngtBlock[scope.uuid_data_ng_node_right_course];
        scope.config = config;
        scope.uid = config.uid;
        scope.nid = config.nid;
        scope.userRegisterMessage = config.config.userRegister;
        scope.userAnonimeMessage = config.config.userAnonime;

        scope.tabsType = [
            {
                id: 1,
                name: 'Presentación',
            }, 
            {
                id: 2,
                name: 'Contenido',
            },
            {
                id: 3,
                name: 'Comentarios',
            }
        ];
        
        if(location.hash.substring(0,7) == '#module') {
            scope.tab = 'contentMain';
            scope.myTabsType = scope.tabsType[1];

            setTimeout(function(){ 
                var moduleId = location.hash;
                var element = document.querySelector(moduleId);
                element.scrollIntoView();
            }, 1000);
           
        }else{
            scope.tab = 'contentPresentation';
            scope.myTabsType = scope.tabsType[0];
        }
        
    }

}

NodeRightCourseController.$inject = ['$scope', '$http', '$rootScope','$interval', '$window'];
function NodeRightCourseController($scope, $http, $rootScope){
    
    $scope.$watch('myTabsType', function() {
        switch ($scope.myTabsType.id) {
            case 1:
                    $scope.tab = 'contentPresentation'
                    jQuery('.body.main.node-course').removeClass('noPaddingTop');
                break;
            case 2:
                    $scope.tab = 'contentMain'
                    jQuery('.body.main.node-course').removeClass('noPaddingTop');
                 break;
            case 3:
                    $scope.tab = 'contentCommunity'
                    jQuery('.body.main.node-course').addClass('noPaddingTop');
                break;
        }
    });

    $scope.showMessage = function(){
        if($scope.uid != '0'){
            var message = $scope.userRegisterMessage;
            $rootScope.showMessageModal(message);
        }else{
            var message = $scope.userAnonimeMessage;
            var includeBtn = true;
            var link = '/user/login';
            var textBtn = $scope.gotoLoginTxt;

            $rootScope.showMessageModal(message, includeBtn, link, textBtn);
        }
    }
}