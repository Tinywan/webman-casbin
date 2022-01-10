<?php

/**
 * @desc Real Policy Model
 * @author Tinywan(ShaoBo Wan)
 * @date 2021/8/28 10:37
 */

declare(strict_types=1);

namespace Tinywan\Casbin\model;

use think\Model;
use think\contract\Arrayable;

/**
 * CasbinRule Model
 */
class RuleModel extends Model implements Arrayable
{
    /**
     * 设置字段信息
     *
     * @var array
     */
    protected $schema = [
        'id'    => 'int',
        'ptype' => 'string',
        'v0'    => 'string',
        'v1'    => 'string',
        'v2'    => 'string',
        'v3'    => 'string',
        'v4'    => 'string',
        'v5'    => 'string',
    ];
    /**
     * 架构函数
     * @access public
     * @param array $data 数据
     */
    public function __construct($data = [])
    {
        $this->connection = $this->config('database.connection') ?: '';
        $this->table = $this->config('database.rules_table');
        $this->name = $this->config('database.rules_name');
        parent::__construct($data);
    }

    /**
     * Gets config value by key.
     *
     * @param string $key
     * @param string $default
     *
     * @return mixed
     */
    protected function config(string $key = null, $default = null)
    {
        $driver = config('permission.default');
        return config('permission.enforcers.' . $driver . '.' . $key, $default);
    }
}
