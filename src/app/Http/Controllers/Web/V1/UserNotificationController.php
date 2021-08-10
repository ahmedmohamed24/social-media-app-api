<?php

namespace App\Http\Controllers\Web\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notifications\SaveTokenRequest;
use App\Http\Traits\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;

class UserNotificationController extends Controller
{
    use ApiResponse;

    public function saveToken(SaveTokenRequest $request)
    {
        $user = $request->user('api');
        //check if this device already saved
        if ($user->notificationDevices()->where('device_client', $request->device_client)->count() > 0) {
            //update this device
            $data = $user->notificationDevices()->where('device_client', $request->device_client)->firstOrFail()->update($request->validated());
        } else {
            $data = $user->notificationDevices()->create($request->validated());
        }

        return $this->response(200, 'success', \null, $data);
    }

    public function sendPush(Request $request)
    {
        $user = $request->user('api');
        $data = [
            'to' => $user->notificationDevices()->first()->device_token,
            'notification' => [
                'title' => 'El nemr',
                'body' => 'Sample Notification',
                'icon' => url('/logo.png'),
            ],
        ];
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key='.config('app.firebase_server_key'),
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        curl_exec($ch);

        return redirect(route('home'))->with('message', 'Notification sent!');
    }

    public function index()
    {
        $users = User::all();

        return \view('welcome', ['user' => \auth()->user(), 'user_id' => \auth()->id(), 'users' => $users]);
    }
}
