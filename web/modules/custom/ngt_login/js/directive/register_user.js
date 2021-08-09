myApp.directive('ngRegisterUser', ['$http', ngRegisterUser]);

function ngRegisterUser($http){
    
    var directive = {
        restrict: 'EA',
        controller: RegisterUserController,
        link: linkFunc
    };

    return directive;

    function linkFunc(scope, el, attr, ctrl){
        var config = drupalSettings.ngtBlock[scope.uuid_data_ng_register_user];
        scope.config = config;
        scope.profession = config.profession;
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
            name : '',
            email : '',
            date : '',
            professionSelect : '',
            countrySelect : '',
            stateSelect : '',
            citySelect : '',
            pass: '',
            repeat_pass: '',
            termsCondition: false,
            pic: '',
        }
    }

}

RegisterUserController.$inject = ['$scope', '$http', '$rootScope','$interval', '$window'];
function RegisterUserController($scope, $http, $rootScope){

    $scope.actionNext = function (){
        var emailValid = true; 

        if($scope.step > 6){
            window.location.href = '/user/login';
        }

        if( $scope.step > 4 ){
            $scope.txtBtnNext = $scope.config.config.login_btn;
        }

        switch ($scope.step) {
            case 1:
                    if($scope.form.name != '' && $scope.form.email != '' &&  $scope.form.date != null && $scope.form.professionSelect != undefined){
                        var verify = $scope.ValidateEmail($scope.form.email);
                        if( !verify ) {
                            var message = 'Debe ingresar un correo válido, recuerde que el correo ingresado será su usuario en la plataforma.';
                            $rootScope.showMessageModal(message);
                        }else{
                            $scope.changeStep();
                        }
                    }else {
                        var message = 'Debe ingresar todos los datos solicitados para continuar';
                        $rootScope.showMessageModal(message);
                    }
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
                    if($scope.form.pass != '' && $scope.form.repeat_pass != ''){
                        if($scope.form.pass == $scope.form.repeat_pass) {
                            if($scope.passValidate){
                                if($scope.form.termsCondition){
                                    $scope.changeStep();
                                }else{
                                    var message = 'Para continuar, debe aceptar los términos y condiciones';
                                    $rootScope.showMessageModal(message);
                                }
                            }else{
                                var message = 'La contraseña ingresada no cumple con el nivel de seguridad requerido';
                                $rootScope.showMessageModal(message);
                            }
                        }else{
                            var message = 'Las contraseñas ingresadas no coinciden';
                            $rootScope.showMessageModal(message);
                        }
                    }else{
                        var message = 'Debe asignar una contraseña que cumpla con el nivel de seguridad requerido';
                        $rootScope.showMessageModal(message);
                    }
                   
                    var value = jQuery('#pass').val();
                    $scope.validCriteriarPass(value);
                    
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

    $scope.changeStep = function (){
        $scope.step += 1; 
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

    $scope.gotoPasswordRecovery = function (){
        window.location.href = '/recovery/pass';
    }

    $scope.omitLoadPic = function (){
        $scope.txtBtnNext = 'Procesando ...';
        $scope.btnDisabled = true;
        $scope.labelStatusRegister = 'Realizando el registro';
        $scope.message = '¡Estamos procesando tu registro, pronto podrás utilizar nuestra plataforma!';
        $scope.changeStep();
        $scope.createNewUser();
    }

    $scope.checkTermConditions = function (){
        $scope.form.termsCondition = true;
    }

    $scope.ValidateEmail = function (mail){
        emailRegex = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;

        if (emailRegex.test(mail)){
            return (true)
        }
        return (false);
    }    

    $scope.getMoreTerms = function(type){
        var parentId = '';
        switch (type) {
            case 'state':
                parentId = $scope.form.countrySelect
                break;

            case 'city':
                parentId = $scope.form.stateSelect;
            break;
        }
        var parameters = {};
        
        var config_data = {
            params: parameters,
            headers: { 'Accept': 'application/json' }
        };

        var path = $scope.config.pathMoreTerms;
        path = path.replace('{vocabularyBundle}', $scope.bundleForCountry); 
        path = path.replace('{parentId}', parentId);  

        $http.get(path, config_data).then(function (resp) {
            if(resp.data){
                switch (type) {
                    case 'state':
                            $scope.state = resp.data;
                            $scope.enableState = false;
                        break;
                
                    case 'city':
                            $scope.city = resp.data;
                            $scope.enableCity = false;
                        break;
                }
            }
        }, function (error) {
            var message = 'Se presentó un error de comunicación al tratar de cargar el servicio de ubicación geográfica, por favor intente más tarde realizar el regitro. ';
            $rootScope.showMessageModal(message);
        });
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

        /*
        if(characterSpecialContain.test(value)){
            characterSpecialContainCheck = true;
            jQuery('.criteriar-pass.criteriar-3').addClass('validate');
        }else{
            jQuery('.criteriar-pass.criteriar-3').removeClass('validate');
        }
        */

        if(longMinCheck && uppercaseContainCheck && numberContainCheck) {
            $scope.passValidate = true;
        }else{
            $scope.passValidate = false;
        }
    }

    $scope.imgLoadPic = function(){
        jQuery("#imgLoadPic").click();
    }

    $scope.readURL = function(input){
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $scope.picUser = e.target.result;
                jQuery('#previewImgLoadPic').attr('src', e.target.result);
                $scope.messageStatusLoadPic = $scope.config.message_picture;
                $scope.form.pic = e.target.result;
            }
            var file = jQuery('#imgLoadPic').val();
            var path = (window.URL || window.webkitURL).createObjectURL(file);
            console.log('path', path);
            reader.readAsDataURL(input.files[0]); // convert to base64 string
        }else{
            $scope.messageStatusLoadPic = $scope.config.message_picture_load_failed;
        }
    }

    $scope.createNewUser = function(){
        var params = $scope.form;

        $scope.disableBtn = true;

        $http.get('/rest/session/token').then(function (resp) {
            $http({
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-Token': resp.data
              },
              data: params,
              url: $scope.config.pathNewUser,
            }).then(function (resp) {
                if (resp.data.status == '200') {
                    // cuenta creada
                    $scope.status_register = true;
                    $scope.status_register_label = $scope.config.config.ready;
                    $scope.messageStatusRegister = $scope.config.config.message_new_user_success;
                    $scope.labelStatusRegister = $scope.config.config.message_new_user_welcome;
                    $scope.btnDisabled = false;
                    $scope.txtBtnNext = $scope.config.config.login;
                }else{
                    // error crear usuario
                    var messageCustom = 'Detectamos que ya te encuentras registrado en nuestra plataforma, sino recuerdas la contraseña puede  registar una nueva con nuestro proceso de recuperación.';
                    $scope.status_register_label = $scope.config.ready;
                    $scope.messageStatusRegister = resp.data.error == 'Usuario existente' ? messageCustom : $scope.config.config.message_new_user_failed;
                    $scope.labelStatusRegister = $scope.config.failed;
                    $scope.status_register = false;
                    $scope.txtBtnReturn = resp.data.error == 'Usuario existente' ? 'Recuperar contraseña' : $scope.txtBtnReturn;
                    $scope.userExist = resp.data.error == 'Usuario existente' ? true : false;
                    $scope.message = '';
                    $scope.txtBtnNext = $scope.config.config.login;
                }
                $scope.changeStep();
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