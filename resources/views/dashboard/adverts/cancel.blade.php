@extends('layout.dashboard')

@section('dashboard')
<style>
    button[type="submit"]:disabled {
        opacity: 0.7 !important;
        cursor: not-allowed !important;
        background-color: #ccc !important;
        border-color: #aaa !important;
        pointer-events: none !important;
    }
</style>

<section class="clearfix mt-3">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="center-box">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h1 class="mt-4">Advertentie annuleren?</h1>
                            <p class="mt-20">
                                Weet je zeker dat je {{$advert->dish->getTitle()}} - {{$advert->getParsedAdvertUuid()}} 
                                wilt annuleren, er zijn {{$activeOrders}} bestellingen actief? 
                                De klanten die een bestelling hebben geplaatst zullen een mail ontvangen en hun geld zal 
                                terug gestort worden. Indien je wilt doorgaan met annuleren kun je een boodschap plaatsen 
                                die via de mail naar je klanten verstuurd zal worden.
                            </p>
                            
                            <form action="{{route('dashboard.adverts.cancel.store', $advert->getUuid())}}" method="POST" id="cancelAdvertForm">
                                @csrf
                                <div class="row">
                                    <div class="col-8 mx-auto">
                                        <label for="cancel"></label>
                                        <textarea name="cancel_text" id="cancel"></textarea>
                                    </div>
                                </div>
                                
                           @if(isset($willExceedLimit) && $willExceedLimit)
                                    <div class="alert alert-warning mt-3 mb-3" style="background-color: #fff3cd; border: 1px solid #ffeeba; border-radius: 4px; color: #856404; padding: 10px; margin-top: 15px; margin-bottom: 15px; max-width: 600px; margin-left: auto; margin-right: auto;">
                                        <strong>Let op!</strong> De grens voor gratis annuleren ({{$cancellationLimit}} bestellingen per maand) is bereikt. 
                                        Er zullen transactiekosten van €0,60 in rekening gebracht worden per geannuleerde bestelling.
                                        @if(isset($ordersOverLimit) && $ordersOverLimit > 0)
                                        <br>
                                        <br>
                                        Bij deze annulering zal er {{$ordersOverLimit}} × €0,60 = €{{number_format($ordersOverLimit * 0.60, 2, ',', '.')}} in rekening worden gebracht.
                                        @endif
                                    </div>
                                    @endif            
                                <div class="row mt-50">
                                    <div class="col-12 text-center">
                                        <a href="{{url()->previous()}}" class="btn btn-light">Terug</a>
                                        <button type="submit" id="cancelAdvertButton" style='height: 44px; margin-bottom: 20px !important;' class="btn btn-outline btn-outline-mb-10-mobile">Advertentie annuleren</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('cancelAdvertForm');
    const button = document.getElementById('cancelAdvertButton');
    let submitted = false;
    
    if (form && button) {
        form.addEventListener('submit', function(e) {
            if (submitted) {
                e.preventDefault();
                return false;
            }
            
            submitted = true;
            button.disabled = true;
            button.innerHTML = 'Bezig met verwerken...';
            button.style.opacity = '0.7';
            button.style.cursor = 'not-allowed';
            
            return true;
        });
        
        // Extra protection tegen double-click
        button.addEventListener('click', function(e) {
            if (submitted) {
                e.preventDefault();
                return false;
            }
        });
    }
});
</script>
@endsection