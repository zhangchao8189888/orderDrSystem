<?php
/**
 * 用户列表
 *
 */
class EmployController extends FController
{
    private $employ_model;
    private $employInfo_model;

    public function __construct($id, $module = null) {

        parent::__construct($id, $module);
        $this->employ_model = new Employ();
        $this->employInfo_model = new EmployInfo();

    }
//注释test
    protected function beforeAction($action) {

        parent::beforeAction($action);

        return true;
    }
    public function actionIndex(){
        $model = new MongoTest();
        $model->addInfo();
        $res = $model->findAll();
        print_r($res);
    }
    public function actionEmployList () {
        //分页参数
        $page = ($this->request->getParam('page') > 0) ? (int) $this->request->getParam('page') : 1;
        $page_size = ($this->request->getParam('size') > 0) ? (int) $this->request->getParam('size') : FConfig::item('config.pageSize');

        $condition_arr = array(
            'condition'=>"e_company_id= 8",
            'limit' => $page_size,
            'offset' => ($page - 1) * $page_size ,
        );
        //分页
        $data['count'] = $this->employ_model-> count($condition_arr);
        $pages = new FPagination($data['count']);
        $pages->setPageSize($page_size);
        $pages->setCurrent($page);
        $pages->makePages();
        $data['employ_status'] = FConfig::item("config.employ_status");

        $data['empList'] = $this->employ_model->findAll($condition_arr);
        $data['page'] = $pages;
        $this->render('employList',$data);
    }
    public function actionGetEmployInfo () {
        $this->layout = 'main_no_menu';
        $id = $this->request->getParam('eid');
        $res = $this->employ_model->findByPk($id);
        $data['employInfo'] = $res;
        $data['employ_type'] = FConfig::item("config.employ_type");

        $this->render('employInfo',$data);
    }
    public function actionGetEmployByIds () {
        $this->layout = 'main_no_menu';
        $ids = $this->request->getParam('ids');
        //$type = $this->request->getParam('type');
        $type = 3;
        $ids = explode("e",$ids);
        foreach($ids as $val){
            $res = $this->employ_model->findByPk($val);
            if ($res) {
                $data['empList'][] = $res;
            }
        }

        $data['employ_status'] = FConfig::item("config.employ_status");
        $this->render('employOutWork',$data);


    }

}