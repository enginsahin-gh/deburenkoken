@if($errors->has('csrf'))
    <div class="alert alert-warning" role="alert">
        <i class="fas fa-exclamation-triangle"></i> {{ $errors->first('csrf') }}
    </div>
@endif
