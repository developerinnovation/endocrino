myApp.directive('ngEvaluation', ['$http', ngEvaluation]);

function ngEvaluation($http){
    
    var directive = {
        restrict: 'EA',
        controller: EvaluationController,
        link: linkFunc
    };

    return directive;

    function linkFunc(scope, el, attr, ctrl){
        var config = drupalSettings.ngtBlock[scope.uuid_data_ng_evaluation];
        scope.tabs = 'presentation';
        scope.moduleId = config.idModule;
        scope.nid = config.nid;
        scope.uid = config.uid;
        scope.evaluationNav = 1;
        scope.maxNavValue = config.total_questions;
        scope.minNavValue = 1;
        scope.answers = config.data;
        scope.countAnswer = 0;
        scope.isDisabledNext = true;
        scope.isDisabledSendAnswers = true;
        scope.idEvaluation = null;
        scope.pathAnswers = config.pathAnswers;
        scope.average = config.average;
        scope.idCourse = config.idCourse;
        scope.pathAnswersStart = config.pathAnswersStart;
        
        scope.evaluation_config = config.evaluation_config;
        scope.result = false; // cambiar a false en prod
        scope.averageFinal = 0; // cambiar a 0 en prod
        scope.titleMessageFinal = ''; // cambiar en prod a ''
        scope.messageFinalResul = ''; // cambiar en prod a ''
        scope.textBtnFinal = 'Descargar certificado';
        scope.returnToCourse = 'Volver al curso';
        scope.showMessageFinalResul = false; // cambiar a false en prod
        scope.messageGeneral = config.evaluation_config.message_approved;
        scope.status_evaluation = true;
        scope.tokenApprovedEvaluation = '';
        scope.urlCourse = '';
        scope.isDisabledStart = false;
        scope.download_certificate = '';

        var isValidEvaluation = window.location.search.split('-');
        if(isValidEvaluation.length != 3){
            scope.isDisabledStart = true;
            alert('Acceso denegado, parece que está ingresando a una url errónea');
        }
    }

}

