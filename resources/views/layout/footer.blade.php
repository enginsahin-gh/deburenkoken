<!-- FOOTER AREA START -->
<footer class="ltn__footer-area">
    <div class="footer-top-area plr--5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-sm-6 col-12">
                    <div class="footer-widget footer-menu-widget clearfix">
                        <a href="{{route('home')}}"><img src="{{asset('img/logo.png')}}" alt="DeBurenKoken.nl"></a>
                        <p>Geniet van thuisgekookte gerechten<br/>van jouw favoriete Thuiskok</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-12">
                    <div class="footer-widget footer-menu-widget clearfix">
                        <h4 class="footer-title">DeBurenKoken.nl</h4>
                        <div class="footer-menu">
                            <ul>
                                <li><a href="{{route('info')}}"><i class="fa fa-chevron-right"></i> Over Ons</a></li>
                                <li><a href="{{route('contact')}}"><i class="fa fa-chevron-right"></i> Contact</a></li>
                                <li><a href="{{route('customer.facts')}}"><i class="fa fa-chevron-right"></i> Veelgestelde vragen</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="footer-widget footer-menu-widget clearfix">
                        <h4 class="footer-title">Regelgeving</h4>
                        <div class="footer-menu">
                            <ul>
                                <li><a href="{{route('terms.conditions')}}"><i class="fa fa-chevron-right"></i> Algemene voorwaarden</a></li>
                                <li><a href="{{route('privacy')}}"><i class="fa fa-chevron-right"></i> Privacy policy</a></li>
                                <li><a href="{{route('cookie')}}"><i class="fa fa-chevron-right"></i> Cookieverklaring</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-1 col-sm-6 col-12">
                    <div class="footer-widget footer-menu-widget social">
                        @if(env('SOCIAL_FACEBOOK') || env('SOCIAL_INSTAGRAM') || env('SOCIAL_TWITTER'))
                        <div class="footer-menu">
                            <ul>
                                @if(env('SOCIAL_INSTAGRAM'))
                                <li><a href="{{ env('SOCIAL_INSTAGRAM') }}" class="pl-3" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a></li>
                                @endif
                                @if(env('SOCIAL_FACEBOOK'))
                                <li><a href="{{ env('SOCIAL_FACEBOOK') }}" class="pl-3" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a></li>
                                @endif
                                @if(env('SOCIAL_TWITTER'))
                                <li><a href="{{ env('SOCIAL_TWITTER') }}" class="pl-3" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a></li>
                                @endif
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12 text-center bottom-footer">
                    <a href="javascript:void(0);" class="scrollTop" aria-label="Naar boven scrollen"><i class="fa fa-chevron-up"></i></a>
                    <p><a href="{{route('home')}}">DeBurenKoken</a> © Copyright 2026, All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </div>
    @if (!isset($acceptedCookies) || !$acceptedCookies)
    @include('layout.accept-cookies')
    @endif
</footer>
<!-- FOOTER AREA END -->
