<?php

namespace App\Policies;

use App\Models\Locations;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocationPolicy
{
    use HandlesAuthorization;

    /**
     * If user is super user, allow all the action
     * 
     * @param User $user
     * @param mixed $ability
     * 
     * @return bool
     */
    public function before($user, $ability)
    {
        if ($user->super_user) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the locations.
     *
     * @param  \App\User  $user
     * @param  \App\Locations  $locations
     * @return mixed
     */
    public function update(User $user, Locations $locations)
    {
        return !empty($locations->locationEditor()->where('user_id', $user->id)->get()->toArray());
    }

    /**
     * Determine whether the user can delete the locations.
     *
     * @param  \App\User  $user
     * @param  \App\Locations  $locations
     * @return mixed
     */
    public function delete(User $user, Locations $locations)
    {
        return !empty($user->locationRelated()->where('location_id', $locations->id)->get()->toArray());
    }
}
