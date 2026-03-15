@extends('layout.main')
@section('content')
    <div class="page-header">
        <div class="container"><h1>Veelgestelde vragen</h1></div>
    </div>
    <section class="accordion-section clearfix mt-3" aria-label="Question Accordions">
        <div class="container">
            <div class="row tabs-links d-flex justify-content-center">
                <a href="{{route('customer.facts')}}" class="@if(\Illuminate\Support\Facades\Route::is('customer.facts')) payout @endif" style="display: inline-block; {{ \Illuminate\Support\Facades\Route::is('customer.facts') ? 'background: linear-gradient(to right, #f3723b 0%, #e54750 100%); color: #fff;' : 'color: #f3723b;' }} border: 2px solid #f3723b; padding: 8px 15px; width: 200px; margin: 10px 5px 30px; border-radius: 6px; text-align: center;">Klanten</a>
                <a href="{{route('cook.facts')}}" class="@if(\Illuminate\Support\Facades\Route::is('cook.facts')) payout @endif" style="display: inline-block; {{ \Illuminate\Support\Facades\Route::is('cook.facts') ? 'background: linear-gradient(to right, #f3723b 0%, #e54750 100%); color: #fff;' : 'color: #f3723b;' }} border: 2px solid #f3723b; padding: 8px 15px; width: 200px; margin: 10px 5px 30px; border-radius: 6px; text-align: center;">Thuiskoks</a>
                <a href="{{route('cook.tips')}}" class="@if(\Illuminate\Support\Facades\Route::is('cook.tips')) payout @endif" style="display: inline-block; {{ \Illuminate\Support\Facades\Route::is('cook.tips') ? 'background: linear-gradient(to right, #f3723b 0%, #e54750 100%); color: #fff;' : 'color: #f3723b;' }} border: 2px solid #f3723b; padding: 8px 15px; width: 200px; margin: 10px 5px 30px; border-radius: 6px; text-align: center;">Praktische tips</a>
            </div>
            <h2>Algemeen</h2>
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-0">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-0" aria-expanded="true" aria-controls="collapse-0">
                                Heb ik een account nodig om te bestellen?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-0" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-0">
                        <div class="panel-body px-3 mb-4">
                            <p>Nee, je hebt geen account nodig om bij DeBurenKoken een bestelling te plaatsen. Je kunt direct en zonder account de gewenste maaltijd selecteren en afrekenen. Alle informatie over je bestelling zal dan via het opgegeven mailadres gedeeld worden. Het is daarom wel van belang om het opgegeven e-mailadres goed te controleren.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-1">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-1" aria-expanded="true" aria-controls="collapse-1">
                               Hoe gaat DeBurenKoken om met mijn persoonsgegevens?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-1">
                        <div class="panel-body px-3 mb-4">
                            <p>Het waarborgen van de privacy van bezoekers van DeBurenKoken is een belangrijke taak voor ons. Daarom beschrijven we in onze privacy policy welke informatie we verzamelen en hoe we deze informatie gebruiken. Lees <a href="{{ route('privacy') }}" class="underline">hier</a> onze Privacy Policy.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-2">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-2" aria-expanded="true" aria-controls="collapse-2">
                                Wat zijn de Algemene Voorwaarden van DeBurenKoken?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-2">
                        <div class="panel-body px-3 mb-4">
                            <p>Je kunt onze Algemene Voorwaarden <a href="{{ route('terms.conditions') }}" class="underline">hier</a> terugvinden</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-3">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-3" aria-expanded="true" aria-controls="collapse-3">
                                Kan ik op de hoogte blijven als mijn favoriete Thuiskok een advertentie heeft geplaatst?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-3" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-3">
                        <div class="panel-body px-3 mb-4">
                            <p>Ja, dat kan door je in te schrijven op de profielpagina van de Thuiskok. Hier kun je je e-mailadres achterlaten en ontvang je een e-mail van DeBurenKoken zodra jouw favoriete Thuiskok een nieuwe advertentie plaatst.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-4">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-4" aria-expanded="true" aria-controls="collapse-4">
                                Hoe worden de prijzen bepaald?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-4" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-4">
                        <div class="panel-body px-3 mb-4">
                            <p>Het is aan de Thuiskok zelf om een prijs te bepalen voor de aangeboden gerechten. Hierdoor kunnen de prijzen voor soortgelijke gerechten variëren op DeBurenKoken.</p>
                        </div>
                    </div>
                </div>            
            </div>
        </div>

        <div class="container">
            <h2>Bestellen</h2>
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-5">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-5" aria-expanded="true" aria-controls="collapse-5">
                                Kan ik mijn bestelling annuleren?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-5" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-5">
                        <div class="panel-body px-3 mb-4">
                            <p>Je kunt een bestelling tot op de aangegeven tijd van de advertentie annuleren. Let wel op dat het annuleren van een advertentie kosten met zich mee kan brengen.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-6">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-6" aria-expanded="true" aria-controls="collapse-6">
                                De Thuiskok heeft mijn bestelling geannuleerd.
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-6" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-6">
                        <div class="panel-body px-3 mb-4">
                            <p>Helaas kan het voorkomen dat een Thuiskok een bestelling annuleert. Je krijgt van DeBurenKoken een mail hierover namens de Thuiskok.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-7">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-7" aria-expanded="true" aria-controls="collapse-7">
                                Kan ik mijn bestelling wijzigen?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-7" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-7">
                        <div class="panel-body px-3 mb-4">
                            <p>Het is niet mogelijk om een bestelling te wijzigen nadat de bestelling is ontvangen. Wel is het mogelijk om een bestelling te annuleren tot op de aangegeven tijd van de advertentie.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-8">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-8" aria-expanded="true" aria-controls="collapse-8">
                                Kan ik mijn contactgegevens aanpassen op de bestelling?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-8" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-8">
                        <div class="panel-body px-3 mb-4">
                            <p>Het is niet mogelijk om de contactgegevens aan te passen op een bestelling. Mocht het toch nodig zijn om je contactgegevens te wijzigen, raden wij het aan om zelf in contact te komen met de Thuiskok.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-9">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-9" aria-expanded="true" aria-controls="collapse-9">
                              Hoe kom ik in contact met een Thuiskok?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-9" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-9">
                        <div class="panel-body px-3 mb-4">
                            <p>Je ontvangt na een bestelling een e-mail met daarin de contactgegevens van de Thuiskok.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-10">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-10" aria-expanded="true" aria-controls="collapse-10">
                                Is het mogelijk om opmerkingen te plaatsen bij een bestelling?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-10" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-10">
                        <div class="panel-body px-3 mb-4">
                            <p>Ja, je kunt bij je bestelling een opmerking achter laten voor de Thuiskok. Let op, een Thuiskok is niet verplicht om aan verzoeken te voldoen geplaatst bij de opmerkingen.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-11">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-11" aria-expanded="true" aria-controls="collapse-11">
                                Kan ik een bestelling laten bezorgen?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-11" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-11">
                        <div class="panel-body px-3 mb-4">
                            <p>Nee, het is momenteel nog niet mogelijk om via DeBurenKoken.nl bestellingen te laten bezorgen. Echter, kan een Thuiskok in een advertentie aangeven om zelf bestellingen te bezorgen.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-12">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-12" aria-expanded="true" aria-controls="collapse-12">
                               Ik heb een klacht over een bestelling.
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-12" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-12">
                        <div class="panel-body px-3 mb-4">
                            <p>Wij raden het aan om bij een klacht over een bestelling eerst contact op te nemen met de Thuiskok. Als het niet lukt om samen tot een oplossing te komen voor de klacht, kun je je ervaring altijd delen in de recensies. Het is ook mogelijk om via het contactformulier de klacht met het team van DeBurenKoken te delen. Wij ontvangen dan graag de naam van Thuiskok, bestelling gegevens en een omschrijving van de klacht.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-13">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-13" aria-expanded="true" aria-controls="collapse-13">
                                Hoe beoordeel ik mijn bestelling?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-13" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-13">
                        <div class="panel-body px-3 mb-4">
                            <p>Je ontvangt een e-mail van DeBurenKoken met een link om de Thuiskok van je bestelling te beoordelen.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <h2>Betalingen</h2>
            <div class="panel-group" id="accordion3" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-14">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion3" href="#collapse-14" aria-expanded="true" aria-controls="collapse-14">
                                Welke betaalmogelijkheden zijn er?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-14" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-14">
                        <div class="panel-body px-3 mb-4">
                            <p>Betalingen op DeBurenkoken worden mogelijk gemaakt via Mollie Payments. Op dit moment is het enkel mogelijk om via iDEAL betalingen te doen voor bestellingen.</p>
                        </div>    
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-15">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-15" aria-expanded="true" aria-controls="collapse-15">
                                Hoe kan ik mijn betaling retour ontvangen?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-15" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-15">
                        <div class="panel-body px-3 mb-4">
                            <p> De betalingen op DeBurenKoken zijn volledig geautomatiseerd en hierdoor worden retourbetalingen in gang gezet zodra een bestelling via het platform wordt geannuleerd. Hiervoor gelden wel de voorwaarden om bestellingen te kunnen annuleren.   </p>
                        </div>    
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
