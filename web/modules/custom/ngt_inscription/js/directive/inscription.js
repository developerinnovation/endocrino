myApp.directive('ngInscriptionButton', ['$http', '$rootScope', ngInscriptionButton]);

function ngInscriptionButton($http) {
	var directive = {
		restrict: 'EA',
		controller: InscriptionButtonController,
		link: linkFunc
	};

	return directive;

	function linkFunc(scope, el, attr, ctrl) {
        var config = drupalSettings.ngtBlock[scope.uuid_data_ng_inscription_button];
        scope.config = config;
        scope.uid = config.uid;
        scope.nid = config.nid;
        scope.id = config.id;
        scope.pathReserve = config.pathReserve;
        scope.txtReserve = config.config.inscription;
        scope.txtUnReserve = config.config.uninscription;
        scope.messageReserve = config.config.messageReserve;
        scope.messageUnReserve = config.config.messageUnReserve;
        scope.errorMessage = config.config.errorMessage;
        scope.errorMessageUnreserve = config.config.errorMessageUnreserve;
        scope.userAnonimeMessage = config.config.userAnonime;
        scope.textBtn = config.typeAction == 'reserve' ? scope.txtReserve : scope.txtUnReserve;
        scope.gotoLoginTxt = config.config.gotoLogin;
        scope.typeAction = config.typeAction;
        scope.disableBtn = config.disableBtn;
	}
}

InscriptionButtonController.$inject = ['$scope', '$http', '$rootScope'];

function InscriptionButtonController($scope, $http, $rootScope) {

    $scope.action = function (action){
        if($scope.uid != '0'){
            if(action == 'reserve'){
                $scope.reserve()
            }else{$
                $scope.unReserve();
            }
        }else{
            var message = $scope.userAnonimeMessage;
            var includeBtn = true;
            var link = '/user/login';
            var textBtn = $scope.gotoLoginTxt;

            $rootScope.showMessageModal(message, includeBtn, link, textBtn);
        }
    }

    $scope.reserve  = function () {
        $scope.disableBtn = true;
        var params = {
            'node_id' : $scope.nid,
            'user_id' : $scope.uid,
        };
        $http.get('/rest/session/token').then(function (resp) {
            $http({
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-Token': resp.data
              },
              data: params,
              url: $scope.pathReserve
            }).then(function (resp) {
                if (resp.data.status == '200') {
                    $scope.textBtn = $scope.txtUnReserve;
                    $scope.typeAction = 'unReserve';
                    $scope.id = resp.data.id;
                    $scope.disableBtn = false;
                    $rootScope.showMessageModal($scope.messageReserve);
                }else{
                    $rootScope.showMessageModal($scope.errorMessage);
                }
            });
          }).catch(
            function (resp) {
                $rootScope.showMessageModal('Se presentó una falla de comunicación, por favor intente más tarde.');
            }
        );
    }

    $scope.unReserve  = function () {
        $scope.disableBtn = true;
        var params = {
            'node_id' : $scope.nid,
            'user_id' : $scope.uid,
            'id' : $scope.id,
        };
        $http.get('/rest/session/token').then(function (resp) {
            $http({
              method: 'DELETE',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-Token': resp.data
              },
              data: params,
              url: $scope.pathReserve
            }).then(function (resp) {
                if (resp.data.status == '200') {
                    $scope.textBtn = $scope.txtReserve;
                    $scope.typeAction = 'reserve';
                    $scope.disableBtn = false;
                    $rootScope.showMessageModal($scope.messageUnReserve);
                }else{
                    $rootScope.showMessageModal($scope.errorMessageUnreserve);
                }
            });
          }).catch(
            function (resp) {
                $rootScope.showMessageModal('Se presentó una falla de comunicación, por favor intente más tarde.');
            }
        );
    }

}