<?php
class loginController extends Controller
{
    public function index()
    {
        $data = array(
            'msg' => ''
        );

        if (!empty($_POST['email'])) {
            $email = addslashes($_POST['email']);
            $password = md5($_POST['password']);

            if (!empty($_POST['email']) && !empty($_POST['password'])) {
                $u = new User();

                // Verificando se email e senha são válidos
                if ($u->verifyUser($email, $password)) {
                    $_SESSION['token'] = $u->createToken($email);
                    header('Location: '.BASE_URL);
                    exit;
                } else {
                    $data['msg'] = 'Email e/ou senha incorreto(s)!';
                }
            } else {
                $data['msg'] = 'Preencha todos os campos!';
            }
        }

        $this->loadTemplate('login', $data);
    }

    public function createAccount()
    {
        $data = array(
            'states' => array(),
            'msg' => array(
                'success' => '',
                'warning' => '',
                'success' => ''
            )
        );

        $federativeUnit = new FederativeUnit();
        $data['states'] = $federativeUnit->getFederativeUnits();

        if (!empty($_POST['name'])) {
            $name           = addslashes($_POST['name']);
            $last_name      = addslashes($_POST['last_name']);
            $email          = addslashes($_POST['email']);
            $password       = md5($_POST['password']);
            $uf             = addslashes($_POST['uf']);
            $city           = addslashes($_POST['city']);
            $phone_number   = addslashes($_POST['phone']);
            $cell_phone     = addslashes($_POST['cell_phone']);

            // Verificando se todos os campos foram preenchidos.
            if (!empty($_POST['name']) && !empty($_POST['last_name']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['uf']) && !empty($_POST['city']) && (!empty($_POST['phone']) || !empty($_POST['cell_phone']))) {
                
                $user = new User();
                $phone = new Phone();
                $userAddress = new UserAdress();
                $userToken = new UserToken();
                
                // Verificando se o email é válido
                if ($user->verifyEmail($email)) {
                    // Cadastrando usuário, telefone e endereço no banco de dados.
                    $id_user = $user->registerUser($name, $last_name, 
                    $email, $password);
                    $userAddress->registerAddress($id_user, $uf, $city);
                    $phone->registerPhone($id_user, $phone_number, $cell_phone);

                    $data['msg']['success'] = 'Conta criada com sucesso, realize o ';
                } else {
                    $data['msg']['danger'] = 'Email inválido, já se encontra em uso';
                }
            } else {
                $data['msg']['warning'] = 'Preencha todos os campos!';
            }
        }

        $this->loadTemplate('createAccount', $data);
    }
}