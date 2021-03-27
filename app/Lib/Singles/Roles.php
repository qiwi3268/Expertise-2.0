<?php

declare(strict_types=1);

namespace App\Lib\Singles;

use LogicException;

use App\Repositories\UsersData\RoleRepository;
use App\Lib\Singles\Arrays\HashArray;
use stdClass;


/**
 * Обеспечивает работу с ролями пользователей
 *
 * Гарантирует, что вся работа ведется с существующими ролям
 */
final class Roles
{

    /**
     * system_value всех ролей из БД
     */
    private static HashArray $dataBaseRoles;

    private HashArray $roles;


    /**
     * Конструктор класса
     *
     * @param string[] $roles роли в соответствии с БД
     * @throws LogicException
     */
    public function __construct(array $roles)
    {
        if (empty($roles)) {
            throw new LogicException('Входной массив ролей не может быть пустым');
        }

        if (!isset(self::$dataBaseRoles)) {

            self::$dataBaseRoles = HashArray::createByCallback(
                (new RoleRepository)->getAll(), fn (stdClass $obj) => $obj->system_value
            );
        }

        $this->roles = new HashArray();

        foreach ($roles as $role) {

            $this->roles->add($this->verifiedRole($role));
        }
    }


    /**
     * Проверяет существование роли
     *
     * @param string $role
     * @return bool
     */
    public function has(string $role): bool
    {
        return $this->roles->has($this->verifiedRole($role));
    }


    /**
     * Возвращает верифицированную с БД роль
     *
     * @param string $role
     * @return string
     * @throws LogicException
     */
    private function verifiedRole(string $role): string
    {
        if (self::$dataBaseRoles->missing($role)) {

            throw new LogicException("Роль: '{$role}' не существует в БД");
        }
        return $role;
    }


    /**
     * Имеет ли текущий экземпляр роль из другого экземпляра
     *
     * @param Roles $obj
     * @return bool
     */
    public function hasIntersectWithOtherInstance(self $obj): bool
    {
        // Обращение к приватному свойству другого экземпляра
        foreach ($obj->roles->getElements() as $role) {

            if ($this->has($role)) {

                return true;
            }
        }
        return false;
    }


    /**
     * Предназначен для преобразования к строке
     *
     * @return string
     */
    public function __toString(): string
    {
        return implode(', ', $this->roles->getElements());
    }
}