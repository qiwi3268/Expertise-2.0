<?php

declare(strict_types=1);

namespace App\Models\UsersData;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Collection;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\AppModel;
use App\Models\Docs\DocApplication;
use App\Models\ApplicantAccessGroup;
use App\Lib\ValueObjects\Fio;
use App\Models\Traits\Mutators\HasFio;
use App\Lib\Singles\Roles;


/**
 * Пользователь системы
 *
 * @property Fio fio
 * @property Collection roles
 */
final class User extends AppModel implements AuthenticatableContract, AuthorizableContract
{
    use HasFio, Authenticatable, Authorizable;


    /**
     * Отношение N к N
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }


    /**
     * Возвращает объект ролей пользователя
     *
     * @return Roles
     */
    public function getRoles(): Roles
    {
        $collection = $this->roles()->select(['system_value'])->toBase()->get();

        $roles = [];

        foreach ($collection as $obj) {

            $roles[] = $obj->system_value;
        }
        return new Roles($roles);
    }




    /**
     * Отношение 1 к N
     *
     * @return HasMany
     */
    public function docApplications(): HasMany
    {
        return $this->hasMany(DocApplication::class);
    }


    /**
     * Отношение N к N
     *
     * @return BelongsToMany
     */
    public function applicantAccessGroups(): BelongsToMany
    {
        return $this->belongsToMany(DocApplication::class, ApplicantAccessGroup::class)
            ->withTimestamps();
    }
}
