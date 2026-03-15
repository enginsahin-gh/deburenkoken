@extends('layout.main')
@section('content')
    <!-- Hero Section -->
    <section class="dbk-hero">
        <div class="dbk-hero-emojis">
            <span class="dbk-emoji dbk-emoji-1">🍲</span>
            <span class="dbk-emoji dbk-emoji-2">🥘</span>
            <span class="dbk-emoji dbk-emoji-3">🍛</span>
            <span class="dbk-emoji dbk-emoji-4">🥗</span>
            <span class="dbk-emoji dbk-emoji-5">🍜</span>
        </div>
        <div class="container">
            <div class="dbk-hero-content">
                <h1 class="dbk-hero-title fade-in-up">Thuisgekookt eten<br>bij jou in de buurt</h1>
                <p class="dbk-hero-subtitle fade-in-up fade-in-up-delay-1">Ontdek heerlijke maaltijden van koks uit je wijk</p>

                <div class="dbk-search-container fade-in-up fade-in-up-delay-2">
                    <input type="hidden" id="client" value="false">
                    <form action="{{route('search.coordinates')}}" method="GET">
                        @csrf
                        <div class="dbk-search-wrap">
                            <input type="text" class="dbk-search-input" placeholder="Zoek op postcode of plaatsnaam..." name="plaats" id="autocomplete" aria-describedby="selection">
                            <button type="button" class="dbk-search-btn-icon" id="searchButton"><i class="fa-solid fa-magnifying-glass"></i></button>
                            <select id="distance" name="distance" class="d-none">
                                <option value="5">5km</option>
                                <option value="10">10km</option>
                                <option value="20">20km</option>
                                <option value="25">25km</option>
                                <option value="100" selected>100km</option>
                            </select>
                        </div>
                        @error('plaats')
                            <div class="dbk-search-error">Voer een plaats in en selecteer een in het dropdown menu</div>
                        @enderror
                        <div class="form-group d-none" id="lat_area">
                            <label for="latitude">Latitude</label>
                            <input type="text" name="latitude" id="latitude" class="form-control">
                        </div>
                        <div class="form-group d-none" id="long_area">
                            <label for="longitude">Longitude</label>
                            <input type="text" name="longitude" id="longitude" class="form-control">
                        </div>
                        <div class="form-group d-none">
                            <input type="text" name="city" id="city">
                        </div>
                        <button type="submit" id="zoeken" disabled class="d-none"></button>
                    </form>
                </div>

                <div id="searchError" class="dbk-search-error" style="display: none;">
                    Voer een geldig adres in met een geldige postcode.
                </div>

                <div class="dbk-hero-links fade-in-up fade-in-up-delay-2">
                    <a href="{{route('search.cooks')}}" class="dbk-btn-white"><i class="fa-solid fa-users"></i> Bekijk alle Thuiskoks</a>
                </div>

                <div class="dbk-stats fade-in-up fade-in-up-delay-3">
                    <div class="dbk-stat-item"><strong>500+</strong> gerechten</div>
                    <div class="dbk-stat-divider">·</div>
                    <div class="dbk-stat-item"><strong>120+</strong> thuiskoks</div>
                    <div class="dbk-stat-divider">·</div>
                    <div class="dbk-stat-item"><strong>25+</strong> steden</div>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="dbk-how-it-works">
        <div class="container">
            <h2 class="dbk-section-title">Hoe werkt het?</h2>
            <div class="dbk-steps">
                <div class="dbk-step">
                    <div class="dbk-step-number">1</div>
                    <img src="img/hoe1.svg" alt="Zoek gerechten" class="dbk-step-img">
                    <h3>Zoek gerechten in de buurt</h3>
                    <p>Vind thuisgekookte gerechten uit jouw omgeving. Scroll en laat je verrassen door het aanbod.</p>
                </div>
                <div class="dbk-step-connector"></div>
                <div class="dbk-step">
                    <div class="dbk-step-number">2</div>
                    <img src="img/hoe2.png" alt="Contact met Thuiskok" class="dbk-step-img">
                    <h3>Bestel met een paar klikken</h3>
                    <p>Heb je een gerecht gevonden? Bestel direct via DeBurenKoken.nl bij de Thuiskok uit jouw buurt.</p>
                </div>
                <div class="dbk-step-connector"></div>
                <div class="dbk-step">
                    <div class="dbk-step-number">3</div>
                    <img src="img/hoe3.png" alt="Geniet" class="dbk-step-img">
                    <h3>Haal af & geniet!</h3>
                    <p>Haal je bestelling af en geniet van een heerlijke thuisgekookte maaltijd!</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="dbk-testimonials">
        <div class="container">
            <h2 class="dbk-section-title">Wat onze gebruikers zeggen</h2>
            <div class="dbk-testimonial-grid">
                <div class="dbk-testimonial-card">
                    <div class="dbk-testimonial-stars">★★★★★</div>
                    <p>"Eindelijk lekker thuisgekookt eten zonder zelf te hoeven koken. De nasi van mijn buurvrouw is ongelooflijk!"</p>
                    <div class="dbk-testimonial-author">— Maria, Amsterdam</div>
                </div>
                <div class="dbk-testimonial-card">
                    <div class="dbk-testimonial-stars">★★★★★</div>
                    <p>"Als thuiskok verdien ik extra bij met mijn hobby. Super platform, makkelijk te gebruiken!"</p>
                    <div class="dbk-testimonial-author">— Ahmed, Rotterdam</div>
                </div>
                <div class="dbk-testimonial-card">
                    <div class="dbk-testimonial-stars">★★★★★</div>
                    <p>"Verse maaltijden uit de buurt, beter dan bezorging. En je steunt ook nog eens je buren!"</p>
                    <div class="dbk-testimonial-author">— Sandra, Utrecht</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA: Word Thuiskok -->
    <section class="dbk-cta-section">
        <div class="container">
            <div class="dbk-cta-card">
                <div class="dbk-cta-content">
                    <h2>🍳 Wil jij gerechten delen met de buurt?</h2>
                    <p>Meld je aan als Thuiskok en begin direct met het aanbieden van jouw heerlijke gerechten!</p>
                    <a href="{{ route('register.info') }}" class="dbk-cta-btn"><i class="fa-solid fa-arrow-right"></i> Registreer nu</a>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('page.scripts')
    @include('layout.scripts.google')
@endsection
