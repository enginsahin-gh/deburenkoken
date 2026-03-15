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
            <h2>Praktische tips voor Thuiskoks</h2>
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-tip-0">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-tip-0" aria-expanded="true" aria-controls="collapse-tip-0">
                                Begin eenvoudig
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-tip-0" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-tip-0">
                        <div class="panel-body px-3 mb-4">
                            <p>Start met één of twee gerechten die je goed kent en vaak kookt. Dat geeft rust en zorgt voor kwaliteit. Gerechten die makkelijk op te warmen zijn, werken vaak het best.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-tip-1">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-tip-1" aria-expanded="true" aria-controls="collapse-tip-1">
                                Kies een herkenbaar gerecht
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-tip-1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-tip-1">
                        <div class="panel-body px-3 mb-4">
                            <p>Buurtbewoners kiezen sneller voor gerechten die ze kennen. Denk aan ovenschotels, stoof, soepen of pasta's. Je kunt later altijd variëren.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-tip-2">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-tip-2" aria-expanded="true" aria-controls="collapse-tip-2">
                                Maak een duidelijke foto
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-tip-2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-tip-2">
                        <div class="panel-body px-3 mb-4">
                            <p>Een goede foto helpt enorm. Gebruik daglicht, een rustige achtergrond en laat het gerecht zien zoals de klant het krijgt. Dit hoeft niet professioneel te zijn, zolang het maar eerlijk en duidelijk is.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-tip-3">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-tip-3" aria-expanded="true" aria-controls="collapse-tip-3">
                                Schrijf persoonlijk en duidelijk
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-tip-3" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-tip-3">
                        <div class="panel-body px-3 mb-4">
                            <p>Vertel kort wat je gerecht is en waarom je het graag kookt. Schrijf alsof je het aan een buur vertelt. Houd het simpel en eerlijk.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-tip-4">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-tip-4" aria-expanded="true" aria-controls="collapse-tip-4">
                                Wees duidelijk over porties
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-tip-4" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-tip-4">
                        <div class="panel-body px-3 mb-4">
                            <p>Geef een duidelijke indicatie van de portiegrootte. Dit voorkomt teleurstellingen en zorgt voor tevreden klanten.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-tip-5">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-tip-5" aria-expanded="true" aria-controls="collapse-tip-5">
                                Bepaal een passende prijs
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-tip-5" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-tip-5">
                        <div class="panel-body px-3 mb-4">
                            <p>Houd rekening met de kosten van ingrediënten en de tijd die je in het gerecht steekt. Kijk eventueel wat andere Thuiskoks op het platform vragen voor vergelijkbare gerechten.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-tip-6">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-tip-6" aria-expanded="true" aria-controls="collapse-tip-6">
                                Plan realistisch
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-tip-6" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-tip-6">
                        <div class="panel-body px-3 mb-4">
                            <p>Kies een bestelmoment en ophaaltijd die goed passen bij jouw agenda. Je bepaalt zelf hoeveel bestellingen je aankunt.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-tip-7">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-tip-7" aria-expanded="true" aria-controls="collapse-tip-7">
                                Deel je gerecht in je netwerk
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-tip-7" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-tip-7">
                        <div class="panel-body px-3 mb-4">
                            <p>Laat vrienden, familie en buurtgenoten weten dat je kookt via DeBurenKoken. Een bericht in een buurtapp of WhatsApp-groep kan al genoeg zijn.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-tip-8">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-tip-8" aria-expanded="true" aria-controls="collapse-tip-8">
                                Houd het leuk
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-tip-8" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-tip-8">
                        <div class="panel-body px-3 mb-4">
                            <p>Kook omdat je het leuk vindt. Je hoeft niet altijd beschikbaar te zijn en je zit nergens aan vast. Jij bepaalt het tempo.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
