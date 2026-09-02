<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Product_category extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Product_category';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides view
     * @return RedirectResponse|void\
     */
    public function index()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;
            $product_categoryTable = DB()->table('product_category');
            $data['product_category_data'] = $product_categoryTable->where('sch_id', $shopId)->get()->getResult();

            $data['menu'] = view('Admin/menu_stock');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Product_category/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method provides view create
     * @return RedirectResponse|void\
     */
    public function create()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $data['action'] = base_url('Admin/Product_category/create_action');

            $data['menu'] = view('Admin/menu_stock');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/Product_category/create', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store Product category
     * @return void\
     */
    public function create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['product_category'] = $this->request->getPost('product_category');
        $data['parent_pro_cat'] = $this->request->getPost('parent_pro_cat') ?? 0;
        $data['status'] = '1';
        $data['sch_id'] = $shopId;
        $data['createdBy'] = $userId;
        $data['createdDtm'] = date('Y-m-d h:i:s');

        $this->validation->setRules([
            'product_category' => ['label' => 'product_category', 'rules' => 'required|only_numeric_not_allow|validusername|max_length[32]'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            $product_categoryTable = DB()->table('product_category');
            if ($product_categoryTable->insert($data)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> Crate data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @description This method provides update view
     * @param int $id
     * @return RedirectResponse|void\
     */
    public function update($id)
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $product_categoryTable = DB()->table('product_category');
            $data['product_category'] = $product_categoryTable->where('prod_cat_id', $id)->get()->getRow();
            $data['action'] = base_url('Admin/Product_category/update_action');

            $data['menu'] = view('Admin/menu_stock');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['update'] == 1) {
                echo view('Admin/Product_category/update', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method update Product category
     * @return void\
     */
    public function update_action()
    {
        $userId = $this->session->userId;

        $data['prod_cat_id'] = $this->request->getPost('prod_cat_id');
        $data['product_category'] = $this->request->getPost('product_category');
        $data['parent_pro_cat'] = $this->request->getPost('parent_pro_cat');
        $data['status'] = $this->request->getPost('status');
        $data['updatedBy'] = $userId;

        $this->validation->setRules([
            'product_category' => ['label' => 'product_category', 'rules' => 'required|only_numeric_not_allow|validusername|max_length[32]'],
            'status' => ['label' => 'status', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            $product_categoryTable = DB()->table('product_category');
            if ($product_categoryTable->where('prod_cat_id', $data['prod_cat_id'])->update($data)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> Update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }



    public function csv_action()
    {
        $validationRule = [
            'file' => [
                'label' => 'CSV File',
                'rules' => 'uploaded[file]|ext_in[file,csv]|max_size[file,2048]',
            ],
        ];

        if (!$this->validate($validationRule)) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">'. $this->validator->getErrors().'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Product_category/create'));
        }

        $file = $this->request->getFile('file');

        if (!$file->isValid() || $file->hasMoved()) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Invalid file upload.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Product_category/create'));
        }

        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads', $newName);
        $filePath = WRITEPATH . 'uploads/' . $newName;

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Unable to open CSV file.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Product_category/create'));
        }

        $db      = \Config\Database::connect();
        $builder = $db->table('product_category');
        $schId = $this->session->shopId;
        $userId = $this->session->userId;

        $header   = null;
        $inserted = 0;
        $updated  = 0;
        $skipped  = 0;
        $rows     = [];

        // Read CSV
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if (count(array_filter($row)) === 0) continue;

            if ($header === null) {
                $header = array_map(fn($v) => strtolower(trim($v)), $row);
                continue;
            }

            $data = array_combine($header, $row);
            if ($data === false) {
                $skipped++;
                continue;
            }

            $rows[] = $data;
        }
        fclose($handle);
        @unlink($filePath);

        // Insert data
        foreach ($rows as $data) {

            $categoryName = trim($data['product_category'] ?? '');
            if (empty($categoryName)) {
                $skipped++;
                continue;
            }

            $parentValue = trim($data['parent_pro_cat'] ?? '');
            $parentId    = 0;

            // If parent is given as name (Electronics)
            if ($parentValue !== '' && !is_numeric($parentValue)) {
                $parent = $builder->where('product_category', $parentValue)
                    ->where('sch_id', $schId)
                    ->get()
                    ->getRowArray();

                if ($parent) {
                    $parentId = $parent['prod_cat_id'];
                }
            }
            // If parent is given as ID
            elseif (is_numeric($parentValue)) {
                $parentId = (int)$parentValue;
            }

            // Check if already exists
            $existing = $builder
                ->where('product_category', $categoryName)
                ->where('parent_pro_cat', $parentId)
                ->where('sch_id', $schId)
                ->get()
                ->getRowArray();

            $saveData = [
                'product_category' => $categoryName,
                'parent_pro_cat'   => $parentId,
                'status'           => '1',
                'sch_id'           => $schId,
                'updatedBy'        => $userId,
                'updatedDtm'       => date('Y-m-d H:i:s'),
            ];

            if ($existing) {
                $builder->where('prod_cat_id', $existing['prod_cat_id'])->update($saveData);
                $updated++;
            } else {
                $saveData['createdBy']  = $userId;
                $saveData['createdDtm'] = date('Y-m-d H:i:s');
                $builder->insert($saveData);
                $inserted++;
            }
        }

        $message = "CSV Upload Successful!<br>
                Inserted: <b>{$inserted}</b><br>
                Updated: <b>{$updated}</b><br>
                Skipped: <b>{$skipped}</b>";

        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">'.$message.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to(site_url('Admin/Product_category/create'));
    }









}