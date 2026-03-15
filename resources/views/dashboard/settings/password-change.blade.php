@extends('layout.main')

@section('content')
<style>
.btn-outlines {
    background: #fff !important;
    color: #f3723b !important;
    border: 2px solid #f3723b !important;
    padding: 8px 15px !important;
    width: 220px !important;
    border-radius: 6px !important;
    margin-left: 2% !important;
    height: auto !important;
    line-height: 1.2 !important;
}

.btn-light {
    padding: 8px 15px !important;
    height: auto !important;
    line-height: 1.2 !important;
}

.form-row {
    position: relative;
    display: flex;
    width: 100%;
}

.form-row input {
    width: 100%;
}

.form-row i {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
}

.tooltip {
    display: inline-block;
    position: relative;
    margin-left: 5px;
}

.tooltip .tooltiptext {
    visibility: hidden;
    width: 300px;
    background-color: #555;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 5px;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%);
    opacity: 0;
    transition: opacity 0.3s;
}

.tooltip:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
}
</style>

<section class="clearfix pt-4">
    <div class="container">
        <div class="row">
            <div class="col-1"></div>
            <div class="col-10">
                <div class="login-box"> 
                    <div class="row">
                        <div class="col-5">
                            <img src="{{ asset('img/login-sideImg.svg') }}" class="login-sideImg" />
                        </div>
                        <div class="col-7">
                            <h1>Wachtwoord wijzigen</h1>
                            <p class="mt-50">Vul je huidige wachtwoord in en kies een nieuw wachtwoord.</p>

                            @if(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif
                           
                            <form method="POST" action="{{ route('dashboard.settings.password.update') }}">
                                @csrf
                                <div class="form-group mt-4">
                                    <label for="current_password">
                                        Huidig wachtwoord
                                        <div class="tooltip">
                                            <i class="fas fa-info-circle"></i>
                                            <span class="tooltiptext">Dit is je huidige wachtwoord</span>
                                        </div>
                                    </label>
                                    <div class="form-row">
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                        <i class="far fa-eye" id="toggleCurrentPassword"></i>
                                    </div>
                                    @error('current_password')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mt-4">
                                    <label for="password">
                                        Nieuw wachtwoord
                                        <div class="tooltip">
                                            <i class="fas fa-info-circle"></i>
                                            <span class="tooltiptext">Het wachtwoord moet minimaal 8 tekens, 1 hoofdletter, 1 cijfer en 1 symbool bevatten</span>
                                        </div>
                                    </label>
                                    <div class="form-row">
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <i class="far fa-eye" id="togglePassword"></i>
                                    </div>
                                    @error('password')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mt-4">
                                    <label for="password_confirmation">
                                        Bevestig nieuw wachtwoord
                                        <div class="tooltip">
                                            <i class="fas fa-info-circle"></i>
                                            <span class="tooltiptext">Dit wachtwoord moet gelijk zijn</span>
                                        </div>
                                    </label>
                                    <div class="form-row">
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                        <i class="far fa-eye" id="togglePasswordConfirmation"></i>
                                    </div>
                                </div>

                                <div class="form-group mt-5 d-flex gap-3">
                                    <button type="submit" class="btn btn-light btn-small flex-fill">Wachtwoord wijzigen</button>
                                    <a href="{{ route('dashboard.settings.details.home') }}" class="btn btn-outlines flex-fill">Annuleren</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-1"></div>
        </div>
    </div>
</section>

@endsection

@section('page.scripts')
<script>
    const toggleCurrentPassword = document.querySelector('#toggleCurrentPassword');
    const togglePassword = document.querySelector('#togglePassword');
    const togglePasswordConfirmation = document.querySelector('#togglePasswordConfirmation');
    
    const currentPassword = document.querySelector('#current_password');
    const password = document.querySelector('#password');
    const passwordConfirmation = document.querySelector('#password_confirmation');

    toggleCurrentPassword.addEventListener('click', () => {
        const type = currentPassword.getAttribute('type') === 'password' ? 'text' : 'password';
        currentPassword.setAttribute('type', type);
        toggleCurrentPassword.classList.toggle('fa-eye-slash');
    });

    togglePassword.addEventListener('click', () => {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        togglePassword.classList.toggle('fa-eye-slash');
    });

    togglePasswordConfirmation.addEventListener('click', () => {
        const type = passwordConfirmation.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirmation.setAttribute('type', type);
        togglePasswordConfirmation.classList.toggle('fa-eye-slash');
    });
</script>
@endsection