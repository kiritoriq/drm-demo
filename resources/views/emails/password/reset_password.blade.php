@component('mail::message')
    <h1 style="text-align: center; padding-bottom: 20px">
        <img
            src="{{ asset('images/dr-demo.png') }}"
            style="max-height: 60px; vertical-align: middle; padding-bottom: 5px" alt="Reset Password">
        <br>
        <span style="padding-top: 5px">Reset Password Mail</span>
    </h1>
    Hi, <strong>{{ $user->name }}</strong>. You have submitted a request to reset your DRM account password.
    <br>
    <br>
    Please press the button below to go to the reset password page
    @component('mail::button', ['url' => route('password.reset', ['token' => $token, 'email' => $user->email])])
        Reset Password
    @endcomponent
    <br>
    <hr>
    <br>
    <small>
        If you not doing it, please contact to our administrator email.<br>
        <b>This message is sent automatically. Please, do not share this message to anyone!</b>
    </small>
@endcomponent
