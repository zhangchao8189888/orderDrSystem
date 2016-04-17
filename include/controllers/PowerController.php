<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/9
 * Time: 14:49
 * 权限管理
 */
class PowerController extends FController
{
    private $authGroup_model;
    private $admin_model;
    private $authRule_model;

    public function __construct($id, $module = null) {

        parent::__construct($id, $module);
        $this->authGroup_model = new AuthGroup();
        $this->admin_model = new Admin();
        $this->authRule_model = new AuthRule();

    }

    protected function beforeAction($action) {

        parent::beforeAction($action);

        return true;
    }

    // 用户表
    public function actionIndex() {
        $data = array();

        //分页参数
        $page = ($this->request->getParam('page') > 0) ? (int) $this->request->getParam('page') : 1;
        $page_size = ($this->request->getParam('size') > 0) ? (int) $this->request->getParam('size') : FConfig::item('config.pageSize');

        $condition_arr = array(
            'limit' => $page_size,
            'offset' => ($page - 1) * $page_size ,
        );
        //分页
        $data['count'] = $this->admin_model-> count($condition_arr);
        $pages = new FPagination($data['count']);
        $pages->setPageSize($page_size);
        $pages->setCurrent($page);
        $pages->makePages();

        $adminList = $this->admin_model->findAll($condition_arr);
        $data['adminList'] = $adminList;
        $data['page'] = $pages;

        $this->render('index', $data);
    }

    // 权限组表
    public function actionAuthGroup() {
        $data = array();

        //分页参数
        $page = ($this->request->getParam('page') > 0) ? (int) $this->request->getParam('page') : 1;
        $page_size = ($this->request->getParam('size') > 0) ? (int) $this->request->getParam('size') : FConfig::item('config.pageSize');

        $condition_arr = array(
            'limit' => $page_size,
            'offset' => ($page - 1) * $page_size ,
        );
        //分页
        $data['count'] = $this->authGroup_model-> count($condition_arr);
        $pages = new FPagination($data['count']);
        $pages->setPageSize($page_size);
        $pages->setCurrent($page);
        $pages->makePages();

        $authGroupList = $this->authGroup_model->findAll($condition_arr);
        $data['authGroupList'] = $authGroupList;
        $data['menus'] = FConfig::item('admin.memu');
//        $data['menus'] = json_encode(FConfig::item('admin.memu'));
        $data['page'] = $pages;
        $this->render('authGroup', $data);
    }

    public function actionGroupAdd () {
        $name = $this->request->getParam('name');
        $status = $this->request->getParam('status');
        $rule = $this->request->getParam('rule');
        $rules = implode(',', $rule);
        $describe = $this->request->getParam('describe');
        $condition_arr = array(
            'title' => $name,
            'status' => $status,
            'rules' => $rules,
            'describe' => $describe
        );
        $this->authGroup_model->attributes = $condition_arr;
        $res = $this->authGroup_model->save();
        if($res){
            $response['status'] = 100000;
            $response['content'] = '保存成功！';
        }else{
            $response['status'] = 100001;
            $response['content'] = '确认失败！';
        }
        Yii::app()->end(FHelper::json($response['content'],$response['status']));
    }

    public function actionGetGroup () {
        $data = array();
        $id = $this->request->getParam('id');
        $groupRecord = $this->authGroup_model->findByPk($id);
        if ($groupRecord) {

            $data['title'] = $groupRecord->title;
            $data['status'] = $groupRecord->status;
            $data['rules'] = explode(',', $groupRecord->rules);
            $data['describe'] = $groupRecord->describe;
            $response['status'] = 100000;
            $response['content'] = $data;
        }else {
            $response['status'] = 100001;
            $response['content'] = 'error';
        }
        Yii::app()->end(FHelper::json($response['content'],$response['status']));
    }

    public function actionUpdateGroup () {
        $id = $this->request->getParam('id');
        $name = $this->request->getParam('name');
        $status = $this->request->getParam('status');
        $rule = $this->request->getParam('rule');
        $rules = implode(',', $rule);
        $describe = $this->request->getParam('describe');
        $condition_arr = array(
            'title' => $name,
            'status' => $status,
            'rules' => $rules,
            'describe' => $describe
        );
        //$this->authGroup_model->attributes = $condition_arr;
        $res = $this->authGroup_model->updateByPk($id,$condition_arr);
        if($res){
            $response['status'] = 100000;
            $response['content'] = '修改成功！';
        }else{
            $response['status'] = 100001;
            $response['content'] = '修改失败！';
        }
        Yii::app()->end(FHelper::json($response['content'],$response['status']));
    }

    public function actionDelGroup () {
        $id = $this->request->getParam('id');
        $res = $this->authGroup_model->deleteByPk($id);
        if($res){
            $response['status'] = 100000;
            $response['content'] = '删除成功！';
        }else{
            $response['status'] = 100001;
            $response['content'] = '删除失败！';
        }
        Yii::app()->end(FHelper::json($response['content'],$response['status']));
    }

    // 规则表
    public function actionAuthRule() {
        $data = array();

        //分页参数
        $page = ($this->request->getParam('page') > 0) ? (int) $this->request->getParam('page') : 1;
        $page_size = ($this->request->getParam('size') > 0) ? (int) $this->request->getParam('size') : FConfig::item('config.pageSize');

        $condition_arr = array(
            'limit' => $page_size,
            'offset' => ($page - 1) * $page_size ,
        );
        //分页
        $data['count'] = $this->authRule_model-> count($condition_arr);
        $pages = new FPagination($data['count']);
        $pages->setPageSize($page_size);
        $pages->setCurrent($page);
        $pages->makePages();

        $authRuleList = $this->authRule_model->findAll($condition_arr);
        $data['authRuleList'] = $authRuleList;
        $data['page'] = $pages;

        $this->render('authRule', $data);
    }
}