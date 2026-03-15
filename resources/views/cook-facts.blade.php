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
                                Hoe meld ik me aan bij DeBurenkoken?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-0" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-0">
                        <div class="panel-body px-3 mb-4">
                           <p>Je kunt je gratis en eenvoudig <a href="{{ route('register.info') }}" class="underline">hier</a> registreren.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-1">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-1" aria-expanded="true" aria-controls="collapse-1">
                                Hoe bepaal ik een prijs per portie?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-1">
                        <div class="panel-body px-3 mb-4">
                            <p>Bij het bepalen van de prijs voor een maaltijd als Thuiskok, moet je rekening houden met de kosten van ingrediënten, de tijd en moeite die je erin steekt. Vergelijk je prijzen met die van andere Thuiskoks in de regio en houd rekening met de portiegrootte en de gebruikte ingrediënten.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-2">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-2" aria-expanded="true" aria-controls="collapse-2">
                                Hoe geef ik mijn maaltijd mee?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-2">
                        <div class="panel-body px-3 mb-4">
                            <p>Je kunt als Thuiskok zelf bepalen hoe je een maaltijd meegeeft. Wij raden het wel sterk aan om dit van tevoren goed aan te geven bij je advertentie zodat het voor klanten geen verassing is.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-3">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-3" aria-expanded="true" aria-controls="collapse-3">
                                Hoe ontvang ik mijn betalingen?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-3" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-3">
                        <div class="panel-body px-3 mb-4">
                            <p>DeBurenKoken faciliteert online betalingen tussen Thuiskoks en klanten. Het bedrag wordt automatisch toegevoegd aan de portemonnee van de Thuiskok op hun account. Vanuit deze portemonnee kunnen Thuiskoks het bedrag vervolgens naar hun eigen betaalrekening uitbetalen.</p>
                        </div>
                    </div>
                </div>
                
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-4">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-4" aria-expanded="true" aria-controls="collapse-4">
                                Kan ik mijn bestelling annuleren?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-4" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-4">
                        <div class="panel-body px-3 mb-4">
                            <p>Bestellingen kunnen geannuleerd worden tot aan het uiterst bestelmoment.</p>
                        </div>
                    </div>
                </div>

             <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-20">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-20" aria-expanded="true" aria-controls="collapse-20">
                                Kan ik een overzicht krijgen met alle bestellingen?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-20" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-20">
                        <div class="panel-body px-3 mb-4">
                            <p>De Thuiskoks ontvangen na het verstrijken van de het uiterst bestelmoment een overzicht per mail met alle bestellingen die zijn ontvangen voor de desbetreffende advertentie. Daarnaast is het mogelijk voor Thuiskoks in ‘Mijn omgeving’ onder de tab bestellingen, alle bestellingen terug te vinden. Ook zijn de bestellingen gegroepeerd weergegeven bij de actieve advertenties.  </p>
                        </div>
                    </div>
                </div>
                
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-5">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-5" aria-expanded="true" aria-controls="collapse-5">
                                Hoe groot is een portie?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-5" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-5">
                        <div class="panel-body px-3 mb-4">
                          <p>Je kunt als Thuiskok zelf bepalen hoe groot een portie is. Om teleurstelling te voorkomen, raden wij het wel sterk aan om bij je advertentie aan een indicatie af te geven. Dit kan bijvoorbeeld doormiddel van een bijgevoegde afbeelding.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="container">
            <h2>Account instellingen</h2>
            <div class="panel-group" id="accordion1" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-6">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion1" href="#collapse-6" aria-expanded="true" aria-controls="collapse-6">
                                Ik ben mijn wachtwoord vergeten
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-6" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-6">
                        <div class="panel-body px-3 mb-4">
                            <p>Je kunt gemakkelijk via <a href="{{ route('login.forgot') }}" class="underline">hier</a> je wachtwoord opnieuw instellen.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-7">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-7" aria-expanded="true" aria-controls="collapse-7">
                               Ik wil mijn account verwijderen
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-7" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-7">
                        <div class="panel-body px-3 mb-4">
                            <p>Een Thuiskok account kan bij de instellingen onder de profiel pagina via de link "account verwijderen", verwijderd worden. Houd er echter rekening mee dat het niet mogelijk is om je account te verwijderen met openstaande bestellingen.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-8">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-8" aria-expanded="true" aria-controls="collapse-8">
                               Ik wil mijn persoonlijke gegevens aanpassen.
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-8" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-8">
                        <div class="panel-body px-3 mb-4">
                            <p>Je kunt als Thuiskok bij Instellingen en onder de tab Gegevens, je persoonlijke gegevens aanpassen.</p>
                        </div>
                    </div>
                </div>           
            </div>
        </div>


        <div class="container">
            <h2>Betalingen</h2>
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-9">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-9" aria-expanded="true" aria-controls="collapse-9">
                                Welke betaalmogelijkheden zijn er?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-9" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-9">
                        <div class="panel-body px-3 mb-4">
                                  <p>Betalingen op DeBurenkoken worden mogelijk gemaakt via Mollie Payments. Op dit moment is het enkel mogelijk om via iDEAL betalingen te doen voor bestellingen. </p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-10">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-10" aria-expanded="true" aria-controls="collapse-10">
                                Hoe kan ik mijn betaling retour ontvangen? 
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-10" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-10">
                        <div class="panel-body px-3 mb-4">
                                  <p>De betalingen op DeBurenKoken zijn volledig geautomatiseerd en hierdoor worden retourbetalingen in gang gezet zodra een bestelling via het platform wordt geannuleerd. Hiervoor gelden wel de voorwaarden om bestellingen te kunnen annuleren.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-11">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-11" aria-expanded="true" aria-controls="collapse-11">
                                Is het mogelijk om mijn bankgegevens te wijzigen? 
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-11" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-11">
                        <div class="panel-body px-3 mb-4">
                                  <p>Het is voor Thuiskoks mogelijk om dagelijks de bankgegevens aan te passen.  </p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-12">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-12" aria-expanded="true" aria-controls="collapse-12">
                                Waarom moet ik mijn bankrekening verifiëren?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-12" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-12">
                        <div class="panel-body px-3 mb-4">
                                  <p>Om betalingen van klanten direct aan de juiste Thuiskok te kunnen koppelen en misverstanden, fouten en fraude in een later stadium te voorkomen, is het van belang om de juiste bankgegevens van de Thuiskoks op te nemen in het betalingsproces. Hiervoor vragen wij de Thuiskoks om hun bankgegevens te verifiëren via een 1 cent-verificatie. </p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-13">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-13" aria-expanded="true" aria-controls="collapse-13">
                                Wat betekent saldo in behandeling?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-13" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-13">
                        <div class="panel-body px-3 mb-4">
                                  <p>Wij reserveren dit bedrag tot het uiterste afhaalmoment is verstreken. Op deze manier kunnen wij de Thuiskoks ontzorgen door het bedrag, indien nodig, terug te betalen.  </p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-14">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-14" aria-expanded="true" aria-controls="collapse-14">
                                Hoe ontvang ik de betalingen op mijn bankrekening?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-14" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-14">
                        <div class="panel-body px-3 mb-4">
                                  <p>DeBurenKoken maakt gebruik van een digitale portemonnee, die voor de Thuiskoks toegankelijk is via 'Mijn omgeving'. De betalingen zijn na het verstrijken van het uiterste afhaalmoment beschikbaar voor de Thuiskok en kunnen dagelijks worden uitbetaald.  </p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-30">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-30" aria-expanded="true" aria-controls="collapse-30">
                                Welke kosten betaal ik als Thuiskok op DeBurenKoken?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-30" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-30">
                        <div class="panel-body px-3 mb-4">
                                  <p>Je kunt je gratis aanmelden en gerechten aanbieden zonder vaste kosten. Zodra je een uitbetaling aanvraagt via je portemonnee, wordt 5% ingehouden op het uit te betalen bedrag. Met deze bijdrage zorgen we voor een veilig en goed werkend platform, de verwerking van betalingen en de verdere verbetering van de website.</p>
                        </div>
                    </div>
                </div>
                    
                </div>    
            </div>

        <div class="container">
            <h2>DAC7 en DeBurenKoken</h2>
            <div class="panel-group" id="accordion1" role="tablist" aria-multiselectable="true">
                
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-15">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion1" href="#collapse-15" aria-expanded="true" aria-controls="collapse-15">
                                Wat houdt de DAC7-regelgeving in voor gebruikers van DeBurenKoken? 
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-15" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-15">
                        <div class="panel-body px-3 mb-4">
                            <p>Sinds 1 januari 2023 zijn online platforms wettelijk verplicht om gegevens te verzamelen over gebruikers die via het platform verkopen. Dit geldt voor alle online platforms, zoals DeBurenKoken, Marktplaats en Vinted. Wanneer je boven bepaalde drempelbedragen of aantallen transacties uitkomt, zijn deze platforms verplicht om jouw gegevens te delen met de Belastingdienst.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-16">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-16" aria-expanded="true" aria-controls="collapse-16">
                               Moet ik belasting betalen als ik maaltijden verkoop via DeBurenKoken? 
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-16" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-16">
                        <div class="panel-body px-3 mb-4">
                            <p>Niet automatisch. Als je maaltijden hobbymatig verkoopt, val je meestal buiten de belastingplicht. Op de website van de <a href="https://www.belastingdienst.nl/wps/wcm/connect/nl/werk-en-inkomen/content/inkomstenbelasting-betalen-over-internetverkopen" target="_blank" class="underline">Belastingdienst</a> lees je meer hierover.</p>
                            <p>Op het moment dat je structureel verkoopt en daarbij winst kunt verwachten, kan er sprake zijn van een bron van inkomen. In dat geval moeten de inkomsten mogelijk worden opgegeven bij de Belastingdienst. Meer informatie over wanneer sprake is van een bron van inkomen vind je <a href="https://www.belastingdienst.nl/wps/wcm/connect/bldcontentnl/belastingdienst/zakelijk/winst/inkomstenbelasting/wanneer_bent_u_ondernemer_voor_de_inkomstenbelasting/bron-van-inkomen" target="_blank" class="underline">hier</a></p>
                            <p>Twijfel je of jouw activiteiten als hobbymatig worden gezien door de Belastingdienst? Dan kun je dit rechtstreeks voorleggen aan de Belastingdienst via <a href="https://www.belastingdienst.nl/wps/wcm/connect/nl/contact/contact" target="_blank" class="underline">hier</a>.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-17">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-17" aria-expanded="true" aria-controls="collapse-17">
                               Wanneer vraagt DeBurenKoken mij om extra informatie?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-17" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-17">
                        <div class="panel-body px-3 mb-4">
                            <p>Deburenkoken moet je om aanvullende gegevens vragen zodra je:</p>
                                <ul class="terms-list mb-3">
 	                                <li>30 of meer verkooptransacties hebt gedaan in één jaar, of </li>
                                    <li>In totaal voor meer dan €2000 hebt verkocht binnen één kalenderjaar.</li>
                                </ul>
                        </div>
                    </div>
                </div>  

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-18">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-18" aria-expanded="true" aria-controls="collapse-18">
                               Wat gebeurt er als ik mijn gegevens nog niet heb aangeleverd? 
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-18" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-18">
                        <div class="panel-body px-3 mb-4">
                            <p>Zonder deze gegevens kan DeBurenKoken een aantal diensten tijdelijk stopzetten. Wij zullen je informeren op het moment dat dit van toepassing is en je nogmaals verzoeken de gegevens aan te leveren. Indien dit niet gebeurt, kunnen lopende en nieuwe betalingen tijdelijk worden opgeschort en pas worden verwerkt nadat je de informatie hebt aangeleverd.</p>
                        </div>
                    </div>
                </div>  

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-19">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-19" aria-expanded="true" aria-controls="collapse-19">
                               Wat voor gegevens moet ik aanleveren? 
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-19" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-19">
                        <div class="panel-body px-3 mb-4">
                            <p>Dat hangt af van je type gebruik. Hobbymatige koks geven naam, adres, geboortedatum, BSN en eventueel een btw-nummer op.</p>
                            <p>Zakelijke Thuiskoks moeten o.a. hun bedrijfsgegevens, KvK-nummer, btw-nummer en informatie over hun vestiging binnen de EU opgeven.</p>
                        </div>
                    </div>
                </div>                  

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-20">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-20" aria-expanded="true" aria-controls="collapse-20">
                               Wat gebeurt er als ik deze gegevens helemaal niet invul?  
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-20" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-20">
                        <div class="panel-body px-3 mb-4">
                            <p>Zolang je je gegevens niet aanlevert, blijven bepaalde functies van je account geblokkeerd. Uitbetalingen worden opgeschort en je kunt de platformdiensten tijdelijk niet gebruiken. Zodra je de gevraagde gegevens aanlevert, wordt alles weer hersteld. </p>
                        </div>
                    </div>
                </div>   

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-21">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-21" aria-expanded="true" aria-controls="collapse-21">
                               Wie vraagt mijn gegevens op en wat gebeurt daarmee? 
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-21" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-21">
                        <div class="panel-body px-3 mb-4">
                            <p>De gegevens worden verzameld via DeBurenKoken. Alle gegevens worden verwerkt volgens de geldende privacywetgeving in Nederland. Gegevens zoals je BSN worden uitsluitend gebruikt voor de verplichte rapportage aan de Belastingdienst in het kader van DAC7. </p>
                        </div>
                    </div>
                </div>       

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-22">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-22" aria-expanded="true" aria-controls="collapse-22">
                               Hoe weet ik dat ik gegevens moet aanleveren? 
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-22" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-22">
                        <div class="panel-body px-3 mb-4">
                            <p>Als je gegevens moet aanleveren of aanvullen, ontvang je hierover automatisch een bericht via e-mail en/of via meldingen in je account. </p>
                        </div>
                    </div>
                </div>        

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-23">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-23" aria-expanded="true" aria-controls="collapse-23">
                               Wat als ik vragen heb over mijn belastingpositie? 
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-23" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-23">
                        <div class="panel-body px-3 mb-4">
                            <p>DeBurenKoken mag geen persoonlijk belastingadvies geven. Voor vragen over of je belasting moet betalen, kun je het beste contact opnemen met de Belastingdienst of de BelastingTelefoon.</p>
                        </div>
                    </div>
                </div>      
      
            </div>
        </div>

        <div class="container">
            <h2>Voedselveiligheid </h2>
            <div class="panel-group" id="accordion1" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-24">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion1" href="#collapse-24" aria-expanded="true" aria-controls="collapse-24">
                                Waarom is veilig en hygiënisch werken zo belangrijk? 
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-24" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-24">
                        <div class="panel-body px-3 mb-4">
                            <p>Door veilig en hygiënisch te werken verklein je de kans dat mensen ziek worden van het eten dat je bereidt. Schoon werken, juiste bewaartemperaturen en duidelijke afspraken dragen allemaal bij aan een veilige maaltijd. </p>
                            <p>Daarnaast is het belangrijk dat je mensen goed informeert over allergenen in je gerechten, zoals pinda’s, noten of gluten. Voor mensen met een voedselallergie kan het ontbreken van deze informatie ernstige gevolgen hebben. Door hier zorgvuldig mee om te gaan, zorg je ervoor dat iedereen met een gerust gevoel van de maaltijd kan genieten.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-25">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-25" aria-expanded="true" aria-controls="collapse-25">
                               Hoe houd ik rekening met voedselveiligheid als Thuiskok? 
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-25" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-25">
                        <div class="panel-body px-3 mb-4">
                            <p>Als je vanuit huis maaltijden bereidt voor anderen, is het belangrijk dat je volgens de geldende hygiëne en voedselveiligheidsrichtlijnen werkt. Deze richtlijnen beschrijven hoe levensmiddelen veilig worden bereid, verpakt en bewaard. Het is niet nodig om zelf richtlijnen op te stellen; je kunt werken volgens een bestaande, door de branche goedgekeurde hygiënecode, zoals die van <a href="https://khn.nl/kennis/hygienecode" target="_blank" class="underline">Koninklijke Horeca Nederland</a>.</p>
                            <p>Meer informatie over een HACCP-voedselveiligheidsplan vind je <a href="https://www.nvwa.nl/onderwerpen/voedselveilig-werken-in-horeca-ambacht-en-retail/hygienecode" target="_blank" class="underline">hier</a>.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-26">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse-26" aria-expanded="true" aria-controls="collapse-26">
                               Welke richtlijnen helpen mij om voedselveilig te werken? 
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-26" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-26">
                        <div class="panel-body px-3 mb-4">
                            <p>De belangrijkste HACCP-richtlijnen voor werken in de keuken zijn overzichtelijk uitgewerkt op de website van de Nederlandse Voedsel- en Warenautoriteit (NVWA). Daar vind je praktische informatie over onderwerpen zoals hygiënisch werken, het voorkomen van kruisbesmetting, het beheersen van temperaturen en het veilig bewaren van voedsel. Deze richtlijnen helpen om voedselveiligheid structureel te borgen bij het bereiden van maaltijden. Lees <a href="https://www.nvwa.nl/onderwerpen/voedselveilig-werken-in-horeca-ambacht-en-retail/hoe-werk-ik-veilig/schoon-en-veilig" target="_blank" class="underline">hier</a> meer</p>
                            <p>Onderstaand stappenplan geeft een aantal voorbeelden van hoe deze richtlijnen in de praktijk kunnen worden toegepast: </p>
                            <p>1. Controleer temperaturen met een voedselthermometer </p>
                            <p>Temperatuur is cruciaal voor voedselveiligheid. Gebruik daarom een digitale steekthermometer om te controleren of eten goed gekoeld of voldoende verhit is. Alleen afgaan op het display van de koelkast is niet voldoende.</p>
                                <ul class="terms-list mb-3">
 	                                <li>Bederfelijke producten bewaar je onder de 7°C</li>
                                    <li>Kip en andere gevogelteproducten verhit je tot minimaal 75°C</li>
                                </ul>                            
                            <p>Meer informatie over het bewaren van voedsel bij de juiste temperatuur vind je <a href="https://www.nvwa.nl/onderwerpen/voedselveilig-werken-in-horeca-ambacht-en-retail/hoe-werk-ik-veilig/schoon-en-veilig" target="_blank" class="underline">hier</a>.</p>
                            <p>2. Bewaar zelfbereid voedsel niet te lang </p>
                            <p>Hoe lang je eten veilig kunt bewaren, hangt af van de temperatuur en de hygiënecode die je volgt. Werk je volgens de hygiënecode van KHN, dan gelden de volgende richtlijnen:</p>
                                <ul class="terms-list mb-3">
 	                                <li>Bij een bewaartemperatuur tussen 4°C en 7°C: maximaal 2 dagen</li>
                                    <li>Bij een bewaartemperatuur onder 4°C: maximaal 3 dagen</li>
                                </ul>   
                            <p>Langer bewaren vergroot de kans op bacteriegroei en kan mensen ziek maken.</p>
                            <p>3. Voorkom kruisbesmetting </p>
                            <p>Gebruik altijd aparte snijplanken voor rauwe en bereide producten. Zo voorkom je dat bacteriën van bijvoorbeeld rauw vlees op andere voedingsmiddelen terechtkomen. Was daarnaast regelmatig en zorgvuldig je handen tijdens het koken. </p>
                            <p>4. Houd je keuken afgesloten tijdens het koken </p>
                            <p>Zorg dat je keuken afgesloten kan worden wanneer je bezig bent met het bereiden van maaltijden. Zo voorkom je dat huisdieren of huisgenoten onbedoeld in contact komen met het eten. </p>
                            <p>5. Zorg voor een schone werkplek </p>
                            <p>Een schone keuken is essentieel. Maak werkbladen, apparatuur en keukengerei goed schoon en doe dit ook tussen privégebruik en koken voor anderen. </p>
                            <p>6. Scheid eten voor anderen en voor jezelf </p>
                            <p>Houd duidelijk onderscheid tussen voedsel dat je gebruikt voor DeBurenKoken.nl en voedsel voor privégebruik. Dit kan bijvoorbeeld door aparte opbergplekken te gebruiken of producten duidelijk te labelen.</p>
                            <p>7. Informeer over allergenen </p>
                            <p>Mensen moeten weten welke allergenen in een maaltijd zitten. Denk aan gluten, noten, pinda’s of lactose. Als Thuiskok ben je verplicht om deze informatie duidelijk te delen met degene voor wie je kookt.</p>              
                        </div>
                    </div>
                </div>   

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-27">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion1" href="#collapse-27" aria-expanded="true" aria-controls="collapse-27">
                                Hoe vermeld ik allergenen van mijn maaltijden? 
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-27" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-27">
                        <div class="panel-body px-3 mb-4">
                            <p>Volgens de richtlijnen van de NVWA, moeten klanten geïnformeerd worden over 14 allergenen. Dit zijn: glutenbevattende granen, ei, vis, pinda, noten, soja, melk (inclusief lactose), schaaldieren, weekdieren, selderij, mosterd, sesamzaad, sulfiet en lupine. Zorg dat je weet welke allergenen in het voedsel zitten dat je bereidt of verkoopt. De volledige lijst met allergenen en toelichting zijn opgenomen op de website van de <a href="https://www.nvwa.nl/onderwerpen/allergenen/over-welke-allergenen-moet-ik-informatie-geven" target="_blank" class="underline">NVWA</a>. </p>
                            <p>Je kunt als referentie ook de <a href="https://www.nvwa.nl/onderwerpen/voedselveilig-werken-in-horeca-ambacht-en-retail/documenten/consument/eten-drinken-roken/allergenen/flyers/flyer-vermelden-van-allergeneninformatie" target="_blank" class="underline">flyer</a> van het NVWA gebruiken voor meer informatie over het vermelden van allergenen </p>
                            <p>Weet je het niet helemaal zeker, dan kun je aangeven dat een gerecht mogelijk allergenen bevat. Houd er wel rekening mee dat dit voor sommige mensen een reden kan zijn om het gerecht niet te kiezen.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-28">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse" data-parent="#accordion1" href="#collapse-28" aria-expanded="true" aria-controls="collapse-28">
                                Heb ik een NVWA registratie nodig? 
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-28" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-28">
                        <div class="panel-body px-3 mb-4">
                            <p>De Nederlandse Voedsel- en Warenautoriteit (NVWA) is de overheidsinstantie die toezicht houdt op voedselveiligheid in Nederland. De NVWA controleert of levensmiddelen veilig worden bereid en verhandeld en of wordt voldaan aan de geldende wet- en regelgeving. Bij registratie kan de NVWA-toezicht en controles uitvoeren.</p>
                            <p>Thuiskoks die regelmatig maaltijden aanbieden, kosten maken met als doel winst te behalen en een vaste klantenkring opbouwen, kunnen worden gezien als ondernemers. Volgens de Kamer van Koophandel is er dan sprake van een onderneming en is een inschrijving bij de KvK verplicht.</p>
                            <p>Met een KvK-inschrijving ontstaat ook de verplichting om je aan te melden bij de NVWA, omdat je keuken dan wordt beschouwd als een levensmiddelenbedrijf.</p>
                            <p>Twijfel je of je je moet inschrijven bij de KvK? Dan kun je dit controleren op de website van de <a href="https://www.kvk.nl/starten/moet-ik-mijn-bedrijf-inschrijven-bij-kvk/" target="_blank" class="underline">Kamer van Koophandel</a>.</p>
                        </div>
                    </div>
                </div>                

                
            </div>
        </div>

        
    </section>
@endsection
