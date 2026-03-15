<header class="dbk-header">
    <div class="dbk-header-inner">
        <div class="container">
            <div class="dbk-header-row">
                <div class="dbk-logo">
                    <a href="{{route('home')}}"><img src="{{asset('img/logo.png')}}" alt="DeBurenKoken.nl"></a>
                </div>
                <nav class="dbk-nav">
                    <ul>
                        <li><a href="{{route('search.cooks')}}" class="{{ request()->routeIs('search.cooks*') ? 'dbk-nav-active' : '' }}"><i class="fa-solid fa-users"></i> Thuiskoks</a></li>
                        <li><a href="{{route('info')}}" class="{{ request()->routeIs('info') ? 'dbk-nav-active' : '' }}"><i class="fa-solid fa-circle-question"></i> Hoe werkt het?</a></li>
                        <li><a href="{{route('info')}}" class="{{ request()->routeIs('info') ? 'dbk-nav-active' : '' }}">Over Ons</a></li>
                        <li><a href="{{route('contact')}}" class="{{ request()->routeIs('contact') ? 'dbk-nav-active' : '' }}">Contact</a></li>
                    </ul>
                </nav>
                <div class="dbk-header-actions">
                    @guest
                    <a href="{{route('login.home')}}" class="dbk-login-link"><i class="fa fa-user"></i> Login</a>
                    <a href="{{route('register.info')}}" class="dbk-register-btn"><i class="fa-solid fa-utensils"></i> Registreer</a>
                    @elseauth
                        @role('cook')
                            <a href="{{route('dashboard.adverts.create')}}" class="dbk-register-btn"><i class="fa-solid fa-plus"></i> Plaats advertentie</a>
                            <a href="{{route('dashboard.adverts.active.home')}}" class="dbk-login-link"><i class="fa-solid fa-grid-2"></i> Mijn omgeving</a>
                        @endrole
                        @role('admin')
                            <a href="{{route('dashboard.admin.accounts')}}" class="dbk-login-link"><i class="fa-solid fa-grid-2"></i> Mijn omgeving</a>
                        @endrole
                    @endauth
                </div>
                <button class="dbk-hamburger" id="toggleMenu" aria-label="Menu openen">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Menu Overlay -->
<div class="dbk-mobile-overlay" id="mobileMenu">
    <div class="dbk-mobile-overlay-inner">
        <div class="dbk-mobile-header">
            <a href="{{route('home')}}"><img src="{{asset('img/logo.png')}}" alt="DeBurenKoken.nl" style="max-height: 40px;"></a>
            <button class="dbk-mobile-close" id="closeMobileMenu" aria-label="Menu sluiten">&times;</button>
        </div>
        <nav class="dbk-mobile-nav">
            <ul>
                <li><a href="{{route('home')}}">Home</a></li>
                <li><a href="{{route('search.cooks')}}"><i class="fa-solid fa-users"></i> Thuiskoks</a></li>
                <li><a href="{{route('info')}}"><i class="fa-solid fa-circle-question"></i> Hoe werkt het?</a></li>
                <li><a href="{{route('info')}}">Over Ons</a></li>
                <li><a href="{{route('contact')}}">Contact</a></li>
                @guest
                <li><a href="{{route('login.home')}}">Login</a></li>
                <li><a href="{{route('register.info')}}" class="dbk-mobile-cta">Registreer als Thuiskok</a></li>
                @elseauth
                    @role('cook')
                    <li><a href="{{route('dashboard.adverts.active.home')}}">Mijn omgeving</a></li>
                    <li><a href="{{route('dashboard.adverts.create')}}" class="dbk-mobile-cta">Plaats advertentie</a></li>
                    @endrole
                    @role('admin')
                    <li><a href="{{route('dashboard.admin.accounts')}}">Mijn omgeving</a></li>
                    @endrole
                @endauth
            </ul>
        </nav>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.getElementById('toggleMenu');
    const mobileMenu = document.getElementById('mobileMenu');
    const closeBtn = document.getElementById('closeMobileMenu');
    
    if (hamburger && mobileMenu) {
        hamburger.addEventListener('click', function() {
            mobileMenu.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }
    if (closeBtn && mobileMenu) {
        closeBtn.addEventListener('click', function() {
            mobileMenu.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
    // Close on link click
    mobileMenu?.querySelectorAll('a').forEach(function(link) {
        link.addEventListener('click', function() {
            mobileMenu.classList.remove('active');
            document.body.style.overflow = '';
        });
    });
});
</script>
