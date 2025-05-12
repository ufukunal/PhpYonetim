<?php
class CustomerGroupController extends BaseController {
    private $groupModel;
    private $auth;

    public function __construct() {
        parent::__construct();
        $this->groupModel = new CustomerGroupModel();
        $this->auth = new Auth();
    }

    public function list() {
        if (!$this->auth->hasPermission('customer.group.edit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('customer/list');
        }
        $groups = $this->groupModel->getAllGroups();
        $this->view('customer/group_list', [
            'title' => 'Müşteri Grupları',
            'groups' => $groups
        ]);
    }

    public function add() {
        if (!$this->auth->hasPermission('customer.group.edit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('customer/groupList');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'code' => $_POST['code'],
                'name' => $_POST['name'],
                'description' => $_POST['description']
            ];
            if ($this->groupModel->addGroup($data)) {
                $this->session->setFlash('success', 'Grup eklendi.');
                $this->redirect('customer/groupList');
            } else {
                $this->session->setFlash('error', 'Grup ekleme başarısız.');
            }
        }
        $this->view('customer/group_add', [
            'title' => 'Grup Ekle'
        ]);
    }

    public function edit($id) {
        if (!$this->auth->hasPermission('customer.group.edit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('customer/groupList');
        }
        $group = $this->groupModel->getGroupById($id);
        if (!$group) {
            $this->session->setFlash('error', 'Grup bulunamadı.');
            $this->redirect('customer/groupList');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'code' => $_POST['code'],
                'name' => $_POST['name'],
                'description' => $_POST['description']
            ];
            if ($this->groupModel->updateGroup($id, $data)) {
                $this->session->setFlash('success', 'Grup güncellendi.');
                $this->redirect('customer/groupList');
            } else {
                $this->session->setFlash('error', 'Grup güncelleme başarısız.');
            }
        }
        $this->view('customer/group_edit', [
            'title' => 'Grup Düzenle',
            'group' => $group
        ]);
    }
}
?>