<?php
/**
 * 派遣管理
 *
 */
class DispatchController extends FController
{
    private $employ_model;
    private $employInfo_model;
    private $customer_model;
    private $m_employ_model;
    private $m_employ_construct_model;
    private $m_social_model;
    private $m_gjjin_model;

    public function __construct($id, $module = null) {

        parent::__construct($id, $module);
        $this->employ_model = new Employ();
        $this->employInfo_model = new EmployInfo();
        $this->customer_model = new Customer();
        $this->m_employ_model = new MEmploy();
        $this->m_employ_construct_model = new MEmployConstruct();
        $this->m_social_model = new MSocial();
        $this->m_gjjin_model = new MGjjin();

    }
//注释test
    protected function beforeAction($action) {

        parent::beforeAction($action);

        return true;
    }

    public function actionCustomerList () {
        //分页参数
        $page = ($this->request->getParam('page') > 0) ? (int) $this->request->getParam('page') : 1;
        $page_size = ($this->request->getParam('size') > 0) ? (int) $this->request->getParam('size') : FConfig::item('config.pageSize');
        $condition_arr = array(
            'condition'=>"op_id=:op_id",
            'params' => array(
                ':op_id'=>$this->user->id,
            ),
            'limit' => $page_size,
            'offset' => ($page - 1) * $page_size ,
        );
        //分页
        $data['count'] = $this->customer_model-> count($condition_arr);
        $pages = new FPagination($data['count']);
        $pages->setPageSize($page_size);
        $pages->setCurrent($page);
        $pages->makePages();

        $data['customList'] = $this->customer_model->findAll($condition_arr);
        $data['page'] = $pages;
        $this->render('customList',$data);
    }
    function actionAddOrUpdateCustom () {
        $customer_name = $this->request->getParam('customer_name');
        $customer_principal = $this->request->getParam('customer_principal');
        $customer_principal_phone = $this->request->getParam('customer_principal_phone');
        $customer_address = $this->request->getParam('customer_address');
        $canbaojin = floatval($this->request->getParam('canbaojin'));
        $service_fee = floatval($this->request->getParam('service_fee'));
        $remark = $this->request->getParam('remark');
        $id = $this->request->getParam('id');
        $date_rang_json = json_encode($this->request->getParam('date_rang_json'));
        $condition_arr = array(
            'customer_name' => $customer_name,
            'customer_principal' => $customer_principal,
            'customer_principal_phone' => $customer_principal_phone,
            'customer_address' => $customer_address,
            'canbaojin' => $canbaojin,
            'service_fee' => $service_fee,
            'date_rang_json' => $date_rang_json,
            'op_id' => $this->user->id,
            'remark' => $remark,
        );
        if (!empty($id)) {
            $res = $this->customer_model->updateByPk($id,$condition_arr);
        } else {

            $this->customer_model->attributes = $condition_arr;
            $res = $this->customer_model->save();
        }
        if($res){
            $response['status'] = 100000;
            $response['content'] = '保存成功！';
        }else{

            $response['status'] = 100001;
            $response['content'] = '确认失败！';
        }
        Yii::app()->end(FHelper::json($response['content'],$response['status']));
    }
    function actionGetCustom () {
        $id = $this->request->getParam('id');
        $res = $this->customer_model->findByPk($id);
        if ($res) {
            $data['customer_name'] = $res->customer_name;
            $data['customer_principal'] = $res->customer_principal;
            $data['customer_principal_phone'] = $res->customer_principal_phone;
            $data['customer_address'] = $res->customer_address;
            $data['canbaojin'] = $res->canbaojin;
            $data['service_fee'] = $res->service_fee;
            $data['date_rang_json'] = json_decode($res->date_rang_json);
            $data['remark'] = $res->remark;
            $response['status'] = 100000;
            $response['content'] = $data;
        }else {
            $response['status'] = 100001;
            $response['content'] = 'error';
        }
        Yii::app()->end(FHelper::json($response['content'],$response['status']));
    }
    function actionEmployManage () {
        $this->layout = 'main_no_menu';
        $condition_arr = array(
            'condition'=>"op_id=:op_id",
            'params' => array(
                ':op_id'=>$this->user->id,
            ),
        );
        $res = $this->customer_model->findAll($condition_arr);
        $i = 0;
        foreach ($res as $row) {
            $data['custom_list'][$i]['id'] = $row->id;
            $data['custom_list'][$i]['name'] = $row->customer_name;
            $i++;
        }
        $this->render('employList',$data);
    }
    function actionGetDepartmentTreeJson() {
        $id = $this->request->getParam('id');
        if (empty($id)) {
            $treeJson['data']['company_id'] = -1;
            $treeJson['data']['name'] = '派遣员工花名册';
            $treeJson['data']['pid'] = 0;
            $treeJson['data']['isParent'] = 'true';
            $treeJson['data']['id'] = -1;
        } elseif ($id == -1) {
            $condition_arr = array(
                'condition'=>"op_id=:op_id",
                'params' => array(
                    ':op_id'=>$this->user->id,
                ),
            );
            $res = $this->customer_model->findAll($condition_arr);
            $i = 0;
            foreach ($res as $row) {
                $treeJson['data'][$i]['id'] = $row->id;
                $treeJson['data'][$i]['name'] = $row->customer_name;
                $treeJson['data'][$i]['pid'] = -1;
                $treeJson['data'][$i]['isParent'] = 'false';
                $i++;
            }
        }

       /* $response['status'] = 100000;
        $response['content'] = $treeJson;*/
        echo json_encode($treeJson);
        exit;
    }
    function actionSaveOrUpdateEmployList () {
        $updateData = $this->request->getParam('data');
        $current_company_id = $this->request->getParam('current_company_id');
        if (!empty($current_company_id)) {
            $c = new EMongoCriteria;
            $c->e_company_id = $current_company_id;
            $employ_construct_po = $this->m_employ_construct_model->find($c);
        }
        $error_list = array();
        $success_list = array();
        foreach ($updateData as $row) {

            if ($row['row_id'] != 'null' && !empty($row['row_id'])) {
                $this->m_employ_model = new MEmploy('update');
                $this->m_employ_model->_id = new MongoId($row['row_id']);
                $this->m_employ_model->e_num = $row[$employ_construct_po->e_num_position];
                $this->m_employ_model->e_hetong_num = intval($row[$employ_construct_po->e_hetong_num_position]);
                $this->m_employ_model->e_name = $row[$employ_construct_po->e_name_position];
                $this->m_employ_model->e_type = $row[$employ_construct_po->e_type_position];
                $this->m_employ_model->e_company_id = $current_company_id;
                $this->m_employ_model->emp_info_row = $row;
                $this->m_employ_model->setIsNewRecord(false);
                if($this->m_employ_model->validate()){
                    $this->m_employ_model->update();
                }
            }else {

                unset($row['row_id']);
                $this->m_employ_model = new MEmploy('insert');
                $this->m_employ_model->e_num = $row[$employ_construct_po->e_num_position];
                $this->m_employ_model->e_hetong_num = intval($row[$employ_construct_po->e_hetong_num_position]);
                $this->m_employ_model->e_name = $row[$employ_construct_po->e_name_position];
                $this->m_employ_model->e_type = $row[$employ_construct_po->e_type_position];
                $this->m_employ_model->e_company_id = $current_company_id;
                $this->m_employ_model->emp_info_row = $row;
                $this->m_employ_model->save();
            }

            $error = $this->m_employ_model->getErrors();
            if (!empty($error)) {
                $error_list[] = $error;
            }
        }

        $response['content']['success_list'] = $success_list;
        if (!empty($error_list)) {
            $response['status'] = 100001;
            $response['content']['error_list'] = $error_list;
            Yii::app()->end(FHelper::json($response['content'],$response['status']));
        }
        $response['status'] = 100000;
        $response['content']['message'] = '添加成功！';
        Yii::app()->end(FHelper::json($response['content'],$response['status']));
    }
    function actionGetEmployListBySearch() {
        $e_company_id = $this->request->getParam('e_company_id');
        $e_name = $this->request->getParam('e_name');
        $e_num = $this->request->getParam('e_num');

        $c = new EMongoCriteria;
        $c->e_company_id = $e_company_id;
        $construct = $this->m_employ_construct_model->find($c);//print_r($construct);exit;
        $data['head'] = $construct->head_row;

        $eColumns = array();
        foreach ($construct->head_row as $key => $val) {
            $eColumns[] = array('data'=> $key);
        }

        $c = new EMongoCriteria;
        if (!empty($e_name)) {
            $c->e_name = $e_name;
        }
        if (!empty($e_num)) {
            $c->e_num = $e_num;
        }

        $c->sort('e_hetong_num',EMongoCriteria::SORT_ASC);
        $res = $this->m_employ_model->findAll($c);
        $eList = array();
        foreach ($res as $row) {
            $row->emp_info_row['row_id'] = $row->_id->{'$id'};
            $row->emp_info_row['e_num'] = $row->e_num;
            $row->emp_info_row['e_name'] = $row->e_name;
            $row->emp_info_row['e_type'] = $row->e_type;
            $eList[] = $row->emp_info_row;

        }
        $data['data_list'] = $eList;
        $data['columns'] = $eColumns;
        echo json_encode($data);
        exit;
    }
    function actionGetEmployList () {
        $id = $this->request->getParam('id');
        $c = new EMongoCriteria;
        $c->e_company_id = $id;
        $construct = $this->m_employ_construct_model->find($c);//print_r($construct);exit;
        if (empty($construct)) {
            $response['status'] = 100001;
            $response['content'] = '内容为空！';
            Yii::app()->end(FHelper::json($response['content'],$response['status']));
        }
        $data['head'] = $construct->head_row;

        $eColumns = array();
        foreach ($construct->head_row as $key => $val) {
            $eColumns[] = array('data'=> $key);
        }

        $c->sort('e_hetong_num',EMongoCriteria::SORT_ASC);
        $res = $this->m_employ_model->findAll($c);
        $eList = array();
        foreach ($res as $row) {
            $row->emp_info_row['row_id'] = $row->_id->{'$id'};
            $row->emp_info_row['e_num'] = $row->e_num;
            $row->emp_info_row['e_name'] = $row->e_name;
            $row->emp_info_row['e_type'] = $row->e_type;
            $row->emp_info_row['row_id'] = $row->_id->{'$id'};
            $eList[] = $row->emp_info_row;

        }

        $data['data_list'] = $eList;
        $data['columns'] = $eColumns;
        echo json_encode($data);
        exit;
    }
    public function actionGetEmployById () {

        $id = $this->request->getParam('id');
        $c = new EMongoCriteria;
        $c->_id = new MongoId($id);
        $employ_po = $this->m_employ_model->find($c);
    }
    private function sumAge($birthday) {
        $age = date('Y', time()) - date('Y', strtotime($birthday)) - 1;
        return $age;
    }
    public function actionDelEmployList () {
        $ids = $this->request->getParam('ids');
        $bError = false;
        foreach ($ids as $_id)  {
            $res = $this->m_employ_model->deleteByPk(new MongoId($_id));
            /*if ($bError) {

            }*/
        }
        $response['status'] = 100000;
        $response['content'] = '删除成功！';
        Yii::app()->end(FHelper::json($response['content'],$response['status']));

    }
    public function actionEmployImport () {
        $this->render('employImport');
    }
    public function actionFileImport() {
        set_time_limit(1800);
        $errorMsg = "";
        //var_dump($_FILES);
        $fileArray = explode(".", $_FILES['file']['name']);
        //var_dump($fileArray);
        if (count($fileArray) != 2) {
            $data['error'] = '文件名格式 不正确';
            $this->render('employImport',$data);
        } else if (!($fileArray[1] == 'xls'||$fileArray[1] == 'xlsx')) {
            $data['error'] = '文件类型不正确，必须是xls类型';
            $this->render('employImport',$data);
        }
        if ($_FILES['file']['error'] != 0) {
            $error = $_FILES['file']['error'];
            switch ($error) {
                case 1:
                    $errorMsg = '1,上传的文件超过了php.ini中  upload_max_filesize选项限制的值.';
                    break;
                case 2:
                    $errorMsg = '2,上传文件的大小超过了HTML表单中MAX_FILE_SIZE  选项指定的大小';
                    break;
                case 3:
                    $errorMsg = '3,文件只有部分被上传';
                    break;
                case 4:
                    $errorMsg = '4,文件没有被上传';
                    break;
                case 6:
                    $errorMsg = '找不到临文件夹';
                    break;
                case 7:
                    $errorMsg = '文件写入失败';
                    break;
            }
        }
        if ($errorMsg != "") {
            $data['error'] = $errorMsg;
            $this->render('employImport',$data);
        }
        /*$err=Read_Excel_File($_FILES['file']['tmp_name'],$return);
        if($err!=0){
        $this->objForm->setFormData("error",$err);
        }*/
        $path = $_FILES['file']['tmp_name'];
        Yii::$enableIncludePath = false;
        Yii::import('application.extensions.PHPExcel.PHPExcel', 1);
        $_ReadExcel = new PHPExcel_Reader_Excel2007();
        if (!$_ReadExcel->canRead($path)) $_ReadExcel = new PHPExcel_Reader_Excel5();
        //读取Excel文件
        $_phpExcel = $_ReadExcel->load($path);
        //获取工作表的数目
        $_sheetCount = $_phpExcel->getSheetCount();
        $return = array();
        $_excelData = array();

        //循环工作表
        //for($_s = 0;$_s<$_sheetCount;$_s++) {
        for ($_s = 0; $_s < 2; $_s++) {
            //选择工作表
            $_currentSheet = $_phpExcel->getSheet($_s);
            //取得一共有多少列
            $_allColumn = $_currentSheet->getHighestColumn();
            //取得一共有多少行
            $_allRow = $_currentSheet->getHighestRow();
            for ($_r = 1; $_r <= $_allRow; $_r++) {
                $cell_obj = array();
                for ($_currentColumn = 'A'; $_currentColumn <= $_allColumn; $_currentColumn++) {
                    $address = $_currentColumn . $_r;
                    $val = $_currentSheet->getCell($address)->getValue();
                    $cell_obj[] = $val;
                }
                $return[] =$cell_obj;
            }
        }
        $excelTool = new FExcelToHanTable(1);
        $excelTool->getData($return);
        $data['list'] = json_encode(array_merge(array(0=>$excelTool->head_row),$excelTool->table_data));
        //print_r($data['list']);exit;
        $data['head_width'] = json_encode($excelTool->head_width_arr);
        $data['head_height'] = json_encode($excelTool->head_height_arr);
        $condition_arr = array(
            'condition'=>"op_id=:op_id",
            'params' => array(
                ':op_id'=>$this->user->id,
            ),
        );
        $res = $this->customer_model->findAll($condition_arr);
        $i = 0;
        foreach ($res as $row) {
            $data['custom_list'][$i]['id'] = $row->id;
            $data['custom_list'][$i]['name'] = $row->customer_name;
            $i++;
        }
        $this->render('employImportToView',$data);
    }
    public function actionSaveEmployList() {
        $em_list = $this->request->getParam('data');
        $e_num = $this->request->getParam('e_num')-1;
        $e_hetong_num = $this->request->getParam('e_hetong_num')-1;
        $e_type = $this->request->getParam('e_type')-1;
        $e_name = $this->request->getParam('e_name')-1;
        $custom_id = $this->request->getParam('custom_id');
        //$save_list = array();
        if ($e_num < 0) {
            $response['status'] = 100002;
            $response['content'] = '身份证列无法找到！';
            Yii::app()->end(FHelper::json($response['content'],$response['status']));
        }
        if ($e_type < 0) {
            $response['status'] = 100002;
            $response['content'] = '身份类别列无法找到！';
            Yii::app()->end(FHelper::json($response['content'],$response['status']));
        }
        if ($e_name < 0) {
            $response['status'] = 100002;
            $response['content'] = '姓名列无法找到！';
            Yii::app()->end(FHelper::json($response['content'],$response['status']));
        }
        if ($e_hetong_num < 0) {
            $response['status'] = 100002;
            $response['content'] = '合同号列无法找到！';
            Yii::app()->end(FHelper::json($response['content'],$response['status']));
        }
        $error_list = array();
        $success_list = array();
        //$this->m_employ_construct_model = new MEmployConstruct();
        foreach ($em_list as $key => $row) {
            if ($key == 0) {
                foreach($row as $k => $v)
                {
                    if ($v == 'null') {
                        $row[$k] = '';
                    }
                }
                $hash_str = hash('md5',implode(",", $row));
                $res = $this->m_employ_construct_model->findByAttributes(array("e_company_id" => $custom_id));

                if (!empty($res)) {
                    if ($hash_str != $res->head_hash) {
                        $response['status'] = 100002;
                        $response['content'] = '该单位导入表头与已存在表头不一致！';
                        Yii::app()->end(FHelper::json($response['content'],$response['status']));
                    }
                    $this->m_employ_construct_model->_id = new MongoId($res->_id->{'$id'});
                    $this->m_employ_construct_model->e_company_id = $custom_id;
                    $this->m_employ_construct_model->e_num_position = $e_num;
                    $this->m_employ_construct_model->e_hetong_num_position = $e_hetong_num;
                    $this->m_employ_construct_model->e_name_position = $e_name;
                    $this->m_employ_construct_model->e_type_position = $e_type;
                    $this->m_employ_construct_model->head_row = $row;
                    $this->m_employ_construct_model->head_hash = $hash_str;
                    $this->m_employ_construct_model->setIsNewRecord(false);
                    $this->m_employ_construct_model->update();
                } else {

                    $this->m_employ_construct_model->e_company_id = $custom_id;
                    $this->m_employ_construct_model->e_num_position = $e_num;
                    $this->m_employ_construct_model->e_hetong_num_position = $e_hetong_num;
                    $this->m_employ_construct_model->e_name_position = $e_name;
                    $this->m_employ_construct_model->e_type_position = $e_type;
                    $this->m_employ_construct_model->head_row = $row;
                    $this->m_employ_construct_model->head_hash = $hash_str;
                    $this->m_employ_construct_model->save();
                    $error = $this->m_employ_construct_model->getErrors();
                    if (!empty($error)){
                        $response['status'] = 100002;
                        $response['content'] = $error->message;
                        Yii::app()->end(FHelper::json($response['content'],$response['status']));
                    }
                }

            } else {
                $this->m_employ_model = new MEmploy();
                $this->m_employ_model->e_num = $row[$e_num];
                $this->m_employ_model->e_hetong_num = intval($row[$e_hetong_num]);
                $this->m_employ_model->e_name = $row[$e_name];
                $this->m_employ_model->e_type = $row[$e_type];
                $this->m_employ_model->e_company_id = $custom_id;
                $this->m_employ_model->emp_info_row = $row;
                $this->m_employ_model->save();
                $error = $this->m_employ_model->getErrors();
                if (!empty($error)){
                    $error_list[] = array(
                        'key' => $key,
                        'message' =>$error
                    );
                } else {
                    $success_list[] = $key;
                }
            }


        }
        if (!empty($error_list)) {
            $response['status'] = 100001;
            $response['content']['error_list'] = $error_list;
            $response['content']['success_list'] = $success_list;
            Yii::app()->end(FHelper::json($response['content'],$response['status']));
        }
        $response['status'] = 100000;
        $response['content'] = '添加成功！';
        Yii::app()->end(FHelper::json($response['content'],$response['status']));
    }
    public function actionGetEmploySocialById() {

        $row_id = $this->request->getParam('row_id');
        $c = new EMongoCriteria;
        $c->_id = new MongoId($row_id);
        $employ_po = $this->m_employ_model->find($c);
        if (!empty($employ_po)) {

            $c = new EMongoCriteria;
            $c->e_num = $employ_po->e_num;
            $social_po = $this->m_social_model->find($c);
            if (!empty($social_po)) {
                $response['content']['social'] = $social_po;
            } else {
                $response['content']['social'] = 'empty';
            }
            $gjjin_po = $this->m_gjjin_model->find($c);
            if (!empty($gjjin_po)) {
                $response['content']['gjjin'] = $gjjin_po;
            } else {
                $response['content']['gjjin'] = 'empty';
            }
        }

        $response['status'] = 100000;
        Yii::app()->end(FHelper::json($response['content'],$response['status']));

    }
}