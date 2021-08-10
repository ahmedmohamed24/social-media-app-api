<?php

namespace App\Services;

use App\Http\Traits\ApiResponse;
use App\Models\User;

class UserRelationService
{
    use ApiResponse;

    public function sendFriendRequest(User $user)
    {
        if ($this->isRelatedUserIsTheAuthUser($user)) {
            $response = $this->response(406, 'Forbidden', ['You cannot add yourself'], []);
        } elseif ($this->hasPreviousRelation($user)) {
            if ($this->isFriends($user)) {
                $response = $this->response(406, 'Forbidden', ['You are already  friends'], []);
            } elseif ($this->isBlocked($user)) {
                $response = $this->response(406, 'Forbidden', ['Cannot send this request'], []);
            } elseif ($this->hasSuspendedFriendRequest($user)) {
                $response = $this->response(406, 'Forbidden', ['You cannot add your self'], []);
            } elseif ($this->hasARejectedRequest($user)) {
                $response = $this->response(406, 'Forbidden', ['Your previous request has been refused.'], []);
            } else {
                $response = $this->response(500, 'Internal error', ['Internal error occurred, please try again later'], []);
            }
        } else {
            $isSent = \auth('api')->user()->relations()->create(['relatingUserID' => $user->id, 'relation' => 'request']);
            $response = $this->response(201, 'success', \null, ['Friend Request sent successfully', $isSent]);
        }

        return $response;
    }

    public function hasPreviousRelation(User $user): bool
    {
        $firstUserRelations = \auth()->user()->relations()->where('relatingUserID', $user->id)->count();
        $secondUserRelations = $user->relations()->where('relatingUserID', \auth()->id())->count();

        return $firstUserRelations > 0 || $secondUserRelations > 0;
    }

    public function hasSuspendedFriendRequest(User $user): bool
    {
        $u1Relation = $user->relations()->where('relatingUserID', \auth()->id())->where('relation', 'request')->count();
        $u2Relation = auth()->user()->relations()->where('relatingUserID', $user->id)->where('relation', 'request')->count();

        return $u1Relation > 0 || $u2Relation > 0;
    }

    public function isBlocked(User $user): bool
    {
        $u1Relation = $user->relations()->where('relatingUserID', \auth()->id())->where('relation', 'block')->count();
        $u2Relation = auth()->user()->relations()->where('relatingUserID', $user->id)->where('relation', 'block')->count();

        return $u1Relation > 0 || $u2Relation > 0;
    }

    public function isFriends(User $user): bool
    {
        $u1Relation = $user->relations()->where('relatingUserID', \auth()->id())->where('relation', 'friend')->count();
        $u2Relation = auth()->user()->relations()->where('relatingUserID', $user->id)->where('relation', 'friend')->count();

        return $u1Relation > 0 || $u2Relation > 0;
    }

    public function isRelatedUserIsTheAuthUser(User $user): bool
    {
        return $user->id == \auth()->id();
    }

    public function hasARejectedRequest(User $user): bool
    {
        return 0 !== auth()->user()->relations()->where('relatingUserID', $user->id)->where('relation', 'rejected')->count();
    }

    public function blockUser(User $user)
    {
        if ($this->isRelatedUserIsTheAuthUser($user)) {
            $response = $this->response(406, 'Forbidden', ['You cannot add your self'], []);
        } elseif ($this->isUserAlreadyBlocked($user)) {
            $response = $this->response(406, 'Forbidden', ['This user could not be found by you.'], []);
        } elseif ($this->hasPreviousNonBlockRelation($user)) {
            //block user
            if ($user = \auth()->user()->relations()->where('relatingUserID', $user->id)->first()) {
                $user->update(['relation' => 'block']);
            } else {
                $user->relations()->where('relatingUserID', auth()->id())->firstOrFail()->update(['relation' => 'block']);
            }

            $response = $this->response(201, 'blocked', \null, \null);
        } else {
            \auth()->user()->relations()->create(['relatingUserID' => $user->id, 'relation' => 'block']);
            $response = $this->response(201, 'blocked', \null, \null);
        }

        return $response;
    }

    public function isUserAlreadyBlocked(User $user): bool
    {
        return (0 !== \auth()->user()->relations()->where('relatingUserID', $user->id)->where('relation', 'block')->count()) || (0 !== $user->relations()->where('relatingUserID', \auth()->id())->where('relation', 'block')->count());
    }

    public function hasPreviousNonBlockRelation(User $user): bool
    {
        $firstUserRelations = \auth()->user()->relations()->where('relatingUserID', $user->id)->where('relation', '!=', 'block')->count();
        $secondUserRelations = $user->relations()->where('relatingUserID', \auth()->id())->where('relation', '!=', 'block')->count();

        return $firstUserRelations > 0 || $secondUserRelations > 0;
    }
}
