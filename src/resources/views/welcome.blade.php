@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <button id="btn-nft-enable" onclick="initFirebaseMessagingRegistration()"
                    class="btn btn-danger btn-xs btn-flat">Allow for Notification</button>
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form action="{{ route('send.notification') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" class="form-control" name="title">
                            </div>
                            <div class="form-group">
                                <label>Body</label>
                                <textarea class="form-control" name="body"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Send Notification</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://www.gstatic.com/firebasejs/7.23.0/firebase.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
        var firebaseConfig = {
            apiKey: "AIzaSyBNOw5AnVwyC_1sE_QbEANCBqlV5N9hdJI",
            authDomain: "laravel-social-media-api.firebaseapp.com",
            databaseURL: "https://laravel-social-media-api.firebaseio.com",
            projectId: "laravel-social-media-api",
            storageBucket: "laravel-social-media-api.appspot.com",
            messagingSenderId: "183068862244",
            appId: "1:183068862244:web:1f2f46a04d27d540faf5b1",
            measurementId: "G-Z54Q14V251",
        };
        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        function initFirebaseMessagingRegistration() {
            messaging
                .requestPermission()
                .then(function() {
                    return messaging.getToken()
                })
                .then(function(token) {
                    axios.post('{{ route('save-token') }}', {
                            device_token: token,
                            device_client: navigator.userAgent
                        })
                        .then(function(response) {
                            alert('Token saved successfully.', response);
                        })
                        .catch(function(error) {
                            console.log('User Chat Token Error' + error);
                        });
                    // $.ajaxSetup({
                    //     headers: {
                    //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    //     }
                    // });

                    // $.ajax({
                    //     url: '{{ route('save-token') }}',
                    //     type: 'POST',
                    //     data: {
                    //         device_token: token,
                    //         device_client: navigator.userAgent
                    //     },
                    //     dataType: 'JSON',
                    //     success: function(response) {
                    //         alert('Token saved successfully.');
                    //     },
                    //     error: function(err) {
                    //         console.log('User Chat Token Error' + err);
                    //     },
                    // });
                }).catch(function(err) {
                    console.log('User Chat Token Error' + err);
                });
        }
        messaging.onMessage(function(payload) {

            const noteTitle = payload.notification.title;
            const noteOptions = {
                body: payload.notification.body,
                icon: payload.notification.icon,
            };
            new Notification(noteTitle, noteOptions);
        });
    </script>
@endsection
