myApp.directive('ngCourseCategory', ['$http', ngCourseCategory]);

function ngCourseCategory($http){
    
    var directive = {
        restrict: 'EA',
        controller: CourseCategoryController,
        link: linkFunc
    };

    return directive;

    function linkFunc(scope, el, attr, ctrl){
        var config = drupalSettings.ngtBlock[scope.uuid_data_ng_course_category];
        scope.showMore = true;
        scope.showLoading = true;
        scope.CourseCategory = [];
        scope.ratingLoad = {
            cat : 'all',
            start : 0,
            stop : 4,
        };

    }



}

CourseCategoryController.$inject = ['$scope', '$http', '$rootScope','$interval', '$window'];
function CourseCategoryController($scope, $http, $rootScope){
    
    $scope.loadMoreCourse = function (tid,rangeInitial,rangeStop) {
        var tid = $scope.ratingLoad.cat;
        var rangeInitial = $scope.ratingLoad.start;
        var rangeStop = $scope.ratingLoad.stop;
        $scope.loadCourseCategory(tid,rangeInitial,rangeStop);
        $scope.showLoading = true;
    }

    $scope.loadCourseCategory  = function (tid,rangeInitial,rangeStop) {

        var url = `/ngt/api/v1/course/${tid}/${rangeInitial}/${rangeStop}`;
        var config_data = {};

        $http.get(url, config_data).then(function (resp) {
            if (resp.data !== undefined && resp.data !== '' && resp.data.length > 0) {
                $scope.showLoading = false;
                $scope.updaRating();
                $scope.CourseCategory = $scope.CourseCategory.concat(resp.data);
            }else {
                $scope.showMore = false;
            }
            $scope.showLoading = false;
        }, function (error) {
            alert('Se presentó un error de comunicación, por favor recargue nuevamente la página');
            $scope.showLoading = false;
        });
    }

    $scope.updaRating = function () {
        $scope.ratingLoad.start = $scope.ratingLoad.stop
        $scope.ratingLoad.stop = $scope.ratingLoad.stop + 4;
    }


    $scope.alert_message = function (message, type = 'success') {
        jQuery(".messages-only .text-alert").append('<div class="txt-message"><p>' + message + '</p></div>');
        var $html_message = jQuery('.messages-only').html();
        jQuery('.main-top').append('<div class="messages clearfix messages--'+ type +' alert alert-'+ type +'" role="contentinfo" aria-label="">' + $html_message + '</div>');
        jQuery('.messages-only .text-alert .txt-message').remove();
        jQuery('.messages .close').on('click', function () {
            jQuery('.messages').remove();
        });
    }
}