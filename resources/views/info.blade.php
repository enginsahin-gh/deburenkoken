@extends('layout.main')
@section('content')
    <div class="page-header">
        <div class="container"><h1>Over Ons</h1></div>
    </div>
    <section class="accordion-section clearfix" aria-label="Question Accordions">
        <div class="container">
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-0">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-0" aria-expanded="true" aria-controls="collapse-0">
                                Wat is DeBurenKoken.nl
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-0" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-0">
                        <div class="panel-body px-3 mb-4">
                            <p>DeBurenKoken.nl is een platform waar thuiskoks hun passie voor koken delen via gezonde, smaakvolle en gevarieerde maaltijden. Wij maken huisgemaakte gerechten eenvoudig vindbaar, zodat iedereen snel kan genieten van goed eten zonder zelf te hoeven koken.</p>
                            <p>Voor klanten staat gemak centraal. Ontdek maaltijden in jouw buurt, koop online, betaal veilig en haal je gerecht op wanneer het jou uitkomt. Geen abonnementen, geen lange wachttijden, alleen vers gekookt eten, precies wanneer jij het nodig hebt.</p>
                            <p>Voor thuiskoks biedt DeBurenKoken een laagdrempelige manier om gerechten aan te bieden en bestellingen te ontvangen. Met een eigen online keuken, reviews van klanten en een eenvoudig betaalsysteem bouw je stap voor stap een vaste klantenkring op.</p>
                            <p>DeBurenKoken combineert kwaliteit, gemak en lokaal aanbod in één platform en maakt thuisgekookte maaltijden toegankelijk voor iedereen die lekker en bewust wil eten.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-1">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-1" aria-expanded="true" aria-controls="collapse-1">
                                Onze missie en visie
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-1">
                        <div class="panel-body px-3 mb-4">
                          <p>Missie: DeBurenKoken.nl brengt de passie en kennis van thuiskoks samen door gezonde, smakelijke en gevarieerde maaltijden zichtbaar te maken voor hun lokale gemeenschap. Wij creëren een platform waar buurtbewoners met liefde voor koken hun culinaire vaardigheden kunnen delen, zodat anderen kunnen genieten van eerlijke, voedzame en smaakvolle gerechten uit de buurt. Door deze verbindingen versterken we het gevoel van gemeenschap en dragen we bij aan een duurzamere en gezondere leefomgeving voor iedereen.</p>
                          <p>Visie: Onze visie is een wereld waarin iedereen toegang heeft tot gezonde, lekkere en gevarieerde thuisgekookte maaltijden, ongeacht achtergrond of situatie. Wij streven ernaar een platform te zijn waar de kracht van thuiskoken wordt benut om de buurt met elkaar te verbinden, voedselvoorziening te democratiseren en een positieve impact te hebben op gezondheid en welzijn. Door het mogelijk te maken dat thuiskoks hun maaltijden delen, creëren we een toegankelijke en duurzame oplossing voor de voedingsbehoeften van elke buurtbewoner.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-2">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-2" aria-expanded="true" aria-controls="collapse-2">
                                Hoe werkt DeBurenKoken.nl voor klanten
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-2">
                        <div class="panel-body px-3 mb-4">
                            <p>Via DeBurenKoken.nl ontdek je thuisgekookte gerechten uit jouw eigen omgeving. Blader door ons aanbod en laat je verrassen!</p>
                            <p>Heb je een gerecht gevonden dat je wilt bestellen? Plaats dan eenvoudig je bestelling met een paar klikken bij de Thuiskok in jouw buurt.</p>
                            <p>Haal je bestelling af en geniet van een thuisgekookte maaltijd!</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-3">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-3" aria-expanded="true" aria-controls="collapse-3">
                                Hoe werkt DeBurenKoken.nl voor thuiskoks
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-3" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-3">
                        <div class="panel-body px-3 mb-4">
                            <p>Wil je meer informatie? klik dan <a href="{{route('register.info')}}" class="text-decoration"> hier</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('style')
    <style>
        
    </style>
@endsection
