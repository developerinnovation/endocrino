<?php  

namespace Drupal\ngt_login\Plugin\Form;  

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoginForm extends ConfigFormBase {
    /**  
     * {@inheritdoc}  
     */  
    protected function getEditableConfigNames() {  
        return [  
            'ngt_login.settings',  
        ];  
    }  

    /**  
     * {@inheritdoc}  
     */  
    public function getFormId() {  
        return 'ngt_login_form_settings';  
    } 

    /**  
     * {@inheritdoc}  
     */  
    public function buildForm(array $form, FormStateInterface $form_state) {  
        $config = $this->config('ngt_login.settings');  

        $help_text_new_pass = [
            'Mínimo o caracteres',
            'Incluir números 0 - 9',
            'Incluir mayúsculas',
        ];

        $form['#tree'] = true;

        // login

        $form['ngt_login'] = [  
            '#type' => 'details',
            '#title' => t('Configuraciones del login'),   
            '#open' => false,  
        ]; 

        $form['ngt_login']['image'] = [
            '#type' => 'managed_file',
            '#title' => t('Foto background'),
            '#default_value' => isset($config->get('ngt_login')['image']) ? $config->get('ngt_login')['image'] : '',
            '#description' => t('Foto fondo, de extension png jpg jpeg, tamaño no superior a 1300x820 px'),
            '#upload_location' => 's3://file-project',
            '#upload_validators' => [
              'file_validate_image_resolution' => ['1300x820'],
              'file_validate_extensions' => ['png jpg jpeg'],
            ],
        ];

        $form['ngt_login']['btn_login'] = [  
            '#type' => 'textfield',
            '#title' => t('Texto para botón de login'),   
            '#default_value' => isset($config->get('ngt_login')['btn_login']) ? $config->get('ngt_login')['btn_login'] : t('Iniciar sesión'),
            '#required' => true
        ]; 

        $form['ngt_login']['input_mail'] = [  
            '#type' => 'textfield',
            '#title' => t('Texto placeholder email'),   
            '#default_value' => isset($config->get('ngt_login')['input_mail']) ? $config->get('ngt_login')['input_mail'] : t('Correo electrónico'),
            '#required' => true
        ]; 

        $form['ngt_login']['input_pass'] = [  
            '#type' => 'textfield',
            '#title' => t('Texto placeholder contraseña'),   
            '#default_value' => isset($config->get('ngt_login')['input_pass']) ? $config->get('ngt_login')['input_pass'] : t('Contraseña'),
            '#required' => true
        ]; 

        $form['ngt_login']['title'] = [  
            '#type' => 'textfield',
            '#title' => t('Texto para saludo o título'),   
            '#default_value' => isset($config->get('ngt_login')['title']) ? $config->get('ngt_login')['title'] : t('¡Hola!'),
            '#required' => true
        ]; 

        $form['ngt_login']['message'] = [  
            '#type' => 'textarea',
            '#title' => t('texto mensaje login'),   
            '#default_value' => isset($config->get('ngt_login')['message']) ? $config->get('ngt_login')['message'] : t('Ingresa tus datos de acceso para iniciar sesión'),
            '#required' => true
        ]; 

        $form['ngt_login']['forgot_password'] = [  
            '#type' => 'textarea',
            '#title' => t('texto olvidaste contraseña'),   
            '#default_value' => isset($config->get('ngt_login')['forgot_password']) ? $config->get('ngt_login')['forgot_password'] : t('¿Olvidaste tu contraseña?'),
            '#required' => true
        ]; 

        $form['ngt_login']['new_user_message'] = [  
            '#type' => 'textarea',
            '#title' => t('texto invitar a registrarse'),   
            '#default_value' => isset($config->get('ngt_login')['new_user_message']) ? $config->get('ngt_login')['new_user_message'] : t('¿No tienes una cuenta?'),
            '#required' => true
        ];

        $form['ngt_login']['new_user_text'] = [  
            '#type' => 'textarea',
            '#title' => t('texto nuevo usuario'),   
            '#default_value' => isset($config->get('ngt_login')['new_user_text']) ? $config->get('ngt_login')['new_user_text'] : t('Regístrate aquí'),
            '#required' => true
        ];

        // restablecer contraseña

        $form['ngt_forgot_password'] = [  
            '#type' => 'details',
            '#title' => t('Configuraciones restablecer contraseña'),   
            '#open' => false,  
        ]; 

        $form['ngt_forgot_password']['mail'] = [  
            '#type' => 'textfield',
            '#title' => t('Placeholder correo electrónico'),   
            '#default_value' => isset($config->get('ngt_forgot_password')['mail']) ? $config->get('ngt_forgot_password')['mail'] : t('Correo electrónico'),
            '#required' => true
        ]; 

        $form['ngt_forgot_password']['continue'] = [  
            '#type' => 'textfield',
            '#title' => t('Texto botón continuar'),   
            '#default_value' => isset($config->get('ngt_forgot_password')['continue']) ? $config->get('ngt_forgot_password')['continue'] : t('Continuar'),
            '#required' => true
        ]; 

        $form['ngt_forgot_password']['cancell'] = [  
            '#type' => 'textfield',
            '#title' => t('Texto botón cancelar'),   
            '#default_value' => isset($config->get('ngt_forgot_password')['cancell']) ? $config->get('ngt_forgot_password')['cancell'] : t('Cancelar'),
            '#required' => true
        ];
        
        $form['ngt_forgot_password']['login_btn'] = [  
            '#type' => 'textfield',
            '#title' => t('Texto botón iniciar sesión'),   
            '#default_value' => isset($config->get('ngt_forgot_password')['login_btn']) ? $config->get('ngt_forgot_password')['login_btn'] : t('Iniciar sesión'),
            '#required' => true
        ]; 

        $form['ngt_forgot_password']['return'] = [  
            '#type' => 'textfield',
            '#title' => t('Texto botón volver'),   
            '#default_value' => isset($config->get('ngt_forgot_password')['return']) ? $config->get('ngt_forgot_password')['return'] : t('Volver'),
            '#required' => true
        ]; 

        $form['ngt_forgot_password']['title'] = [  
            '#type' => 'textfield',
            '#title' => t('Título'),   
            '#default_value' => isset($config->get('ngt_forgot_password')['title']) ? $config->get('ngt_forgot_password')['title'] : t('Restablecer contraseña'),
            '#required' => true
        ]; 

        $form['ngt_forgot_password']['msj_mail'] = [  
            '#type' => 'textfield',
            '#title' => t('Mensaje ingrese su email registrado'),   
            '#default_value' => isset($config->get('ngt_forgot_password')['msj_mail']) ? $config->get('ngt_forgot_password')['msj_mail'] : t('Ingresa el correo electrónico con el que creaste tu cuenta'),
            '#required' => true
        ]; 

        $form['ngt_forgot_password']['placeholder_code'] = [  
            '#type' => 'textfield',
            '#title' => t('Placelholder código de confirmación'),   
            '#default_value' => isset($config->get('ngt_forgot_password')['placeholder_code']) ? $config->get('ngt_forgot_password')['placeholder_code'] : t('Código de verificación'),
            '#required' => true
        ]; 

        $form['ngt_forgot_password']['code_send'] = [  
            '#type' => 'textarea',
            '#title' => t('Mensaje código enviado'),   
            '#default_value' => isset($config->get('ngt_forgot_password')['code_send']) ? $config->get('ngt_forgot_password')['code_send'] : t('Te enviamos un código de verificación al correo, el cual podrás utilizar para cambiar tu contraseña, recuerda no compartirlo y que es válido solo por 5 minutos'),
            '#required' => true
        ]; 

        $form['ngt_forgot_password']['error_code_send'] = [  
            '#type' => 'textarea',
            '#title' => t('Mensaje error al enviar código'),   
            '#default_value' => isset($config->get('ngt_forgot_password')['error_code_send']) ? $config->get('ngt_forgot_password')['error_code_send'] : t('Se presentó un error al enviar el código de verificación al correo ingresado, por favor verifica que sea el mismo con el cual te registraste en la plataforma.'),
            '#required' => true
        ]; 

        $form['ngt_forgot_password']['error_code'] = [  
            '#type' => 'textarea',
            '#title' => t('Mensaje error al verificar código'),   
            '#default_value' => isset($config->get('ngt_forgot_password')['error_code']) ? $config->get('ngt_forgot_password')['error_code'] : t('el código de verificación ingresado no es válido o ya ha caducado , por favor verifica el código recibido o intenta obtener uno nuevo.'),
            '#required' => true
        ]; 

        $form['ngt_forgot_password']['new_pass'] = [  
            '#type' => 'textarea',
            '#title' => t('Mensaje ingrese nueva contraseña'),   
            '#default_value' => isset($config->get('ngt_forgot_password')['new_pass']) ? $config->get('ngt_forgot_password')['new_pass'] : t('Ingresa tu nueva contraseña'),
            '#required' => true
        ]; 

        $form['ngt_forgot_password']['password'] = [  
            '#type' => 'textfield',
            '#title' => t('Texto placeholder input contraseña'),   
            '#default_value' => isset($config->get('ngt_forgot_password')['password']) ? $config->get('ngt_forgot_password')['password'] : t('Contraseña'),
            '#required' => true
        ]; 

        $form['ngt_forgot_password']['repeat_password'] = [  
            '#type' => 'textfield',
            '#title' => t('Texto placeholder input confirmar contraseña'),   
            '#default_value' => isset($config->get('ngt_forgot_password')['repeat_password']) ? $config->get('ngt_forgot_password')['repeat_password'] : t('Confirmar contraseña'),
            '#required' => true
        ]; 

        $form['ngt_forgot_password']['help_text_new_pass'] = [  
            '#type' => 'textarea',
            '#title' => t('Características de la contraseña'),   
            '#default_value' => isset($config->get('ngt_forgot_password')['help_text_new_pass']) ? $config->get('ngt_forgot_password')['help_text_new_pass'] : implode(PHP_EOL, $help_text_new_pass),
            '#description' => 'Ingrese linea por linea las características que la contraseña debe tener',
            '#required' => true
        ];

        $form['ngt_forgot_password']['new_pass_success_label'] = [  
            '#type' => 'textarea',
            '#title' => t('Label contraseña restablecida correctamente'),   
            '#default_value' => isset($config->get('ngt_forgot_password')['new_pass_success_label']) ? $config->get('ngt_forgot_password')['new_pass_success_label'] : t('¡Listo!'),
            '#required' => true
        ]; 

        $form['ngt_forgot_password']['new_pass_success'] = [  
            '#type' => 'textarea',
            '#title' => t('Mensaje contraseña restablecida correctamente'),   
            '#default_value' => isset($config->get('ngt_forgot_password')['new_pass_success']) ? $config->get('ngt_forgot_password')['new_pass_success'] : t('Tu contraseña se restableció correctamente'),
            '#required' => true
        ]; 

        $form['ngt_forgot_password']['new_pass_failed_label'] = [  
            '#type' => 'textarea',
            '#title' => t('Label error al restablecer contraseña'),   
            '#default_value' => isset($config->get('ngt_forgot_password')['new_pass_failed_label']) ? $config->get('ngt_forgot_password')['new_pass_failed_label'] : t('¡Algo salió mal!'),
            '#required' => true
        ]; 

        $form['ngt_forgot_password']['new_pass_failed'] = [  
            '#type' => 'textarea',
            '#title' => t('Mensaje error al restablecer contraseña'),   
            '#default_value' => isset($config->get('ngt_forgot_password')['new_pass_failed']) ? $config->get('ngt_forgot_password')['new_pass_failed'] : t('Tu contraseña no se restableció, intenta nuevamente en unos minutos'),
            '#required' => true
        ]; 

        // Crear cuenta

        $form['ngt_new_user'] = [  
            '#type' => 'details',
            '#title' => t('Configuraciones para nuevo usuario'),   
            '#open' => false,  
        ]; 

        $form['ngt_new_user']['continue'] = [  
            '#type' => 'textfield',
            '#title' => t('Texto botón continuar'),   
            '#default_value' => isset($config->get('ngt_new_user')['continue']) ? $config->get('ngt_new_user')['continue'] : t('Continuar'),
            '#required' => true
        ]; 

        $form['ngt_new_user']['cancell'] = [  
            '#type' => 'textfield',
            '#title' => t('Texto botón cancelar'),   
            '#default_value' => isset($config->get('ngt_new_user')['cancell']) ? $config->get('ngt_new_user')['cancell'] : t('Cancelar'),
            '#required' => true
        ];
        
        $form['ngt_new_user']['login'] = [  
            '#type' => 'textfield',
            '#title' => t('Texto botón iniciar sesión'),   
            '#default_value' => isset($config->get('ngt_new_user')['login']) ? $config->get('ngt_new_user')['login'] : t('Iniciar sesión'),
            '#required' => true
        ]; 

        $form['ngt_new_user']['return'] = [  
            '#type' => 'textfield',
            '#title' => t('Texto botón volver'),   
            '#default_value' => isset($config->get('ngt_new_user')['return']) ? $config->get('ngt_new_user')['return'] : t('Volver'),
            '#required' => true
        ]; 

        $form['ngt_new_user']['omit'] = [  
            '#type' => 'textfield',
            '#title' => t('Texto botón omitir'),   
            '#default_value' => isset($config->get('ngt_new_user')['omit']) ? $config->get('ngt_new_user')['omit'] : t('Omitir'),
            '#required' => true
        ]; 

        $form['ngt_new_user']['exit'] = [  
            '#type' => 'textfield',
            '#title' => t('Texto regresar al sitio'),   
            '#default_value' => isset($config->get('ngt_new_user')['exit']) ? $config->get('ngt_new_user')['exit'] : t('Regresar al inicio'),
            '#description' => t('Texto del enlace a mostrar si se genera error al crear una nueva cuenta'),
            '#required' => true
        ]; 

        $form['ngt_new_user']['title'] = [  
            '#type' => 'textfield',
            '#title' => t('título'),   
            '#default_value' => isset($config->get('ngt_new_user')['title']) ? $config->get('ngt_new_user')['title'] : t('Crea tu cuenta'),
            '#required' => true
        ]; 

        $form['ngt_new_user']['message'] = [  
            '#type' => 'textarea',
            '#title' => t('Mensaje ingresa tus datos'),   
            '#default_value' => isset($config->get('ngt_new_user')['message']) ? $config->get('ngt_new_user')['message'] : t('Ingresa tus datos personales para crear tu cuenta'),
            '#required' => true
        ]; 

        $form['ngt_new_user']['name'] = [  
            '#type' => 'textfield',
            '#title' => t('PlaceHolder Nombres y Apellidos'),   
            '#default_value' => isset($config->get('ngt_new_user')['name']) ? $config->get('ngt_new_user')['name'] : t('Nombres y Apellidos'),
            '#required' => true
        ]; 

        $form['ngt_new_user']['email'] = [  
            '#type' => 'textfield',
            '#title' => t('PlaceHolder Email'),   
            '#default_value' => isset($config->get('ngt_new_user')['email']) ? $config->get('ngt_new_user')['email'] : t('Correo electrónico'),
            '#required' => true
        ];

        $form['ngt_new_user']['date'] = [  
            '#type' => 'textfield',
            '#title' => t('PlaceHolder fecha nacimiento'),   
            '#default_value' => isset($config->get('ngt_new_user')['date']) ? $config->get('ngt_new_user')['date'] : t('Fecha de nacimiento'),
            '#required' => true
        ];

        $form['ngt_new_user']['profession'] = [  
            '#type' => 'textfield',
            '#title' => t('PlaceHolder profession'),   
            '#default_value' => isset($config->get('ngt_new_user')['profession']) ? $config->get('ngt_new_user')['profession'] : t('Profesión'),
            '#required' => true
        ];

        $form['ngt_new_user']['country'] = [  
            '#type' => 'textfield',
            '#title' => t('PlaceHolder país'),   
            '#default_value' => isset($config->get('ngt_new_user')['country']) ? $config->get('ngt_new_user')['country'] : t('País'),
            '#required' => true
        ];

        $form['ngt_new_user']['state'] = [  
            '#type' => 'textfield',
            '#title' => t('PlaceHolder departamento/estado'),   
            '#default_value' => isset($config->get('ngt_new_user')['state']) ? $config->get('ngt_new_user')['state'] : t('Departamento/Estado'),
            '#required' => true
        ];

        $form['ngt_new_user']['city'] = [  
            '#type' => 'textfield',
            '#title' => t('PlaceHolder ciudad/municipio'),   
            '#default_value' => isset($config->get('ngt_new_user')['city']) ? $config->get('ngt_new_user')['city'] : t('Ciudad/Municipio'),
            '#required' => true
        ];

        $form['ngt_new_user']['user_name'] = [  
            '#type' => 'textfield',
            '#title' => t('PlaceHolder nombre de usuario'),   
            '#default_value' => isset($config->get('ngt_new_user')['user_name']) ? $config->get('ngt_new_user')['user_name'] : t('Nombre de usuario'),
            '#required' => true
        ];

        $form['ngt_new_user']['password'] = [  
            '#type' => 'textfield',
            '#title' => t('Texto placeholder input contraseña'),   
            '#default_value' => isset($config->get('ngt_new_user')['password']) ? $config->get('ngt_new_user')['password'] : t('Contraseña'),
            '#required' => true
        ]; 

        $form['ngt_new_user']['repeat_password'] = [  
            '#type' => 'textfield',
            '#title' => t('Texto placeholder input confirmar contraseña'),   
            '#default_value' => isset($config->get('ngt_new_user')['repeat_password']) ? $config->get('ngt_new_user')['repeat_password'] : t('Confirmar contraseña'),
            '#required' => true
        ]; 

        $form['ngt_new_user']['help_text_new_pass'] = [  
            '#type' => 'textarea',
            '#title' => t('Características de la contraseña'),   
            '#default_value' => isset($config->get('ngt_new_user')['help_text_new_pass']) ? $config->get('ngt_new_user')['help_text_new_pass'] : implode(PHP_EOL, $help_text_new_pass),
            '#description' => 'Ingrese linea por linea las características que la contraseña debe tener',
            '#required' => true
        ];

        $form['ngt_new_user']['title_picture'] = [  
            '#type' => 'textarea',
            '#title' => t('Título Añade tu foto de perfil'),   
            '#default_value' => isset($config->get('ngt_new_user')['title_picture']) ? $config->get('ngt_new_user')['title_picture'] : 'Añade tu foto de perfil',
            '#required' => true
        ];

        $form['ngt_new_user']['message_picture'] = [  
            '#type' => 'textarea',
            '#title' => t('Mensaje invitación cargar foto perfil'),   
            '#default_value' => isset($config->get('ngt_new_user')['message_picture']) ? $config->get('ngt_new_user')['message_picture'] : t('¡Queremos conocerte!'),
            '#required' => true
        ]; 
        
        $form['ngt_new_user']['message_picture_load_success'] = [  
            '#type' => 'textarea',
            '#title' => t('Mensaje foto perfil cargada'),   
            '#default_value' => isset($config->get('ngt_new_user')['message_picture_load_success']) ? $config->get('ngt_new_user')['message_picture_load_success'] : t('Foto añadida correctamente'),
            '#required' => true
        ]; 

        $form['ngt_new_user']['message_picture_load_failed'] = [  
            '#type' => 'textarea',
            '#title' => t('Mensaje error al cargar foto perfil'),   
            '#default_value' => isset($config->get('ngt_new_user')['message_picture_load_failed']) ? $config->get('ngt_new_user')['message_picture_load_failed'] : t('Se presentó un error al cargar la foto, intenta nuevamente u omite este paso, podrás cargarla nuevamente accediendo a tu perfil'),
            '#required' => true
        ]; 

        $form['ngt_new_user']['ready'] = [  
            '#type' => 'textfield',
            '#title' => t('Título cuenta creada'),   
            '#default_value' => isset($config->get('ngt_new_user')['ready']) ? $config->get('ngt_new_user')['ready'] : t('¡Listo!'),
            '#required' => true
        ]; 

        $form['ngt_new_user']['message_new_user_success'] = [  
            '#type' => 'textarea',
            '#title' => t('Mensaje cuenta creada'),   
            '#default_value' => isset($config->get('ngt_new_user')['message_new_user_success']) ? $config->get('ngt_new_user')['message_new_user_success'] : t('Tu cuenta se creó correctamente'),
            '#required' => true
        ]; 

        $form['ngt_new_user']['message_new_user_welcome'] = [  
            '#type' => 'textarea',
            '#title' => t('Texto bienvenida'),   
            '#default_value' => isset($config->get('ngt_new_user')['message_new_user_welcome']) ? $config->get('ngt_new_user')['message_new_user_welcome'] : t('¡Bienvenido (a)!'),
            '#required' => true
        ]; 

        $form['ngt_new_user']['failed'] = [  
            '#type' => 'textfield',
            '#title' => t('Título error al crear cuenta'),   
            '#default_value' => isset($config->get('ngt_new_user')['failed']) ? $config->get('ngt_new_user')['failed'] : t('¡Algo salió mal!'),
            '#required' => true
        ]; 

        $form['ngt_new_user']['message_new_user_failed'] = [  
            '#type' => 'textarea',
            '#title' => t('Mensaje cuenta creada'),   
            '#default_value' => isset($config->get('ngt_new_user')['message_new_user_failed']) ? $config->get('ngt_new_user')['message_new_user_failed'] : t('No logramos crear tu cuenta, por favor intenta nuevamente, si el problema persiste por favor comunícate con nosotros mediante el formulario de contacto.'),
            '#required' => true
        ]; 

        return parent::buildForm($form, $form_state);
    } 

    /**  
     * {@inheritdoc}  
     */  
    public function submitForm(array &$form, FormStateInterface $form_state) {  
        parent::submitForm($form, $form_state);

        $this->config('ngt_login.settings')
        ->set('ngt_login', $form_state->getValue('ngt_login'))  
        ->set('ngt_forgot_password', $form_state->getValue('ngt_forgot_password'))
        ->set('ngt_new_user', $form_state->getValue('ngt_new_user'))
        ->save();   

        $fid_logo_design = $form_state->getValue('ngt_login')['image'];
        if ($fid_logo_design) {
            \Drupal::service('ngt_general.methodGeneral')->setFileAsPermanent($fid_logo_design);
        }

    }  

    /**  
     * {@inheritdoc}  
     */ 
    public function validateFormat(array &$form, FormStateInterface $form_state){
        parent::validateFormat($form, $form_state);
    }

}