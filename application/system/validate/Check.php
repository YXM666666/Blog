<?php


namespace app\system\validate;

use think\Validate;

class Check extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'name|类别名称' => 'require',
        'id|编号' => 'number|require'
    ];

    protected $scene = [
        'increase' => ['name'],
        'change' => ['id'],
        'delete' => ['id'],
    ];
    protected $message = [];
}
