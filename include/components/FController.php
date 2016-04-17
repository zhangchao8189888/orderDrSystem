<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class FController extends CController
{
    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    //public $layout='//layouts/column1';
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu=array();
    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs=array();

    /**
     * User info
     *
     * @var JUser
     */
    public $user;

    public $userInfo;
    /**
     * @var CHttpRequest
     */
    public $request;

    /**
     * @var string;
     */
    public $pageTitle;
    /**
     * @var string
     */
    public $pagekeywords;
    /**
     * @var string;
     */
    public $pageDescription;
    /**
     * @var string;
     */
    public $auth_list;
    /**
     * @var string;
     */
    protected $_controller;
    /**
     * @var string;
     */
    protected $_action;

    public $user_menu_list = array();
    /**
     * @var string
     */
    public $returnurl;

    private $access = array('login','getLogin','setcookie','error');

    public function __construct($id, $module = null) {

        parent::__construct($id, $module);
        $this -> auth_list  =  FConfig::item('admin.memu');
        $this -> request      = Yii::app()->getRequest();
    }
    /*
    *判断当前用户是否登录
    */
    public function is_login(){



        return isset($this->userInfo['id']) && $this->userInfo['id'] ? true : false ;
    }
    protected function beforeAction($action) {
        $this -> user = Yii::app()-> user ->loadUser();
        if (!$this -> user&& !in_array($action -> getId(), $this -> access))
        {
            Yii::app()->getRequest() ->redirect(FF_DOMAIN."/login");
        } else {
            //获取用户权限
            // $mc_menu_key = md5('mc_menu_key' . $this -> user -> id);
            // $this -> user_menu_list = Yii::app() ->cache ->get($mc_menu_key);
            // Yii::app() -> cache ->set($mc_menu_key , $this -> user_menu_list , 600);

            //用户-> 用户-组关系 -> 组 -> 组-权限
            if(!empty($this -> user -> admin_user_group)){
                $user_auth_list = explode(',',$this -> user -> admin_user_group -> group -> rules);
            }
            $allow_controller = array('site','power','login','user','social','likingpic');
            if(!empty($user_auth_list)){
                foreach ($user_auth_list as $k => $v) {
                    //菜单
                    $this -> user_menu_list[] =   $v;

                    $allow_controller[] = $this -> auth_list[$v]['controller'];
                }
            }

            //判断是否有访问权限
            $this -> _controller = $action -> getController() ->getId();


            if(!in_array( $this -> _controller,$allow_controller))
            {
                Yii::app()->getRequest() ->redirect(FF_DOMAIN.'/site');
            }

        }
        $this -> _action =  $action ->getId();

        return true;
    }
    protected function getUserinfo($uid) {
        $userModel = new User();
        $attr = array(
            'condition'=>"id=:id",
            'params' => array(':id'=>$uid,),

        );
        $user = $userModel->find($attr);
        $account = $user->getAttributes();
        return $account;
    }
}