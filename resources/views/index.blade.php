@extends('layout.main')
@section('content')
    <!-- Hero Section -->
    <div class="ltn__about-us-area search pt-120 pb-120 home-banner">
        <div class="container">
            <div class="row">
                <div class="align-self-center">
                    <div class="about-us-info-wrap">
                        <div class="section-title-area ltn__section-title-2 text-center">
                            <h1 class="section-title fade-in-up">Vind thuisgekookt eten bij jou in de buurt</h1>
                            <p class="hero-subtitle fade-in-up fade-in-up-delay-1">Verse maaltijden van koks uit je eigen wijk</p>
                        </div>
                        <div class="search-open fade-in-up fade-in-up-delay-2">
                            <input type="hidden" id="client" value="false">
                            <form action="{{route('search.coordinates')}}" method="GET">
                                @csrf
                                <div class="row">
                                    <div class="col-12 pos-relative">
                                        <input type="text" class="form-input-box search-box" placeholder="Voer je postcode of plaatsnaam in..." name="plaats" id="autocomplete" aria-describedby="selection">
                                        <i class="fa-solid fa-magnifying-glass magnifying-glass pointer" id='searchButton'></i>
                                    </div>
                                    <div class="col-2 hide">
                                        <select id="distance" name="distance" class="form-control search-distance">
                                            <option value="5">5km</option>
                                            <option value="10">10km</option>
                                            <option value="20">20km</option>
                                            <option value="25">25km</option>
                                            <option value="100" selected>100km</option>
                                        </select>
                                    </div>
                                </div>
                                @error('plaats')
                                    <div class="error red" style="color: #FCA5A5; margin-top: 8px;">Voer een plaats in en selecteer een in het dropdown menu</div>
                                @enderror

                                <div class="form-group d-none" id="lat_area">
                                    <label for="latitude">Latitude </label>
                                    <input type="text" name="latitude" id="latitude" class="form-control">
                                </div>

                                <div class="form-group d-none" id="long_area">
                                    <label for="longitude">Longitude </label>
                                    <input type="text" name="longitude" id="longitude" class="form-control">
                                </div>

                                <div class="form-group d-none">
                                    <input type="text" name="city" id="city">
                                </div>

                                <button type="submit" id="zoeken" disabled class="d-none"></button>
                            </form>
                        </div>
                        <div class="align-content-end fade-in-up fade-in-up-delay-2">
                            <div class="text-left">
                                <div id="searchError" style="color: #FCA5A5; display: none;">
                                    Voer een geldig adres in met een geldige postcode.
                                </div>                    
                            </div>
                            <div class="text-center" style="margin-top: 16px;">
                                <a href="{{route('search.cooks')}}" class="cook-search-btn"><i class="fa-solid fa-users"></i> Bekijk alle Thuiskoks</a>
                            </div>
                        </div>
                        <!-- Social proof -->
                        <div class="social-proof-counter fade-in-up fade-in-up-delay-3">
                            <div class="counter-badge">
                                <i class="fa-solid fa-fire"></i>
                                <span>Ontdek verse maaltijden van thuiskoks bij jou in de buurt</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- How it works -->
    <div class="ltn__service-area pt-115 pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area ltn__section-title-2 text-center">
                        <h1 class="section-title">Hoe werkt het?</h1>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <span class="step-number">1</span><br>
                            <img src="img/hoe1.svg" alt="Zoek gerechten">
                        </div>
                        <div class="service-item-brief">
                            <h3>Zoek gerechten in de buurt</h3>
                            <p>Vind thuisgekookte gerechten uit jouw omgeving. Scroll en laat je verrassen door het aanbod.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <span class="step-number">2</span><br>
                            <img src="img/hoe2.png" alt="Contact met Thuiskok">
                        </div>
                        <div class="service-item-brief">
                            <h3>Bestel met een paar klikken</h3>
                            <p>Heb je een gerecht gevonden? Bestel direct via DeBurenKoken.nl bij de Thuiskok uit jouw buurt.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <span class="step-number">3</span><br>
                            <img src="img/hoe3.png" alt="Geniet">
                        </div>
                        <div class="service-item-brief">
                            <h3>Haal af & geniet!</h3>
                            <p>Haal je bestelling af en geniet van een heerlijke thuisgekookte maaltijd!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA: Word Thuiskok -->
    <div class="ltn__call-to-action-area ltn__call-to-action-4 bg-image pt-115 pb-60 mb-60">
        <div style='z-index: 0;' class="container">
            <div class="call-to-action-inner call-to-action-inner-4">
                <div class="section-title-area ltn__section-title-2">
                    <h1><i class="fa-solid fa-heart" style="color: var(--dbk-accent-light); margin-right: 8px;"></i> Wil jij gerechten delen met de buurt?</h1>
                    <p>Meld je aan als Thuiskok en begin direct met het aanbieden van jouw heerlijke gerechten!</p>
                    <a href="{{ route('register.info') }}"><i class="fa-solid fa-arrow-right"></i> Registreer nu</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page.scripts')
    @include('layout.scripts.google')
@endsection
