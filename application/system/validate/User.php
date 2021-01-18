<?php

namespace app\system\validate;

use think\Validate;

class User extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'title|标题' => 'require',
        'volume|阅读量' => 'number',
        'id|编号' => 'number|require'
    ];

    protected $scene = [
        'increase' => ['volume', 'title'],
        'change' => ['id', 'volume'],
        'delete' => ['id'],
        'select' => ['volume']
    ];
    protected $message = [];
}
