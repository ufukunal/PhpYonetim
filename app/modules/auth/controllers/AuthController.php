<?php
class AuthController extends BaseController {
    private $authModel;
    private $auth;

    public function __construct() {
        parent::__construct();
        $this->authModel = new AuthModel();
        $this->auth = new Auth();
    }

    public function roleList() {
        if (!$this->auth->hasPermission('auth.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('');
        }
        $roles = $this->authModel->getAllRoles();
        $this->view('auth/role_list', ['title' => 'Roller', 'roles' => $roles]);
    }

    public function roleEdit($id = null) {
        if (!$this->auth->hasPermission('auth.edit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            if ($this->authModel->addRole($name, $description)) {
                $this->session->setFlash('success', 'Rol eklendi.');
                $this->redirect('auth/roleList');
            } else {
                $this->session->setFlash('error', 'Rol ekleme başarısız.');
            }
        }
        $this->view('auth/role_edit', ['title' => 'Rol Düzenle']);
    }

    public function permissionList() {
        if (!$this->auth->hasPermission('auth.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('');
        }
        $permissions = $this->authModel->getAllPermissions();
        $this->view('auth/permission_list', ['title' => 'İzinler', 'permissions' => $permissions]);
    }

    public function permissionEdit($id = null) {
        if (!$this->auth->hasPermission('auth.edit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            if ($this->authModel->addPermission($name, $description)) {
                $this->session->setFlash('success', 'İzin eklendi.');
                $this->redirect('auth/permissionList');
            } else {
                $this->session->setFlash('error', 'İzin ekleme başarısız.');
            }
        }
        $this->view('auth/permission_edit', ['title' => 'İzin Düzenle']);
    }
}
?>