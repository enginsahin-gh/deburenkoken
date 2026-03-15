@if(!isset($acceptedCookies) || !$acceptedCookies)
            <form  action="{{ route('accepted.cookies.response') }}" method="POST">
                @csrf
                <div class="accept-cookie-content">
                    <p>Wij gebruiken cookies en andere technologieën om jouw gebruikerservaring te verbeteren, te personaliseren en analyses voor optimalisatie uit te kunnen voeren. Wij gebruiken hiervoor onze eigen cookies. Door op “accepteer” te klikken accepteer je het gebruik van alle cookies. Wij plaatsen altijd noodzakelijke cookies.</p>
                    <div class='cookies-button-container'>
                        <button type="submit" name="cookierights" value="decline">Essentiële</button>
                        <button type="submit" name="cookierights" value="accept">Accepteer</button>
                    </div>
                </div>            
            </form>
@endif
