@component('mail::message')
    <p>
        <a href="{{ url('admin/tickets') }}">{{ $message->sender->email }} message:</a>
    </p>
    <p>
        {{ $message->body }}
    </p>
    <hr>
    <br>
    <small>
        You're receiving this email because of your account on <a href="{{ url('') }}">DRM - Dr. Maintenance</a> and the ticket you created.
    </small>
@endcomponent
