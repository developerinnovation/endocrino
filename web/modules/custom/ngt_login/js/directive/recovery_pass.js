myApp.directive('ngRecoveryPass', ['$http', ngRecoveryPass]);

function ngRecoveryPass($http){
    
    var directive = {
        restrict: 'EA',
        controller: RecoveryPassController,
        link: linkFunc
    };

    return directive;

    function linkFunc(scope, el, attr, ctrl){
        var config = drupalSettings.ngtBlock[scope.uuid_data_ng_recovery_pass];
        scope.config = config;
        scope.pathGetCode = config.pathGetCode;
        scope.pathVerifyCode = config.pathVerifyCode;
        scope.pathSetNewPass = config.pathSetNewPass;
        scope.step = 1;
        scope.message = config.config.msj_mail;
        scope.txtBtnNext = config.config.continue;
        scope.txtBtnReturn = config.config.cancell;
        scope.statusCode = true;
        scope.status_pass_label = config.config.new_pass_success_label;
        scope.status_pass = true;
        scope.messageStatusRecovery = config.config.new_pass_success;
        scope.pass_criteriar = config.pass_criteriar;
        scope.showLoading = false;
        scope.passValidate = false;
        scope.errorMailInput = '';
        scope.inputCard = {
            email : '',
            pings : '',
        }

       
    }

}

