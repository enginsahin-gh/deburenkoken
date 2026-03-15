@extends('layout.dashboard')

@section('dashboard')
    <style>
        @media (max-width: 767px) {
            .header-row {
                display: none;
            }
            .mobile-label {
                display: inline-block !important;
                margin-left: 8px;
            }
        }
        .mobile-label {
            display: none;
        }
    </style>

    <div class="container">
        <div class="row">
            <div class="col-8 offset-2">
                <form action="{{route('dashboard.settings.privacy.update')}}" method="post" class="form-box">
                    @csrf
                    <div class="row mt-3 header-row">
                        <div class="col-5">
                        </div>
                        <div class="col-2">
                            Altijd zichtbaar
                        </div>
                        <div class="col-2">
                            Zichtbaar na bestelling
                        </div>
                        <div class="col-2">
                            Niet zichtbaar
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-5">
                            Thuiskoknaam<span class="required-star"></span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="username" id="username" value="3" checked disabled>
                            <span class="mobile-label">Altijd zichtbaar</span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="username" id="username" value="2" disabled>
                            <span class="mobile-label">Zichtbaar na bestelling</span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="username" id="username" value="1" disabled>
                            <span class="mobile-label">Niet zichtbaar</span>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-5">
                            Voornaam<span class="required-star"></span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="firstname" id="firstname-3" value="3" disabled>
                            <span class="mobile-label">Altijd zichtbaar</span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="firstname" id="firstname-2" value="2" disabled>
                            <span class="mobile-label">Zichtbaar na bestelling</span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="firstname" id="firstname-1" value="1" checked disabled>
                            <span class="mobile-label">Niet zichtbaar</span>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-5">
                            Achternaam<span class="required-star"></span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="lastname" id="lastname" value="3" disabled>
                            <span class="mobile-label">Altijd zichtbaar</span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="lastname" id="lastname" value="2" disabled>
                            <span class="mobile-label">Zichtbaar na bestelling</span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="lastname" id="lastname" value="1" checked disabled>
                            <span class="mobile-label">Niet zichtbaar</span>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-5">
                            Woonplaats<span class="required-star"></span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="place" id="place-3" value="3" checked disabled>
                            <span class="mobile-label">Altijd zichtbaar</span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="place" id="place-2" value="2" disabled>
                            <span class="mobile-label">Zichtbaar na bestelling</span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="place" id="place-1" value="1" disabled>
                            <span class="mobile-label">Niet zichtbaar</span>
                            @if (is_null($privacy))
                                <input type="number" name="place" id="place-def" value="3" style="display: none">
                            @endif
                        </div>
                    </div>
                    @if ($errors->has('place'))
                        @foreach($errors->get('place') as $error)
                            <div class="row text-center red">
                                <div class="col-12">
                                    {{$error}}
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <div class="row mt-3">
                        <div class="col-5">
                            Postcode<span class="required-star"></span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="postal" id="postal-3" value="3" disabled checked>
                            <span class="mobile-label">Altijd zichtbaar</span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="postal" id="postal-2" value="2" disabled>
                            <span class="mobile-label">Zichtbaar na bestelling</span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="postal" id="postal-1" value="1" disabled>
                            <span class="mobile-label">Niet zichtbaar</span>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-5">
                            E-mailadres<span class="required-star"></span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="email" id="email-3" value="3" {{ $privacy?->showEmail() === 3 ? 'checked' : null}}>
                            <span class="mobile-label">Altijd zichtbaar</span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="email" id="email-2" value="2" {{ $privacy?->showEmail() === 2 ? 'checked' : null}} @if (is_null($privacy)) checked @endif>
                            <span class="mobile-label">Zichtbaar na bestelling</span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="email" id="email-1" value="1" disabled>
                            <span class="mobile-label">Niet zichtbaar</span>
                        </div>
                    </div>
                    @if ($errors->has('email'))
                        @foreach($errors->get('email') as $error)
                            <div class="row text-center red">
                                <div class="col-12">
                                    {{$error}}
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <div class="row mt-3">
                        <div class="col-5">
                            Straat<span class="required-star"></span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="street" id="street-3" value="3" {{$privacy?->showStreet() === 3 ? 'checked' : null}}>
                            <span class="mobile-label">Altijd zichtbaar</span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="street" id="street-2" value="2" {{$privacy?->showStreet() === 2 ? 'checked' : null}}  @if (is_null($privacy)) checked @endif>
                            <span class="mobile-label">Zichtbaar na bestelling</span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="street" id="street-1" value="1" disabled>
                            <span class="mobile-label">Niet zichtbaar</span>
                        </div>
                    </div>
                    @if ($errors->has('street'))
                        @foreach($errors->get('street') as $error)
                            <div class="row text-center red">
                                <div class="col-12">
                                    {{$error}}
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <div class="row mt-3">
                        <div class="col-5">
                            Huisnummer<span class="required-star"></span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="number" id="number-3" value="3" {{$privacy?->showHouseNumber() === 3 ? 'checked' : null}}>
                            <span class="mobile-label">Altijd zichtbaar</span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="number" id="number-2" value="2" {{$privacy?->showHouseNumber() === 2 ? 'checked' : null}} @if (is_null($privacy)) checked @endif>
                            <span class="mobile-label">Zichtbaar na bestelling</span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="number" id="number-1" value="1" disabled>
                            <span class="mobile-label">Niet zichtbaar</span>
                        </div>
                    </div>
                    @if ($errors->has('number'))
                        @foreach($errors->get('number') as $error)
                            <div class="row text-center red">
                                <div class="col-12">
                                    {{$error}}
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <div class="row mt-3">
                        <div class="col-5">
                            Telefoonnummer<span class="required-star"></span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="phone" id="phone-3" value="3" {{ $privacy?->showPhone() === 3 ? 'checked' : null}}>
                            <span class="mobile-label">Altijd zichtbaar</span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="phone" id="phone-2" value="2" {{ $privacy?->showPhone() === 2 ? 'checked' : null}}  @if (is_null($privacy) || $privacy->showPhone() === 1) checked @endif>
                            <span class="mobile-label">Zichtbaar na bestelling</span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="phone" id="phone-1" value="1" disabled>
                            <span class="mobile-label">Niet zichtbaar</span>
                        </div>
                    </div>
                    @if ($errors->has('phone'))
                        @foreach($errors->get('phone') as $error)
                            <div class="row text-center red">
                                <div class="col-12">
                                    {{$error}}
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <div class="row mt-3">
                        <div class="col-5">
                            Aantal verkochte porties<span class="required-star"></span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="sold_portions" id="soldportions-3" value="3" {{ old('sold_portions', $privacy?->showSoldPortions() ?? 1) == 3 ? 'checked' : '' }}>
                            <span class="mobile-label">Altijd zichtbaar</span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="sold_portions" id="soldportions-2" value="2" disabled>
                            <span class="mobile-label">Zichtbaar na bestelling</span>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="sold_portions" id="soldportions-1" value="1" {{ old('sold_portions', $privacy?->showSoldPortions() ?? 1) == 1 ? 'checked' : '' }}>
                            <span class="mobile-label">Niet zichtbaar</span>
                        </div>
                    </div>
                    @if ($errors->has('soldPortions'))
                        @foreach($errors->get('sold_portions') as $error)
                            <div class="row text-center red">
                                <div class="col-12">
                                    {{$error}}
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <div class="row mt-2 text-center">
                        <button class="btn btn-light btn-small btn-center" type="submit">Opslaan</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@section('page.scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectedPortion = "{{ session('selected_sold_portions', $privacy?->showSoldPortions() ?? 1) }}";
            if (selectedPortion) {
                document.querySelector(`input[name="sold_portions"][value="${selectedPortion}"]`).checked = true;
            }
        });
    </script>
@endsection