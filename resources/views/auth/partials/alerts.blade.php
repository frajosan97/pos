{{-- Success or Error Messages --}}
@if (session('status'))
<div class="alert alert-success" role="alert">
    {!! session('status') !!}
</div>
@endif

@if (session('success'))
<div class="alert alert-success" role="alert">
    {!! session('success') !!}
</div>
@endif

@if (session('error'))
<div class="alert alert-danger" role="alert">
    {!! session('error') !!}
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="list-group">
        @foreach ($errors->all() as $error)
        <li class="list-group-item p-0 border-0 bg-transparent text-white">{!! $error !!}</li>
        @endforeach
    </ul>
</div>
@endif