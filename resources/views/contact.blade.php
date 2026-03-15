@extends('layout.main')
@section('content')
    <div class="dbk-page-header">
        <div class="container"><h1>Contact</h1></div>
    </div>
    <section class="dbk-contact-section">
        <div class="container">
            <div class="dbk-contact-grid">
                <div class="dbk-contact-info">
                    <h2>Neem contact op</h2>
                    <p>Staat je vraag niet tussen de <a href="{{ route('customer.facts') }}">veelgestelde vragen</a>? Stuur ons een bericht.</p>
                    <div class="dbk-contact-details">
                        <div class="dbk-contact-item">
                            <i class="fa-solid fa-envelope"></i>
                            <span>info@deburenkoken.nl</span>
                        </div>
                    </div>
                </div>
                <div class="dbk-contact-form-wrap">
                    @if($errors->has('csrf'))
                        <div class="alert alert-warning mb-3" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> {{ $errors->first('csrf') }}
                        </div>
                    @endif
                    <form action="{{route('contact.form')}}" method="POST" class="dbk-contact-form" id="contactForm">
                        @csrf
                        <div class="dbk-form-row">
                            <div class="form-group">
                                <label for="name">Naam<span class="required-star"></span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required pattern="^[A-Za-zÀ-ÖØ-öø-ÿ\s\-'\.]+$" title="Alleen letters, spaties en speciale tekens zoals ' - . zijn toegestaan">
                                <small id="nameError" class="form-text text-danger"></small>
                                @error('name')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="phonenumber">Telefoonnummer (optioneel)</label>
                                <input type="tel" class="form-control" id="phonenumber" name="phone_number" value="{{ old('phone_number') }}" pattern="[0-9]*">
                                <small id="phoneError" class="form-text text-danger"></small>
                                @error('phone_number')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email">E-mailadres</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            <small id="emailError" class="form-text text-danger"></small>
                            @error('email')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="question">Vraag</label>
                            <textarea class="form-control" id="question" name="question" rows="4" required maxlength="1000" data-max-length="1000" pattern="^[A-Za-zÀ-ÖØ-öø-ÿ0-9\s\.\,\?\!\:\;\-\–\—\'\&quot;\(\)\@\#\%\&amp;\*\+\=\€\$\/\n\r]*$" title="Alleen letters, cijfers, spaties en standaard leestekens zijn toegestaan">{{ old('question') }}</textarea>
                            <div class="d-flex justify-content-between">
                                <small id="questionError" class="form-text text-danger"></small>
                                <small id="charCounter" class="form-text text-muted">0 / 1000 karakters</small>
                            </div>
                            @error('question')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="dbk-submit-btn">Verstuur</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('contactForm');
            const phoneInput = document.getElementById('phonenumber');
            const emailInput = document.getElementById('email');
            const nameInput = document.getElementById('name');
            const questionInput = document.getElementById('question');
            const charCounter = document.getElementById('charCounter');
            const MAX_QUESTION_LENGTH = 1000;
            function updateCharCounter() {
                const currentLength = questionInput.value.length;
                charCounter.textContent = currentLength + ' / ' + MAX_QUESTION_LENGTH + ' karakters';
                if (currentLength > 950) {
                    charCounter.classList.remove('text-muted');
                    charCounter.classList.add('text-warning');
                } else if (currentLength >= MAX_QUESTION_LENGTH) {
                    charCounter.classList.remove('text-warning');
                    charCounter.classList.add('text-danger');
                } else {
                    charCounter.classList.remove('text-warning', 'text-danger');
                    charCounter.classList.add('text-muted');
                }
            }
            function enforceQuestionLimit() {
                if (questionInput.value.length > MAX_QUESTION_LENGTH) {
                    questionInput.value = questionInput.value.substring(0, MAX_QUESTION_LENGTH);
                }
                updateCharCounter();
            }
            function protectMaxLength() {
                if (questionInput.getAttribute('maxlength') !== '1000') questionInput.setAttribute('maxlength', '1000');
                if (questionInput.getAttribute('data-max-length') !== '1000') questionInput.setAttribute('data-max-length', '1000');
            }
            updateCharCounter();
            questionInput.addEventListener('input', function() {
                protectMaxLength();
                enforceQuestionLimit();
                var questionRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ0-9\s\.\,\?\!\:\;\-\–\—\'\"\(\)\@\#\%\&\*\+\=\€\$\/\n\r]*$/;
                if (this.value && !questionRegex.test(this.value)) {
                    var filteredValue = this.value.replace(/[^A-Za-zÀ-ÖØ-öø-ÿ0-9\s\.\,\?\!\:\;\-\–\—\'\"\(\)\@\#\%\&\*\+\=\€\$\/\n\r]/g, '');
                    if (this.value !== filteredValue) {
                        this.value = filteredValue;
                        document.getElementById('questionError').textContent = 'Sommige karakters zijn niet toegestaan en zijn verwijderd';
                        setTimeout(function() { if (document.getElementById('questionError').textContent === 'Sommige karakters zijn niet toegestaan en zijn verwijderd') document.getElementById('questionError').textContent = ''; }, 3000);
                    }
                }
            });
            questionInput.addEventListener('keydown', function(e) {
                protectMaxLength();
                if (this.value.length >= MAX_QUESTION_LENGTH && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'ArrowLeft' && e.key !== 'ArrowRight' && e.key !== 'ArrowUp' && e.key !== 'ArrowDown' && !e.ctrlKey && !e.metaKey) e.preventDefault();
            });
            questionInput.addEventListener('paste', function() { protectMaxLength(); setTimeout(enforceQuestionLimit, 0); });
            setInterval(function() { protectMaxLength(); if (questionInput.value.length > MAX_QUESTION_LENGTH) enforceQuestionLimit(); }, 500);
            nameInput.addEventListener('input', function() {
                var value = this.value;
                var filteredValue = value.replace(/[0-9]/g, '');
                if (value !== filteredValue) { this.value = filteredValue; document.getElementById('nameError').textContent = 'Getallen zijn niet toegestaan in de naam'; }
                else if (this.value.trim() === '') { document.getElementById('nameError').textContent = 'Naam is verplicht'; }
                else { document.getElementById('nameError').textContent = ''; }
            });
            nameInput.addEventListener('blur', function() {
                if (this.value.trim() === '') { document.getElementById('nameError').textContent = 'Naam is verplicht'; }
                else { var nameRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ\s\-'\.]+$/; if (!nameRegex.test(this.value)) document.getElementById('nameError').textContent = "Alleen letters, spaties en speciale tekens zoals ' - . zijn toegestaan"; else document.getElementById('nameError').textContent = ''; }
            });
            phoneInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '');
                if (this.value && this.value.length !== 10) document.getElementById('phoneError').textContent = 'Telefoonnummer moet 10 cijfers bevatten';
                else document.getElementById('phoneError').textContent = '';
            });
            emailInput.addEventListener('blur', function() {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (this.value && !emailRegex.test(this.value)) document.getElementById('emailError').textContent = 'Voer een geldig e-mailadres in';
                else document.getElementById('emailError').textContent = '';
            });
            questionInput.addEventListener('blur', function() {
                if (this.value.trim() === '') document.getElementById('questionError').textContent = 'Vraag is verplicht';
                else if (this.value.length > MAX_QUESTION_LENGTH) document.getElementById('questionError').textContent = 'Vraag mag maximaal ' + MAX_QUESTION_LENGTH + ' karakters bevatten';
                else { var questionRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ0-9\s\.\,\?\!\:\;\-\–\—\'\"\(\)\@\#\%\&\*\+\=\€\$\/\n\r]*$/; if (!questionRegex.test(this.value)) document.getElementById('questionError').textContent = 'Alleen letters, cijfers, spaties en standaard leestekens zijn toegestaan'; else document.getElementById('questionError').textContent = ''; }
            });
            form.addEventListener('submit', function(e) {
                var isValid = true;
                protectMaxLength(); enforceQuestionLimit();
                if (nameInput.value.trim() === '') { document.getElementById('nameError').textContent = 'Naam is verplicht'; isValid = false; }
                else { var nameRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ\s\-'\.]+$/; if (!nameRegex.test(nameInput.value)) { document.getElementById('nameError').textContent = "Alleen letters, spaties en speciale tekens zoals ' - . zijn toegestaan"; isValid = false; } }
                if (phoneInput.value && phoneInput.value.length !== 10) { document.getElementById('phoneError').textContent = 'Telefoonnummer moet 10 cijfers bevatten'; isValid = false; }
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailInput.value)) { document.getElementById('emailError').textContent = 'Voer een geldig e-mailadres in'; isValid = false; }
                if (questionInput.value.trim() === '') { document.getElementById('questionError').textContent = 'Vraag is verplicht'; isValid = false; }
                else if (questionInput.value.length > MAX_QUESTION_LENGTH) { document.getElementById('questionError').textContent = 'Vraag mag maximaal ' + MAX_QUESTION_LENGTH + ' karakters bevatten'; isValid = false; }
                else { var qr = /^[A-Za-zÀ-ÖØ-öø-ÿ0-9\s\.\,\?\!\:\;\-\–\—\'\"\(\)\@\#\%\&\*\+\=\€\$\/\n\r]*$/; if (!qr.test(questionInput.value)) { document.getElementById('questionError').textContent = 'Alleen letters, cijfers, spaties en standaard leestekens zijn toegestaan'; isValid = false; } }
                if (!isValid) e.preventDefault();
            });
        });
    </script>
@endsection
