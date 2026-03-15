<header class="ltn__header-area ltn__header-5 header-spacer ltn__header-logo-and-mobile-menu-in-mobile ltn__header-transparent--- gradient-color-4--- ">
    <div class="ltn__header-middle-area ltn__header-sticky ltn__sticky-bg-white plr--5">
        <div class="container">
            <div class="row">
                <div class="col mobileHeaderCol">
                    <div class="site-logo-wrap">
                        <div class="site-logo">
                            <a href="{{route('home')}}"><img src="{{asset('img/logo.png')}}" alt="DeBurenKoken.nl"></a>
                        </div>
                    </div>
                    <button id="toggleMenu" class="mobile-only" aria-label="Menu openen"><i class="fa-solid fa-bars"></i></button>
                </div>
                <div class="col header-menu-column">
                    <div class="header-menu d-xl-block">
                        <nav>
                            <div class="ltn__main-menu">
                                <ul>
                                    <li><a href="{{route('info')}}">Over Ons</a></li>
                                    <li><a href="{{route('contact')}}">Contact</a></li>
                                    <li>
                                        @guest
                                        <a href="{{route('register.info')}}" class="red-round-border"><i class="fa-solid fa-utensils"></i> Registreer & plaats gerecht</a>
                                        @elseauth
                                            @role('cook')
                                                <a href="{{route('dashboard.adverts.create')}}" class="red-round-border"><i class="fa-solid fa-plus"></i> Plaats advertentie</a>
                                            @endrole
                                        @endauth
                                    </li>
                                    <li>
                                        @guest
                                        <a href="{{route('login.home')}}" class="login-menu-item"><i class="fa fa-user"></i> Login als Thuiskok</a>
                                        @elseauth
                                            @role('cook')
                                                <a href="{{route('dashboard.adverts.active.home')}}"><i class="fa-solid fa-grid-2"></i> Mijn omgeving</a>
                                            @endrole
                                            @role('admin')
                                                <a href="{{route('dashboard.admin.accounts')}}"><i class="fa-solid fa-grid-2"></i> Mijn omgeving</a>
                                            @endrole
                                        @endauth
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
