<?php
class UserController extends BaseController {
    private $userModel;
    private $auth;

    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->auth = new Auth();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            if ($this->auth->login($email, $password)) {
                $this->session->setFlash('success', 'Giriş başarılı!');
                $this->redirect('');
            } else {
                $this->session->setFlash('error', 'E-posta veya şifre hatalı.');
            }
        }
        $this->view('user/login', ['title' => 'Giriş Yap']);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $first_name = $_POST['first_name'] ?? '';
            $last_name = $_POST['last_name'] ?? '';
            if ($this->userModel->register($email, $password, $first_name, $last_name)) {
                $this->session->setFlash('success', 'Kayıt başarılı! Giriş yapabilirsiniz.');
                $this->redirect('user/login');
            } else {
                $this->session->setFlash('error', 'Kayıt başarısız.');
            }
        }
        $this->view('user/register', ['title' => 'Kayıt Ol']);
    }

    public function list() {
        if (!$this->auth->hasPermission('user.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('');
        }
        $users = $this->userModel->getAllUsers();
        $this->view('user/user_list', ['title' => 'Kullanıcılar', 'users' => $users]);
    }

    public function edit($id) {
        if (!$this->auth->hasPermission('user.edit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('');
        }
        $user = $this->userModel->getUserByEmail($this->session->get('user_email'));
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $first_name = $_POST['first_name'] ?? '';
            $last_name = $_POST['last_name'] ?? '';
            $password = $_POST['password'] ?? '';
            if ($this->userModel->updateUser($id, $email, $first_name, $last_name, $password)) {
                $this->session->setFlash('success', 'Kullanıcı güncellendi.');
                $this->redirect('user/list');
            } else {
                $this->session->setFlash('error', 'Güncelleme başarısız.');
            }
        }
        $this->view('user/user_edit', ['title' => 'Kullanıcı Düzenle', 'user' => $user]);
    }

    public function profile() {
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('user/login');
        }
        $user = $this->userModel->getUserByEmail($this->session->get('user_email'));
        $this->view('user/profile', ['title' => 'Profil', 'user' => $user]);
    }
}
?>