EvaluationController.$inject = ['$scope', '$http', '$rootScope','$interval', '$window'];
function EvaluationController($scope, $http, $rootScope){


    $scope.actionAfterResult = function (){
        if($scope.status_evaluation){
            $scope.downloadCertificate();
        }else{
            location.reload();
        }
    }

    $scope.downloadCertificate = function (){
        var urlCertificate = '/render/certificate/'+ $scope.idCourse +'/'+ $scope.nid +'/'+ $scope.moduleId +'/'+ $scope.uid +'/' + $scope.tokenApprovedEvaluation + '/' + $scope.idEvaluation;
        var pdfCertificate = '/render/pdf/certificate/'+ $scope.idCourse +'/'+ $scope.nid +'/'+ $scope.moduleId +'/'+ $scope.uid +'/' + $scope.tokenApprovedEvaluation + '/' + $scope.idEvaluation;
        var messageCertificate = 'Estamos generando su certificado, pronto se descargará.';
        $rootScope.showMessageModal(messageCertificate);
        var pathCertificate = '';

        var config_data = {};

        $http.get(pdfCertificate, config_data).then(function (resp) {
            if (resp.data.code == '200') {
                pathCertificate = resp.data.certificate.browser_path;
                $scope.download_certificate = pathCertificate;
                if (pathCertificate != ''){
                    $rootScope.close_message();
                    messageCertificate = 'Se ha descargado su certificado, felicitaciones.';
                    $rootScope.showMessageModal(messageCertificate);
                    window.open(pathCertificate, '_blank');
                }else {
                    messageCertificate = 'Se presentó un error al descargar el certificado, por favor contacta al equipo de soporte.';
                    $rootScope.showMessageModal(messageCertificate);
                }
            }else{
                var errorMessage = 'Se presentó un error al descargar el certificado, por favor contacta al equipo de soporte.';
                $rootScope.showMessageModal(errorMessage);
            }
        }, function (error) {
            $rootScope.showMessageModal('Se presentó una falla de comunicación, por favor intente más tarde.');
        });
    }

    $scope.returnToCourseLink = function (){
        window.location.href = $scope.urlCourse;
    }

    $scope.returnPrevPage = function (){
        window.location.href = document.referrer;;
    }
    
    $scope.changeTabs = function (tabs){
        if(tabs == 'evaluation') {
            $scope.startEvaluation();
        }
        $scope.tabs = tabs;
        jQuery('li#question_1').addClass('active');
    }

    $scope.changeEvaluationNav = function (type){
        if(type == 'prev') {
            $scope.evaluationNav = $scope.evaluationNav - 1;
            $scope.isDisabledNext = false;
        }else{
            $scope.evaluationNav = $scope.evaluationNav + 1;
            if($scope.answers[$scope.evaluationNav - 1] != ''){
                $scope.isDisabledNext = false;
            }else{
                $scope.isDisabledNext = true;
            }
            
        }
        var nav = $scope.evaluationNav;
        jQuery('li#question_' + nav).addClass('active');
    }

    jQuery('button.bool').click(function( event ) {
        event.preventDefault();
    });

    $scope.checkAnswer = function(questionId, valueId, typeQuestion){
        var arrLastAnswer = [];
        var lastAnswer = '';
        var newValue = '';
        if(typeQuestion == 'multiple'){
            lastAnswer = $scope.answers[questionId];
            arrLastAnswer.push(lastAnswer);
            arrLastAnswer.push(valueId);
            newValue = arrLastAnswer.join();
        }else{
            newValue = valueId.toString();
        }
        $scope.answers[questionId] = newValue;
        $scope.countAnswer = $scope.countAnswer + 1;
        $scope.isDisabledNext = false;
        if($scope.maxNavValue == $scope.countAnswer && $scope.answers[questionId] != ''){
            $scope.isDisabledSendAnswers = false;
        }
    }

    $scope.changeClassBool = function(questionId, response, idElement, classElement){
        jQuery('.' + classElement).removeClass('selected');
        jQuery('#' + idElement).addClass('selected');
    }

    $scope.startEvaluation = function(){
        var params = {
            'answer' : $scope.answers,
            'average' : $scope.average,
            'moduleId' : $scope.moduleId,
            'nid' : $scope.nid,
            'idCourse' : $scope.idCourse,
            'maxNavValue' : $scope.maxNavValue,
        };
        $scope.idEvaluation = 1; // eliminar al conectar el flujo
        $http.get('/rest/session/token').then(function (resp) {
            $http({
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-Token': resp.data
              },
              data: params,
              url: $scope.pathAnswersStart
            }).then(function (resp) {
                if (resp.data.status == '200') {
                    $scope.idEvaluation = resp.data.id;
                }else{
                    var errorMessage = 'Se presentó un error al inciar el examen, por favor recarga la página e intenta nuevamente, si el error continua por favor repórtalo al equipo de soporte.';
                    $rootScope.showMessageModal(errorMessage);
                }
            });
          }).catch(
            function (resp) {
                $rootScope.showMessageModal('Se presentó una falla de comunicación, por favor intente más tarde.');
            }
        );
    }

    $scope.sendAnswers = function(){
        $scope.isDisabledSendAnswers = true;
        var params = {
            'answer' : $scope.answers,
            'average' : $scope.average,
            'moduleId' : $scope.moduleId,
            'nid' : $scope.nid,
            'idCourse' : $scope.idCourse,
            'idEvaluation' : $scope.idEvaluation,
        };
        $http.get('/rest/session/token').then(function (resp) {
            $http({
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-Token': resp.data
              },
              method: 'PUT',
              data: params,
              url: $scope.pathAnswers
            }).then(function (resp) {
                if (resp.data.status == '200') {
                    if(resp.data.evaluation != 'bad' && resp.data.countCorrectly >= resp.data.totalAnswersMin){
                        $scope.titleMessageFinal = $scope.evaluation_config.success;
                        $scope.messageFinalResul = $scope.evaluation_config.success_message;
                        $scope.messageGeneral = $scope.evaluation_config.message_approved;
                        $scope.tokenApprovedEvaluation = resp.data.token;
                        $scope.status_evaluation = true;
                    }else{
                        $scope.titleMessageFinal = $scope.evaluation_config.faild;
                        $scope.messageFinalResul = $scope.evaluation_config.attempets;
                        $scope.messageGeneral = $scope.evaluation_config.faild_message;
                        $scope.textBtnFinal = 'Repetir evaluación';
                        $scope.status_evaluation = false;
                    }
                    $scope.averageFinal = resp.data.averageObtained;
                    $scope.showMessageFinalResul = true;
                    $scope.urlCourse = resp.data.urlCourse;
                    $scope.result = true;
                }else{
                    var errorMessage = 'Se presentó un error al procesar las respuestas del examen, por favor comunícate con el equipo de soporte.';
                    $rootScope.showMessageModal(errorMessage);
                }
            });
          }).catch(
            function (resp) {
                $rootScope.showMessageModal('Se presentó una falla de comunicación al tratar de enviar las respuestas, por favor comunícate con el equipo de soporte.');
            }
        );
    }
 
}

