<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\User;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class PostCreatedJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $post;
    public $id;

    /**
     * Create a new job instance.
     *
     * @param mixed $id
     */
    public function __construct(Post $post, $id)
    {
        $this->post = $post;
        $this->id = $id;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        //get user friends
        $user = User::findOrFail($this->id);
        $userFriends = User::all();
        // $userFriends = $user->relations()->where('relation', 'friend')->get();
        $userName = $user->name;
        $userName = \strlen($userName) > 20 ? Str::substr($userName, 0, 20).'...' : $userName;
        $created_at = $this->post->created_at->diffForHumans();
        $postLink = \route('post.show', $this->post->id);
        $data = [
            'title' => "new post added by {$userName}",
            'body' => "{$postLink} . {$created_at}",
        ];
        foreach ($userFriends as $friend) {
            $devices = $friend->notificationDevices()->get();
            foreach ($devices as $device) {
                (new PushNotificationService())->sendPush($device->device_token, $data);
            }
        }
    }
}
