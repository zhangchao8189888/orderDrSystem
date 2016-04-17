<?php
return array(
	'memu'  => array(
		// 后台菜单
        '2' => array(
            'controller' => 'dispatch',
            'resource'   => '派遣管理',
            'icon'		 =>	'envelope-alt',
            'son'        => array(
                '1'	=> array(
                    'action' 	=> 'customerList',
                    'resource'	=> '派遣单位',
                ),
                '2' =>  array(
                    'action'    =>  'employManage',
                    'resource'  =>  '派遣员工花名册',
                    'is_ref' => '_blank',
                ),
                '3' =>  array(
                    'action'    =>  'employImport',
                    'resource'  =>  '花名册导入',
                ),
            ),
        ),
        '3' => array(
            'controller' => 'employ',
            'resource'   => '档案管理',
            'icon'		 =>	'envelope-alt',
            'son'        => array(
                '1'	=> array(
                    'action' 	=> 'employList',
                    'resource'	=> '人员信息',
                ),
                '2' =>  array(
                    'action'    =>  'employManage',
                    'resource'  =>  '档案管理',
                ),
                '3' =>  array(
                    'action'    =>  'employSearch',
                    'resource'  =>  '档案查询',
                ),
            ),
        ),
        '4' => array(
            'controller' => 'social',
            'resource'   => '社保管理',
            'icon'		 =>	'envelope-alt',
            'son'        => array(
                '1'	=> array(
                    'action' 	=> 'getSocialList',
                    'resource'	=> '社保导入',
                ),
                '2'	=> array(
                    'action' 	=> 'getGjjinList',
                    'resource'	=> '公积金导入',
                ),
                '3'	=> array(
                    'action' 	=> 'showSocialList',
                    'resource'	=> '社保增员列表',
                ),
                '4'	=> array(
                    'action' 	=> 'showGjjinList',
                    'resource'	=> '公积金增员列表',
                ),
                '5'	=> array(
                    'action' 	=> 'showSocialReduceList',
                    'resource'	=> '社保减员列表',
                ),
                '6'	=> array(
                    'action' 	=> 'showGjjinReduceList',
                    'resource'	=> '公积金减员列表',
                ),
            ),
        ),
		/*'1' => array(
			'controller' => 'user',
			'resource'   => '基础信息设置',
			'icon'		 =>	'user',
			'son'		 => array(
				'1'	=> array(
					'action' 	=> 'index',
					'resource'	=> '身份类别',
				),
                '2'	=> array(
					'action' 	=> 'index',
					'resource'	=> '部门设置',
				),
                '3'	=> array(
					'action' 	=> 'index',
					'resource'	=> '工资字段设置',
				),
            ),
		),
        '2' => array(
			'controller' => 'employ',
			'resource'   => '档案管理',
			'icon'		 =>	'envelope-alt',
            'son'        => array(
                '1'	=> array(
                    'action' 	=> 'employList',
                    'resource'	=> '人员信息',
                ),
                '2' =>  array(
                    'action'    =>  'employManage',
                    'resource'  =>  '档案管理',
                ),
                '3' =>  array(
                    'action'    =>  'employSearch',
                    'resource'  =>  '档案查询',
                ),
            ),
		),
        '3' => array(
            'controller' => 'tags',
            'resource'   => '标签管理',
            'icon'		 =>	'tag',
            'son'        => array(
                '1'	=> array(
                    'action' 	=> 'index',
                    'resource'	=> '标签列表',
                ),
            ),
        ),
        '4' => array(
            'controller' => 'product',
            'resource'   => '产品管理',
            'icon'		 =>	'shopping-cart',
            'son'        => array(
                '1'	=> array(
                    'action' 	=> 'index',
                    'resource'	=> '产品类型列表',
                ),
                '2'	=> array(
                    'action' 	=> 'productList',
                    'resource'	=> '产品列表',
                ),
                '3'	=> array(
                    'action' 	=> 'publish',
                    'resource'	=> '产品发布',
                ),
            ),
        ),*/
        '5' => array(
			'controller' => 'power',
			'resource'   => '权限管理',
			'icon'		 =>	'wrench',
            'son'        => array(
                '1'	=> array(
                    'action' 	=> 'index',
                    'resource'	=> '用户表',
                ),
                '2'	=> array(
                    'action' 	=> 'authGroup',
                    'resource'	=> '权限组表',
                ),
                '3'	=> array(
                    'action' 	=> 'authRule',
                    'resource'	=> '权限规则表',
                ),
            ),
		),
		'6' => array(
			'controller' => 'manager',
			'resource'   => '管理员',
			'icon'		 =>	'user-md',
		
		),
		'7' => array(
			'controller' => 'logs',
			'resource'   => '操作日志',
			'icon'		 =>	'file',
		),

	),
	
);