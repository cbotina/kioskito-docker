<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isGranted(User::ROLE_ADMIN);
    }
    public function viewAvailable(User $user): bool
    {
        return $user->isGranted(User::ROLE_ADMIN)
        or $user->isGranted(User::ROLE_CLIENT);
    }

    public function viewApproved(User $user): bool
    {
        return $user->isGranted(User::ROLE_ADMIN) or $user->isGranted(User::ROLE_KITCHEN_BOSS   );
    }
    public function viewUserOrders(User $user): bool
    {
        return $user->isGranted(User::ROLE_ADMIN) or $user->isGranted(User::ROLE_CLIENT   );
    }

    public function approveOrReject(User $user): bool {
        return $user->isGranted(User::ROLE_ADMIN);

    }

    public function startOrFinish(User $user) : bool {
        return $user->isGranted(User::ROLE_KITCHEN_BOSS);

    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isGranted(User::ROLE_CLIENT);

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return true;

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        return true;

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Order $order): bool
    {
        return true;

    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        return true;

    }
}
