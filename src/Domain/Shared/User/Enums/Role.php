<?php

namespace Domain\Shared\User\Enums;

use ArrayAccess;
use Domain\Shared\Foundation\Concerns\Enum\HasCaseResolver;
use Domain\Shared\User\Models\User;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role as Model;

/**
 * @method static string admin()
 * @method static string customer()
 * @method static string serviceManager()
 * @method static string officeAdmin()
 * @method static string account()
 * @method static string contractor()
 */
enum Role: string
{
    use HasCaseResolver;

    case admin = 'Admin';

    case customer = 'Customer';

    case serviceManager = 'Service Manager';

    case officeAdmin = 'Office Admin';

    case account = 'Account';

    case contractor = 'Contractor';

    case branchCustomer = 'Branch Customer';

    /**
     * @param  User|null  $user
     */
    public static function has(Role $role, User | null $user = null): bool
    {
        return self::hasAny(Arr::wrap($role), $user);
    }

    public static function doesntHave(Role $role, User | null $user = null): bool
    {
        return ! self::has($role, $user);
    }

    /**
     * @param  User | null  $user
     */
    public static function hasAny(ArrayAccess | array $roles, User | null $user = null): bool
    {
        $resolved = User::query()->resolve($user);

        foreach ($roles as $role) {
            if ($resolved->hasRole($role->value)) {
                return true;
            }
        }

        return false;
    }

    public static function hasIds(ArrayAccess | array $roles, array $preferredRoles): bool
    {
        $resolved = array_intersect(
            Model::query()
                ->whereIn(column: 'id', values: $roles)
                ->pluck(column: 'name')
                ->toArray(),

            $preferredRoles
        );

        if (empty($resolved)) {
            return false;
        }

        return true;
    }

    public static function exactlyCustomerRole(): bool
    {
        return self::has(self::customer) && (self::doesntHave(self::admin) || self::doesntHave(self::officeAdmin));
    }

    public static function exactlyBranchCustomerRole(): bool
    {
        return self::has(self::branchCustomer) && (self::doesntHave(self::admin) || self::doesntHave(self::officeAdmin));
    }

    public static function exactlyServiceManagerRole(): bool
    {
        return self::has(self::serviceManager) && (self::doesntHave(self::admin) || self::doesntHave(self::officeAdmin));
    }

    public static function customersRole(): bool
    {
        return self::hasAny([self::customer, self::branchCustomer]) && (self::doesntHave(self::admin) || self::doesntHave(self::officeAdmin));
    }

    public static function exactlyContractorRole(): bool
    {
        return self::has(self::contractor) && (self::doesntHave(self::admin) || self::doesntHave(self::officeAdmin));
    }

    public static function internalUserRoles(): array
    {
        return [
            self::admin->value,
            self::officeAdmin->value,
            self::serviceManager->value,
            self::account->value
        ];
    }

    public static function canAccessAdminPanel(): array
    {
        return [
            self::admin->value,
            self::customer->value,
            self::branchCustomer->value,
            self::officeAdmin->value,
            self::serviceManager->value,
            self::account->value
        ];
    }
}
