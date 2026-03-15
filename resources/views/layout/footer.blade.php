<!-- FOOTER AREA START -->
<footer class="dbk-footer">
    <div class="container">
        <div class="dbk-footer-grid">
            <div class="dbk-footer-brand">
                <a href="{{route('home')}}"><img src="{{asset('img/logo.png')}}" alt="DeBurenKoken.nl" class="dbk-footer-logo"></a>
                <p>Geniet van thuisgekookte gerechten van jouw favoriete Thuiskok</p>
            </div>
            <div class="dbk-footer-links">
                <h4>DeBurenKoken.nl</h4>
                <ul>
                    <li><a href="{{route('info')}}">Over Ons</a></li>
                    <li><a href="{{route('contact')}}">Contact</a></li>
                    <li><a href="{{route('customer.facts')}}">Veelgestelde vragen</a></li>
                </ul>
            </div>
            <div class="dbk-footer-links">
                <h4>Regelgeving</h4>
                <ul>
                    <li><a href="{{route('terms.conditions')}}">Algemene voorwaarden</a></li>
                    <li><a href="{{route('privacy')}}">Privacy policy</a></li>
                    <li><a href="{{route('cookie')}}">Cookieverklaring</a></li>
                </ul>
            </div>
            <div class="dbk-footer-social">
                @if(env('SOCIAL_FACEBOOK') || env('SOCIAL_INSTAGRAM') || env('SOCIAL_TWITTER'))
                <div class="dbk-social-icons">
                    @if(env('SOCIAL_INSTAGRAM'))
                    <a href="{{ env('SOCIAL_INSTAGRAM') }}" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    @endif
                    @if(env('SOCIAL_FACEBOOK'))
                    <a href="{{ env('SOCIAL_FACEBOOK') }}" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
                    @endif
                    @if(env('SOCIAL_TWITTER'))
                    <a href="{{ env('SOCIAL_TWITTER') }}" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
                    @endif
                </div>
                @endif
            </div>
        </div>
        <div class="dbk-footer-bottom">
            <a href="javascript:void(0);" class="dbk-scroll-top" aria-label="Naar boven scrollen"><i class="fa fa-chevron-up"></i></a>
            <p>&copy; {{ date('Y') }} <a href="{{route('home')}}">DeBurenKoken</a>. Alle rechten voorbehouden.</p>
        </div>
    </div>
    @if (!isset($acceptedCookies) || !$acceptedCookies)
    @include('layout.accept-cookies')
    @endif
</footer>
<!-- FOOTER AREA END -->
