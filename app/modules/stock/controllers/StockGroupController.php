<?php
class StockGroupController extends BaseController {
    private $groupModel;
    private $auth;

    public function __construct() {
        parent::__construct();
        $this->groupModel = new StockGroupModel();
        $this->auth = new Auth();
    }

    public function list() {
        if (!$this->auth->hasPermission('stock.group.edit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('stock/list');
        }
        $groups = $this->groupModel->getAllGroups();
        $sub_groups = $this->groupModel->getAllSubGroups();
        $sub_sub_groups = $this->groupModel->getAllSubSubGroups();
        $this->view('stock/group_list', [
            'title' => 'Stok Grupları',
            'groups' => $groups,
            'sub_groups' => $sub_groups,
            'sub_sub_groups' => $sub_sub_groups
        ]);
    }

    public function add() {
        if (!$this->auth->hasPermission('stock.group.edit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('stock/groupList');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = $_POST['type'];
            $data = [
                'code' => $_POST['code'],
                'name' => $_POST['name'],
                'description' => $_POST['description']
            ];
            $success = false;
            if ($type === 'group') {
                $success = $this->groupModel->addGroup($data);
            } elseif ($type === 'sub_group') {
                $data['group_id'] = $_POST['group_id'];
                $success = $this->groupModel->addSubGroup($data);
            } elseif ($type === 'sub_sub_group') {
                $data['sub_group_id'] = $_POST['sub_group_id'];
                $success = $this->groupModel->addSubSubGroup($data);
            }
            if ($success) {
                $this->session->setFlash('success', 'Grup eklendi.');
                $this->redirect('stock/groupList');
            } else {
                $this->session->setFlash('error', 'Grup ekleme başarısız.');
            }
        }
        $groups = $this->groupModel->getAllGroups();
        $this->view('stock/group_add', [
            'title' => 'Grup Ekle',
            'groups' => $groups
        ]);
    }

    public function edit($id, $type) {
        if (!$this->auth->hasPermission('stock.group.edit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('stock/groupList');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'code' => $_POST['code'],
                'name' => $_POST['name'],
                'description' => $_POST['description']
            ];
            $success = false;
            if ($type === 'group') {
                $success = $this->groupModel->updateGroup($id, $data);
            } elseif ($type === 'sub_group') {
                $data['group_id'] = $_POST['group_id'];
                $success = $this->groupModel->updateSubGroup($id, $data);
            } elseif ($type === 'sub_sub_group') {
                $data['sub_group_id'] = $_POST['sub_group_id'];
                $success = $this->groupModel->updateSubSubGroup($id, $data);
            }
            if ($success) {
                $this->session->setFlash('success', 'Grup güncellendi.');
                $this->redirect('stock/groupList');
            } else {
                $this->session->setFlash('error', 'Grup güncelleme başarısız.');
            }
        }
        $groups = $this->groupModel->getAllGroups();
        $this->view('stock/group_edit', [
            'title' => 'Grup Düzenle',
            'groups' => $groups,
            'type' => $type,
            'id' => $id
        ]);
    }
}
?>