RecoveryPassController.$inject = ['$scope', '$http', '$rootScope','$interval', '$window'];
function RecoveryPassController($scope, $http, $rootScope){

    $scope.actionNext = function (){
        var next_step = false;

        switch ($scope.step) {
            case 1:
                    if($scope.inputCard.email != ''){
                        var IsValidMail = $scope.validateEmail($scope.inputCard.email);
                        if(IsValidMail){
                            $scope.btnDisabled = true;
                            $scope.showLoading = true;
                            $scope.message = '';
                            jQuery('.action-next').addClass('disabled');
                            $scope.getPing($scope.inputCard.email);
                            $scope.recoveryPass.card_email.$setValidity('card_email', true);
                        }else{
                            $scope.recoveryPass.card_email.$setValidity('card_email', false);
                            $scope.errorMailInput = 'El correo electrónico ingresado no es válido';
                        }
                    }else{
                        $scope.recoveryPass.card_email.$setValidity('card_email', false);
                        $scope.errorMailInput = 'Debe ingresar un correo electrónico válido.';
                    }
                break;
            case 2:
                    if($scope.inputCard.pings != '' && $scope.inputCard.pings.length > 3){
                        var isValidInteger = $scope.isValidInteger($scope.inputCard.pings);
                        if(isValidInteger){
                            $scope.btnDisabled = true;
                            $scope.showLoading = true;
                            $scope.message = '';
                            jQuery('.action-next').addClass('disabled');
                            $scope.verifyPing($scope.inputCard.email, $scope.inputCard.pings);
                        }else{
                            $scope.recoveryPass.card_ping.$setValidity('card_ping', false);
                            $scope.errorPingInput = 'El código ingresado no es válido';
                        }
                    }else{
                        $scope.recoveryPass.card_ping.$setValidity('card_ping', false);
                        $scope.errorPingInput = 'Debe ingresar el código enviado a su correo: '.$scope.inputCard.email;
                    } 
                break;
            case 3:
                    if($scope.inputCard.pass == $scope.inputCard.repeat_pass) {
                        if($scope.passValidate){
                            $scope.btnDisabled = true;
                            $scope.showLoading = true;
                            jQuery('.action-next').addClass('disabled');
                            $scope.setNewPass($scope.inputCard.email, $scope.inputCard.pings, $scope.inputCard.repeat_pass);
                        }else{
                            $scope.errorPassInput = 'La contraseña ingresada no cumple con el nivel de seguridad requerido';
                        }
                    }else{
                        $scope.errorPassInput = 'Las contraseñas ingresadas no coinciden';
                    }
                break;
        }

        if(next_step){
            $scope.step += 1; 
        }
    }

    $scope.validateEmail = function (email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

    $scope.isValidInteger = function (ping){
        return /^\+?([0-9]\d*)$/.test(ping);
    }

    $scope.actionReturn = function (){
        $scope.step -= 1; 
    }

    $scope.actionCancell = function (){
        window.location.href = '/user/login';
    }
    
    $scope.actionLogin = function (){
        window.location.href = '/user/login';
    }
    

    $scope.getPing  = function (mail) {
        var params = {
            'mail' : mail
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
              url: $scope.pathGetCode
            }).then(function (resp) {
                if (resp.data.status == '200') {
                    $scope.step += 1; 
                }else{
                    $scope.recoveryPass.card_email.$setValidity('card_email', false);
                }
                $scope.btnDisabled = false;
                $scope.showLoading = false;
                jQuery('.action-next').removeClass('disabled');
                $scope.message = resp.data.message;
            });
          }).catch(
            function (resp) {
                alert('Se presentó una falla de comunicación, por favor intente más tarde.');
            }
        );

    }

    $scope.verifyPing  = function (mail, ping) {
        var params = {
            'mail' : mail,
            'key_dinamic' : ping,
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
              url: $scope.pathVerifyCode
            }).then(function (resp) {
                if (resp.data.status == '200') {
                    $scope.step += 1; 
                    $scope.message = $scope.config.config.new_pass;
                }else{
                    $scope.recoveryPass.card_ping.$setValidity('card_ping', false);
                }
                $scope.btnDisabled = false;
                $scope.showLoading = false;
                jQuery('.action-next').removeClass('disabled');
                $scope.message = resp.data.message;
            });
          }).catch(
            function (resp) {
                alert('Se presentó una falla de comunicación, por favor intente más tarde.');
            }
        );
    }

    $scope.setNewPass = function (mail, ping, pass) {
        var msj = '';
        var params = {
            'mail' : mail,
            'key_dinamic' : ping,
            'pass' : pass,
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
              url: $scope.pathSetNewPass
            }).then(function (resp) {
                if (resp.data.status == '200') {
                    $scope.step += 1; 
                    $scope.txtBtnNext = $scope.config.config.login_btn;
                }else{
                    $scope.ErrorRecoveryPass = true;
                    $scope.messageStatusRecovery = $scope.config.config.new_pass_failed;
                    $scope.errorPassInput = $scope.config.config.new_pass_failed;
                    $scope.status_pass = false;
                }
                $scope.btnDisabled = false;
                $scope.showLoading = false;
                jQuery('.action-next').removeClass('disabled');
            });
          }).catch(
            function (resp) {
                alert('Se presentó una falla de comunicación, por favor intente más tarde.');
            }
        );
    }

    

    // eventos jQuery para inputs
    setTimeout(function(){

        jQuery('#card_ping').on('keyup keypress change', function (e) {
            if(e.which != 0 && e.which != 8 && (e.which < 48 || e.which > 57 )) { return false; }
        }); 

        jQuery('#card_email').on('keyup keypress change', function (e) {
            var value = jQuery(this).val();
            var regex = /[\w-\.]{2,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
            if (regex.test(value.trim())) {
                $scope.recoveryPass.card_email.$setValidity('card_email', true);
            }
            else {
                $scope.recoveryPass.card_email.$setValidity('card_email', false);
            }
            
        });

        jQuery('#pass').on('keyup keypress change', function () {
            var longMin = new RegExp('(?=.{8,})');
            var characterSpecialContain = new RegExp('(?=.*[!@#$%^&*])');
            var uppercaseContain = new RegExp('(?=.*[!@#$%^&*])');
            var numberContain = new RegExp('(?=.*[0-9])');
            var value = jQuery(this).val();
            var longMinCheck = false;
            var characterSpecialContainCheck = false;
            var uppercaseContainCheck = false;
            var numberContainCheck = false;

            if(longMin.test(value)){
                longMinCheck = true;
                jQuery('.criteriar-pass.criteriar-0').addClass('validate');
            }else{
                jQuery('.criteriar-pass.criteriar-0').removeClass('validate');
            }

            if(numberContain.test(value)){
                numberContainCheck = true;
                jQuery('.criteriar-pass.criteriar-1').addClass('validate');
            }else{
                jQuery('.criteriar-pass.criteriar-1').removeClass('validate');
            }

            if(uppercaseContain.test(value)){
                uppercaseContainCheck = true;
                jQuery('.criteriar-pass.criteriar-2').addClass('validate');
            }else{
                jQuery('.criteriar-pass.criteriar-2').removeClass('validate');
            }

            if(characterSpecialContain.test(value)){
                characterSpecialContainCheck = true;
                jQuery('.criteriar-pass.criteriar-3').addClass('validate');
            }else{
                jQuery('.criteriar-pass.criteriar-3').removeClass('validate');
            }

            if(longMinCheck && characterSpecialContainCheck && uppercaseContainCheck && numberContainCheck) {
                $scope.passValidate = true;
            }

        });
    }, 300);
}
