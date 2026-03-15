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
                                @if($errors->has('csrf'))
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i> {{ $errors->first('csrf') }}
                                    </div>
                                @endif
                                <h1 class="mt-4">Bestelling annuleren?</h1>
                                <p class="mt-20">
                                    Weet je zeker dat je de bestelling van {{$clientName}} wilt annuleren? 
                                    De klant ontvangt een e-mail en het betaalde bedrag wordt teruggestort. 
                                    Als je doorgaat met annuleren, kun je een boodschap toevoegen die naar 
                                    de klant gestuurd zal worden via e-mail.
                                </p>
                                
                                <form action="{{route('dashboard.orders.cancel.store', $orderId)}}" method="POST" id="cancelOrderForm">
                                    @csrf
                                    <div class="row">
                                        <div class="col-8 mx-auto">
                                            <label for="cancel"></label>
                                            <textarea name="cancel_text" id="cancel"></textarea>
                                        </div>
                                    </div>
                                    
                                    @if(isset($atCancellationLimit) && $atCancellationLimit)
                                    <div class="alert alert-warning mt-3 mb-3" style="background-color: #fff3cd; border: 1px solid #ffeeba; border-radius: 4px; color: #856404; padding: 10px; margin-top: 15px; margin-bottom: 15px; max-width: 600px; margin-left: auto; margin-right: auto;">
                                        <strong>Let op!</strong> Je hebt de grens voor gratis annuleren bereikt. 
                                        Er zullen transactiekosten van €0,60 in rekening gebracht worden voor deze geannuleerde bestelling.
                                    </div>
                                    @endif
                                    
                                    <div class="row mt-50">
                                        <div class="col-12 text-center">
                                            <a href="{{url()->previous()}}" class="btn btn-light">Terug</a>
                                            <button type="submit" id="cancelOrderButton" style='height: 44px; margin-bottom: 20px !important;' class="btn btn-outline btn-outline-mb-10-mobile">Bestelling annuleren</button>
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
    const cancelForm = document.getElementById('cancelOrderForm');
    const cancelButton = document.getElementById('cancelOrderButton');
    
    if (cancelForm && cancelButton) {
        let formSubmitted = false;
        
        cancelForm.addEventListener('submit', function(e) {
            if (!formSubmitted) {
                formSubmitted = true;
                cancelButton.disabled = true;
                cancelButton.innerHTML = 'Bezig met verwerken...';
                cancelButton.style.opacity = '0.7';
                cancelButton.style.cursor = 'not-allowed';
                
                // Re-enable na 2 seconden (voor het geval de submit faalt)
                setTimeout(() => {
                    if (formSubmitted) {
                        formSubmitted = false;
                        cancelButton.disabled = false;
                        cancelButton.innerHTML = 'Bestelling annuleren';
                        cancelButton.style.opacity = '1';
                        cancelButton.style.cursor = 'pointer';
                    }
                }, 2000);
            } else {
                e.preventDefault();
                return false;
            }
        });
    }
});
</script>
@endsection