<?php
/**
 * @desc 描述
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/1/10 21:33
 */

declare(strict_types=1);

namespace Tinywan\Casbin;


use Casbin\Enforcer;
use Casbin\Exceptions\CasbinException;
use Casbin\Model\Model;
use support\Container;
use Workerman\Timer;
use Workerman\Worker;
use Webman\Bootstrap;

/**
 * @see \Casbin\Enforcer
 * @mixin \Casbin\Enforcer
 * @method static enforce(mixed ...$rvals) 权限检查，输入参数通常是(sub, obj, act)
 * @method static bool addPolicy(mixed ...$params) 当前策略添加授权规则
 * @method static bool addPolicies(mixed ...$params) 当前策略添加授权规则
 * @method static bool hasPolicy(mixed ...$params) 确定是否存在授权规则
 * @method static bool removePolicy(mixed ...$params) 当前策略移除授权规则
 * @method static getAllRoles() 获取所有角色
 * @method static getPolicy() 获取所有的角色的授权规则
 * @method static getRolesForUser(string $name, string ...$domain) 获取某个用户的所有角色
 * @method static getUsersForRole(string $name, string ...$domain) 获取某个角色的所有用户
 * @method static hasRoleForUser(string $name, string $role, string ...$domain) 决定用户是否拥有某个角色
 * @method static addRoleForUser(string $user, string $role, string ...$domain) 给用户添加角色
 * @method static addPermissionForUser(string $user, string ...$permission) 赋予权限给某个用户或角色
 * @method static deleteRoleForUser(string $user, string $role, string $domain) 删除用户的角色
 * @method static deleteRolesForUser(string $user, string ...$domain) 删除某个用户的所有角色
 * @method static deleteRole(string $role) 删除单个角色
 * @method static deletePermission(string ...$permission) 删除某个权限
 * @method static deletePermissionsForUser(string $user, string ...$permission) 删除某个用户或角色的权限
 * @method static getPermissionsForUser(string $user) 获取用户或角色的所有权限
 * @method static hasPermissionForUser(string $user, string ...$permission) 决定某个用户是否拥有某个权限
 * @method static getImplicitRolesForUser(string $name, string ...$domain) 获取用户具有的隐式角色
 * @method static getImplicitPermissionsForUser(string $username, string ...$domain) 获取用户具有的隐式权限
 * @method static addFunction(string $name, \Closure $func) 添加一个自定义函数
 */
class Permission implements Bootstrap
{
    /**
     * @var $_manager
     */
    protected static $_manager = null;

    /**
     * Permission constructor.
     * Creates an Permission via file or DB.
     * File:
     * $e = new Permission("path/to/basic_model.conf", "path/to/basic_policy.csv")
     * MySQL DB:
     * $a = DatabaseAdapter::newAdapter([
     *      'type'     => 'mysql', // mysql,pgsql,sqlite,sqlsrv
     *      'hostname' => '127.0.0.1',
     *      'database' => 'test',
     *      'username' => 'root',
     *      'password' => '123456',
     *      'hostport' => '3306',
     *  ]);
     * $e = new Permission("path/to/basic_model.conf", $a).
     *
     * @param mixed ...$params
     *
     * @throws CasbinException
     */

    /**
     * @param Worker $worker
     * @return mixed|void
     * @throws CasbinException
     * @author Tinywan(ShaoBo Wan)
     */
    public static function start($worker)
    {
        $default = config('permission.default');
        if (empty($default)) {
            throw new \RuntimeException("Casbin permission.php config not found.");
        }
        $config = config('permission.enforcers.'.$default);
        $configType = $config['model']['config_type'];
        $model = new Model();
        if ('file' == $configType) {
            $model->loadModel($config['model']['config_file_path']);
        } elseif ('text' == $$configType) {
            $model->loadModel($config['model']['config_text']);
        }
        if (is_null(static::$_manager)) {
            static::$_manager = new Enforcer($model, Container::get($config['adapter']),false);
        }
        // 多进程需要使用watcher，这里使用定时器定时刷新策略
        Timer::add($config['policy_refresh_time_interval'], function () {
            static::$_manager->loadPolicy();
        });
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @author Tinywan(ShaoBo Wan)
     */
    public static function __callStatic($name, $arguments)
    {
        return static::$_manager->{$name}(...$arguments);
    }
}