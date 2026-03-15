@extends('layout.main')

@section('content')
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
                               @if(session()->has('error'))
                                   <div class="alert alert-danger">
                                       {{ session()->get('error') }}
                                   </div>
                               @endif
                               <h1 class="mt-4">Bestelling annuleren</h1>

                               @if ($cancellable)
                                   <p class="mt-20">
                                       Je hebt aangegeven de bestelling met {{$order->getParsedOrderUuid()}} te willen annuleren. 
                                       Klopt dit? Door op de knop hieronder te drukken, kun je dit bevestigen. 
                                       Na annulering wordt je betaling teruggestort op je rekening.
                                   </p>

                                   <form action="{{ route('submit.customer.cancel.order', [$order->getUuid(), $key]) }}" method="POST" id="customerCancelForm">
                                       @csrf
                                       <div class="row">
                                           <div class="col-8 mx-auto">
                                               <label for="cancel_text">Annuleringsbericht</label>
                                               <textarea name="cancel_text" id="cancel_text" class="form-control" rows="4" required></textarea>
                                           </div>
                                       </div>
                                       
                                       <!-- Melding nu hier, onder het tekstveld maar boven de annuleerknop -->
                                       @if(isset($cancellationWarning) && $cancellationWarning)
                                        <div class="row">
                                            <div class="col-8 mx-auto">
                                                <div class="alert alert-warning mt-3 mb-3">
                                                   <p><strong>Let op:</strong> Omdat je de grens van kosteloos annuleren hebt overschreden zijn we genoodzaakt om transactiekosten van €0.60 in rekening te brengen, dit wordt in vermindering gebracht op het terug te betalen bedrag.</p>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                       
                                       <div class="row mt-50">
                                           <div class="col-12 text-center">
                                               <button type="submit" id="customerCancelButton" class="btn btn-light col-3">Annulering bevestigen</button>
                                           </div>
                                       </div>
                                   </form>
                               @else
                                   <p class="mt-20">
                                       Je hebt aangegeven dat je de bestelling {{$order->getParsedOrderUuid()}} wilt annuleren. 
                                       Dit is helaas niet meer mogelijk via ons. Neem contact op met je thuiskok om de opties te bespreken!
                                   </p>
                                   <div class="row mt-50">
                                       <div class="col-12 text-center">
                                           <a href="{{ route('home') }}" class="btn btn-light col-3">Naar de website</a>
                                       </div>
                                   </div>
                               @endif
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const customerCancelForm = document.getElementById('customerCancelForm');
    const customerCancelButton = document.getElementById('customerCancelButton');
    
    if (customerCancelForm && customerCancelButton) {
        let formSubmitted = false;
        
        customerCancelForm.addEventListener('submit', function(e) {
            if (!formSubmitted) {
                e.preventDefault(); // Stop de submit tijdelijk
                formSubmitted = true;
                
                // Visuele feedback direct tonen
                customerCancelButton.disabled = true;
                customerCancelButton.innerHTML = 'Bezig met verwerken...';
                customerCancelButton.style.opacity = '0.7';
                customerCancelButton.style.pointerEvents = 'none';
                customerCancelButton.style.cursor = 'not-allowed';
                
                // Submit na korte delay zodat gebruiker de tekst kan zien
                setTimeout(() => {
                    customerCancelForm.submit();
                }, 300);
                
                return false;
            } else {
                e.preventDefault();
                return false;
            }
        });
    }
});
</script>
@endsection