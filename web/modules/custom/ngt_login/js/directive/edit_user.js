myApp.directive('ngEditUser', ['$http', ngEditUser]);

function ngEditUser($http){
    
    var directive = {
        restrict: 'EA',
        controller: EditUserController,
        link: linkFunc
    };

    return directive;

    function linkFunc(scope, el, attr, ctrl){
        var config = drupalSettings.ngtBlock[scope.uuid_data_ng_edit_user];
        scope.config = config;
        scope.profession = config.profession;
        scope.profession_default = config.dataUser.profession;
        scope.location_default = config.dataUser.location;
        scope.step = 1;
        scope.message = config.config.message;
        scope.txtBtnNext = config.config.continue;
        scope.txtBtnReturn = config.config.cancell;
        scope.statusCode = true;
        scope.status_pass_label = config.config.new_pass_success_label;
        scope.status_pass = true;
        scope.messageStatusRecovery = config.config.new_pass_success;
        scope.pass_criteriar = config.pass_criteriar;
        scope.btnDisabled = false;
        scope.country = config.country;
        scope.state = [];
        scope.city = [];

        scope.enableCountry = false;
        scope.enableState = true;
        scope.enableCity = true;

        scope.status_register = true;
        scope.status_register_label = config.config.ready;
        scope.messageStatusRegister = config.config.message_new_user_success;
        scope.labelStatusRegister = config.config.message_new_user_welcome;
        scope.messageStatusLoadPic = config.config.message_picture;
        scope.txtBtnOmit = config.config.omit;
        scope.passValidate = false;
        scope.bundleForCountry = config.bundleForCountry;
        scope.picUser = config.icon_load_pic;
        scope.setInterval = false;
        scope.userExist = false;
        scope.style = {
            'padding' : '20px',
        };

      
        scope.form = {
            name : config.dataUser.name,
            email : config.dataUser.mail,
            professionSelect : config.dataUser.profession,
            countrySelect : config.dataUser.location,
            pass: '',
            repeat_pass: '',
            pic: '',
            changePassword: 'no',
        }
    }

}

EditUserController.$inject = ['$scope', '$http', '$rootScope','$interval', '$window'];
function EditUserController($scope, $http, $rootScope){

    $scope.actionNext = function (){
        var emailValid = true; 

        if($scope.form.name != '' && $scope.form.email != ''  && $scope.form.professionSelect != undefined){
            var verify = $scope.ValidateEmail($scope.form.email);
            if( !verify ) {
                var message = 'Debe ingresar un correo válido, recuerde que el correo ingresado será su usuario en la plataforma.';
                $rootScope.showMessageModal(message);
            }else{
                if($scope.form.changePassword == 'yes'){
                    if($scope.form.pass != '' && $scope.form.repeat_pass != ''){
                        if($scope.form.pass == $scope.form.repeat_pass) {
                            if($scope.passValidate){
                                $scope.changeStep();
                            }else{
                                var message = 'La contraseña ingresada no cumple con el nivel de seguridad requerido';
                                $rootScope.showMessageModal(message);
                            }
                        }else{
                            var message = 'Las contraseñas ingresadas no coinciden';
                            $rootScope.showMessageModal(message);
                        }
                    }else{
                        var message = 'Debe asignar una contraseña que cumpla con el nivel de seguridad requerido, sino desea cambiar su contraseña, por favor seleccione la opción No del selector "¿Desea cambiar su contraseña?"';
                        $rootScope.showMessageModal(message);
                    }
                }else{
                    $scope.changeStep();
                }
                
               
                var value = jQuery('#pass').val();
                $scope.validCriteriarPass(value);

                $scope.changeStep();
            }
        }else {
            var message = 'Debe ingresar todos los datos solicitados para continuar, sino los recuerda por favor recargue la página para precargarlos.';
            $rootScope.showMessageModal(message);
        }

        switch ($scope.step) {
            case 1:
                    
                break;

            case 2:
                    if( $scope.form.countrySelect != undefined){
                        $scope.changeStep();
                    }else {
                        var message = 'Para continuar, es necesario nos confirmes tu ubicación geográfica';
                        $rootScope.showMessageModal(message);
                    }
                break;

            case 3:
                    
                    
                break;

            case 4:
                    $scope.message = $scope.config.config.message_picture;
                    $scope.txtBtnNextBkp = $scope.txtBtnNext;
                    $scope.btnDisabled = true;
                    $scope.txtBtnNext = 'Procesando ...';
                    $scope.labelStatusRegister = 'Realizando el registro';
                    $scope.message = '¡Estamos procesando tu registro, pronto podrás utilizar nuestra plataforma!';
                    $scope.changeStep();
                    $scope.createNewUser();
                break;

            case 6:
                    $scope.txtBtnNext = $scope.config.config.login;
                break;
        }
    }


    $scope.actionCancell = function (){
        window.location.href = '/user';
    }
    
    $scope.actionLogin = function (){
        window.location.href = '/user/login';
    }

    $scope.ValidateEmail = function (mail){
        emailRegex = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;

        if (emailRegex.test(mail)){
            return (true)
        }
        return (false);
    }    

    $scope.validCriteriarPass = function(value){
        var longMin = new RegExp('(?=.{8,})');
        var characterSpecialContain = new RegExp('(?=.*[!@#$%^&*])');
        var uppercaseContain = new RegExp('(?=.*[!@#$%^&*])');
        var numberContain = new RegExp('(?=.*[0-9])');
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
        }else{
            $scope.passValidate = false;
        }
    }

    $scope.updateUser = function(){
        var params = $scope.form;
        $http.get('/rest/session/token').then(function (resp) {
            $http({
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-Token': resp.data
              },
              data: params,
              url: $scope.config.pathUpdateUser,
            }).then(function (resp) {
                if (resp.data.status == '200') {
                    // cuenta actualizada
                    var messageCustom = 'Perfil actualizado de forma exitosa.';
                }else{
                    // error actualizando usuario
                    var messageCustom = 'No fue posible actualizar su perfil, por favor intente más tarde la acción.';
                }
                $rootScope.showMessageModal(messageCustom);
            });
          }).catch(
            function (resp) {
                $rootScope.showMessageModal('Se presentó una falla de comunicación, por favor intente más tarde.');
            }
        );
    }

    setInterval(function(){ 
        jQuery("#imgLoadPic").change(function() {
            $scope.readURL(this);
        });
    }, 600);

    // eventos jQuery para inputs
    setTimeout(function(){
        jQuery('#pass').on('keyup keypress change', function () {
            var value = jQuery(this).val();
            $scope.validCriteriarPass(value);
        });
        
    }, 600);


}