<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\User;
use App\Models\UserRelation;
use App\Services\UserRelationService;

class RelationController extends Controller
{
    use ApiResponse;
    private $relationService;

    public function __construct(UserRelationService $relationService)
    {
        $this->relationService = $relationService;
    }

    public function addFriend(User $user)
    {
        return $this->relationService->sendFriendRequest($user);
    }

    public function friendList()
    {
        $friends = \auth()->user()->relations()->where('relation', 'friend')->with('relatingUser')->get();
        $friends = $friends->merge(UserRelation::where('relatingUserID', \auth()->id())->with('relatedUser')->where('relation', 'friend')->get());

        return $this->response(200, 'success', \null, ['friends' => $friends]);
    }

    public function rejectFriend(User $user)
    {
        $user->relations()->where('relatingUserID', \auth()->id())->where('relation', 'request')->firstOrFail()->update(['relation' => 'rejected']);

        return $this->response(200, 'rejected', \null, \null);
    }

    public function removeFriend(User $user)
    {
        $isExists = \auth()->user()->relations()->where('relatingUserID', $user->id)->where('relation', 'friend')->first();
        if ($isExists) {
            $isExists->forceDelete();
        } else {
            $user->relations()->where('relatingUserID', \auth()->id())->where('relation', 'friend')->firstOrFail()->forceDelete();
        }

        return $this->response(200, 'deleted', \null, \null);
    }

    public function getFriendsRequestsList()
    {
        $sent = auth()->user()->relations()->where('relation', 'request')->with('relatingUser')->get();
        $received = UserRelation::where('relatingUserID', \auth()->id())->where('relation', 'request')->with('relatedUser')->get();
        $rejected = auth()->user()->relations()->where('relation', 'rejected')->with('relatingUser')->get();
        $rejected = $rejected->merge(UserRelation::where('relatingUserID', \auth()->id())->where('relation', 'rejected')->with('relatedUser')->get());

        return $this->response(200, 'success', \null, ['requests-list' => ['sent' => $sent, 'received' => $received, 'rejected' => $rejected]]);
    }

    public function approveFriendRequest(User $user)
    {
        UserRelation::where('relatingUserID', \auth()->id())->where('relatedUserID', $user->id)->whereIn('relation', ['rejected', 'request'])->firstOrFail()->update(['relation' => 'friend']);

        return $this->response(200, 'approved', \null, \null);
    }

    public function blockUser(User $user)
    {
        return $this->relationService->blockUser($user);
    }

    public function getBlocksList()
    {
        $blockList = \auth()->user()->relations()->where('relation', 'block')->with('relatingUser')->get();

        return $this->response(200, 'success', \null, ['block-list' => $blockList]);
    }

    public function unblockUser(User $user)
    {
        $blockRelation = \auth()->user()->relations()->where('relatingUserID', $user->id)->where('relation', 'block')->firstOrFail();
        $blockRelation->forceDelete();

        return $this->response(200, 'unblocked', \null, \null);
    }
}
