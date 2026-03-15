@extends('layout.main')
@section('content')
<style>.ltn__call-to-action-area .call-to-action-inner a{border-radius: 6px;}</style>
    <div class="ltn__about-us-area search pt-120 pb-120 home-banner">
        <div class="container">
            <div class="row">
                <div class="align-self-center">
                    <div class="about-us-info-wrap">
                        <div class="section-title-area ltn__section-title-2 text-center">
                            <h1 class="section-title">Vind gerechten bij jou in de buurt</h1>
                        </div>
                        <div class="search-open">
                            <input type="hidden" id="client" value="false">
                            <form action="{{route('search.coordinates')}}" method="GET">
                                @csrf
                                <div class="row">
                                    <div class="col-12 pos-relative">
                                        <!-- <i class="fa-solid fa-location-dot location-pin"></i> -->
                                        <input type="text" class="form-input-box search-box" placeholder="Locatie" name="plaats" id="autocomplete" aria-describedby="selection">
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
                                    <div class="error red">Voer een plaats in en selecteer een in het dropdown menu</div>
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
                        <div class="align-content-end">
                            <div class="text-left">
                                <div id="searchError" style="color: red; display: none;">
                                    Voer een geldig adres in met een geldige postcode.
                                </div>                    
                            </div>
                            <div class="text-right">
                                <a href="{{route('search.cooks')}}" class="cook-search-btn">Bekijk Thuiskoks</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="ltn__service-area ltn__primary-bg pt-115 pb-70">
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
                            <img src="img/hoe1.svg" alt="Zoek gerechten">
                        </div>
                        <div class="service-item-brief">
                            <h3>Zoek gerechten in de buurt</h3>
                            <p>Je kunt via DeBurenKoken.nl thuisgekookte gerechten vinden uit jouw eigen omgeving. Scroll en laat je verrassen door het aanbod.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <img src="img/hoe2.png" alt="Contact met Thuiskok">
                        </div>
                        <div class="service-item-brief">
                            <h3>Kom direct in contact met een Thuiskok</h3>
                            <p>Heb je een gerecht gevonden? Bestel dan direct via DeBurenKoken.nl met een paar klikken bij de Thuiskok uit jouw buurt.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <img src="img/hoe3.png" alt="Geniet">
                        </div>
                        <div class="service-item-brief">
                            <h3>Geniet!</h3>
                            <p>Haal je bestelling af en geniet van een thuisgekookte maaltijd!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


   <!-- Homecook reviews 
   <div class="ltn__about-us-area testimonials testimonials-home">
       <div class="container">
           <div class="row justify-content-center">
               <div class="align-self-center w-80">
                    <div class="about-us-info-wrap">
                        <div class="page-title">
                           <h1>Woorden van Thuiskoks</h1>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center testimonial-content">
                <div class="col-lg-4 col-sm-6">
                    <div class="testimonial-item alt">
                        <div class="circle">
                            <img src="{{asset('img/nine.jpg')}}" class="round-image mx-auto" alt="gratis aanmelden">
                        </div>
                        <div class="service-item-brief">
                            <h4>Kookliefhebber, Thuiskok uit Sliedrecht</h4>
                            <hr/>
                           <p>Ik vind het fantastisch om te zien hoe iets eenvoudigs als een maaltijd mensen samenbrengt. Of het nu een snelle doordeweekse maaltijd is of een uitgebreid diner, het is altijd bijzonder om te zien hoe een goed bereide maaltijd een glimlach op iemands gezicht tovert.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="testimonial-item">
                        <div class="circle">
                            <img src="{{asset('img/ten.jpg')}}" class="round-image mx-auto" alt="Contact met Thuiskok">
                        </div>
                        <div class="service-item-brief">
                            <h4>Dethuischef, Thuiskok uit Papendrecht</h4>
                            <hr/>
                            <p>Als kok is koken voor mij veel meer dan alleen een dagelijkse taak; het is een passie die me vreugde en voldoening brengt. Wat ik het meest waardeer aan koken, is de creativiteit die het met zich meebrengt.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="testimonial-item alt">
                        <div class="circle">
                            <img src="{{asset('img/eleven.jpg')}}" class="round-image mx-auto" alt="Geniet">
                        </div>
                        <div class="service-item-brief">
                            <h4>Desmaakmaker, Thuiskok uit Sliedrecht</h4>
                            <hr/>
                            <p>Ik vind het geweldig om te experimenteren met seizoensgebonden producten. Er is iets bijzonders aan het koken met ingrediënten die op dat moment op hun best zijn.</p>
                        </div>
                    </div>
                </div>
             </div>
       </div>
 </div> 
-->


    <div class="ltn__call-to-action-area ltn__call-to-action-4 bg-image pt-115 pb-60 mb-60">
        <div style='z-index: 0;' class="container">
            <div class="call-to-action-inner call-to-action-inner-4">
                
                    <div class="section-title-area ltn__section-title-2">
                        <h1>Wil jij gerechten delen met de buurt?</h1>
                        <p>Meld je aan als Thuiskok en begin direct met het aanbieden van jouw heerlijke gerechten!</p>
                        <a href="{{ route('register.info') }}">Registreer</a>
                    </div>
                
            </div>
        </div>
    </div>
@endsection

@section('page.scripts')
    @include('layout.scripts.google')
@endsection
