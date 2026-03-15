@extends('layout.main')

@section('style')
@endsection
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<script src="https://kit.fontawesome.com/770d02fc1e.js" crossorigin="anonymous"></script>
<style>
.sidenav {
    background-color: transparent;
    transition: all 0.3s ease;
    overflow-y: hidden; /* Fix for admin scrollbar */
}

.dashboard-item {
    position: relative;
    background-color: transparent;
    margin-bottom: 1rem; /* Increased spacing between all menu items */
}

.dashboard-item a {
    position: relative;
    padding-right: 2rem;
    color: #333;
    transition: all 0.3s ease;
    border-radius: 8px;
}

/* Hover effect for direct links without dropdown */
.dashboard-item:not(:has(.second)) > a:hover {
    background-color: white !important;
    color: #ff6b35 !important;
    border-radius: 8px;
}

/* Hover effect for parent items with dropdowns */
.dashboard-item:has(.second) > a:hover {
    background-color: white !important;
    color: #ff6b35 !important;
    border-radius: 8px;
}

/* Apply white background to direct links without .second when active */
.dashboard-item:not(:has(.second)) > a.active {
    background-color: white !important;
    color: #ff6b35 !important;
    border-radius: 8px;
}

/* Remove white background for parent items with dropdowns when active */
.dashboard-item:has(.second) > a.active {
    background-color: transparent !important;
    color: #333 !important;
    border-radius: 8px;
    margin-bottom: 15px !important;
}

/* Ensure hover effect works even when a sub-item is active */
.dashboard-item:has(.second) > a:hover {
    background-color: white !important;
    color: #ff6b35 !important;
    border-radius: 8px;
}

.dashboard-item a {
    position: relative;
    padding-right: 2rem;
    color: #333;
    transition: all 0.3s ease;
    border-radius: 8px;
    background-color: transparent;
}

.dashboard-item .second {
    display: none;
    background-color: transparent !important;
    padding-left: 0;
    margin-top: 0.75rem; /* Increased spacing between main item and its dropdown */
}

.dashboard-item.open .second {
    display: block;
    background-color: transparent !important;
}

.dashboard-item .second a {
    padding: 0.75rem 1rem;
    margin: 0.5rem 0; /* Increased spacing between subcategory items */
    display: block;
    border-radius: 8px;
    transition: all 0.3s ease;
    background-color: transparent !important;
}

/* Apply white background to active sub-items */
.dashboard-item .second a.active {
    background-color: white !important;
    color: #ff6b35 !important;
    border-radius: 8px;
}

/* Hover effect for sub-items in dropdown */
.dashboard-item .second a:hover {
    background-color: white !important;
    color: #ff6b35 !important;
    border-radius: 8px;
}

.admin-menu .second a.active {
    background-color: white !important;
    color: #ff6b35 !important;
    border-radius: 8px;
}

/* Update hover/active states */
.sidenav a.active, .sidenav a:hover, .sidenav a:focus {
    color: #333;
}

.sidenav .second a {
    padding: 8px 5px 8px 65px !important;
    margin-top: 5px !important;
}

.dashboard-item > a {
    background-color: transparent;
    padding: 0.75rem 1rem;
    margin: 0.25rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.dashboard-item:has(.second) > a::after {
    content: '▼';
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%) rotate(0deg);
    font-size: 0.6rem;
    color: inherit;
    transition: transform 0.3s ease;
}

.dashboard-item.open > a::after {
    transform: translateY(-50%) rotate(180deg);
}

.second {
    display: none;
    background-color: transparent !important;
}

.dashboard-item.open .second {
    display: block;
}

.dashboard-item .second a {
    border: none !important;
    background-color: transparent !important;
    border-radius: 8px;
}

.admin-menu .second {
    display: block !important;
}

.admin-menu .second a {
    background-color: transparent !important;
    border: none !important;
    border-radius: 8px;
}

.admin-menu .second a.active {
    background-color: white !important;
    color: #ff6b35 !important;
    border-radius: 8px;
}

.admin-menu .dashboard-item a::after {
    display: none;
}

.no-transition * {
    transition: none !important;
}

/* Updated logout button styling */
.ico-logout {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    margin: 2rem 0 0.25rem 0; /* Added more top margin to separate from menu items */
    color: #333;
    text-decoration: none;
    transition: all 0.3s ease;
    border-radius: 8px;
    margin-top: auto;
    background-color: transparent;
}

/* Fixed hover effect for logout button */
.ico-logout:hover {
    background-color: white !important;
    color: #ff6b35 !important;
}

/* Override any conflicting hover styles */
.sidenav .ico-logout:hover {
    background-color: white !important;
    color: #ff6b35 !important;
}

.dashboard-item a img:first-of-type {
    display: block;
}

.dashboard-item a img:last-of-type {
    display: none;
}

/* For items without dropdowns that are active, show active icon */
.dashboard-item:not(:has(.second)) > a.active img:first-of-type {
    display: none;
}

.dashboard-item:not(:has(.second)) > a.active img:last-of-type {
    display: block;
}

/* For items with dropdowns, handle icon states */
.dashboard-item:has(.second) > a img:first-of-type {
    display: block !important;
}

.dashboard-item:has(.second) > a img:last-of-type {
    display: none !important;
}

/* Remove focus styles */
.dashboard-item a:focus, .dashboard-item a:focus-visible {
    outline: none;
    background-color: transparent !important;
    color: inherit !important;
}

.dashboard-item .second a:focus, .dashboard-item .second a:focus-visible {
    outline: none;
    background-color: transparent !important;
    color: inherit !important;
}

/* Basisstijlen voor alle meldingen */
.message {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
    font-weight: 500;
    display: block;
    width: 100%;
}

.message .content {
    font-size: 16px;
}

/* Override voor specifieke berichttypen */
.message.error {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

.message.success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.fade-out {
    animation: fadeOut 0.5s forwards;
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; visibility: hidden; }
}

/* Mobiele tutorial styling */
.mobile-tutorial-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.7);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
}

.mobile-tutorial-modal {
    background-color: white;
    border-radius: 8px;
    width: 85%;
    max-width: 350px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.mobile-tutorial-content {
    padding: 20px;
    text-align: center;
}

.mobile-tutorial-dots {
    display: flex;
    justify-content: center;
    margin: 10px 0;
}

.mobile-tutorial-dot {
    height: 8px;
    width: 8px;
    margin: 0 5px;
    border-radius: 50%;
    background-color: #ccc;
}

.mobile-tutorial-dot.active {
    background-color: #ff6b35;
}

.mobile-tutorial-buttons {
    display: flex;
    justify-content: flex-end;
    padding: 15px 20px;
    border-top: 1px solid #eee;
}

.mobile-tutorial-button {
    padding: 8px 16px;
    background-color: #ff6b35;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
}

.mobile-tutorial-button:disabled {
    background-color: #ccc;
}

.mobile-tutorial-button.prev {
    margin-right: 10px;
    background-color: #f0f0f0;
    color: #333;
}
</style>
@if(isset($firsttime) && $firsttime || isset($secondtime) && $secondtime || isset($hideMenu) && $hideMenu)
@else
    @section('sidebar')
    <body class="no-transition">
        <button id="sidenavMenu" class="mobile-only"><i class="fa-solid fa-bars"></i> Mijn omgeving</button>
        <div class="sidenav m" data-intro="This is the Advertenties dashboard item" data-step="0">
        @role('cook')
            <!-- Cook menu items -->
            <div class="dashboard-item {{ Route::is('dashboard.adverts*') && (Route::is('dashboard.adverts.active*') || Route::is('dashboard.adverts.past*')) ? 'open' : '' }} {{ Route::is('dashboard.adverts*') ? 'active-parent' : '' }}" data-intro="This is the Advertenties dashboard item" data-step="2">
                <a href="{{ route('dashboard.adverts.active.home') }}" class="{{ Route::is('dashboard.adverts*') ? 'active' : '' }}">
                    <img src="{{ asset('img/sidebar/icon-adv.svg') }}" />
                    <img src="{{ asset('img/sidebar/icon-adv-active.svg') }}" />
                    Advertenties
                </a>
                <div class="second">
                    <a href="{{ route('dashboard.adverts.active.home') }}" class="{{ Route::is('dashboard.adverts.active*') ? 'active' : '' }}">Actieve Advertenties</a>
                    <a href="{{ route('dashboard.adverts.past.home') }}" class="{{ Route::is('dashboard.adverts.past*') ? 'active' : '' }}">Verlopen Advertenties</a>
                </div>
            </div>

            <div class="dashboard-item {{ Route::is('dashboard.dishes.new*') ? 'active-parent' : '' }}" data-intro="This is the Gerechten dashboard item" data-step="1">
                <a href="{{ route('dashboard.dishes.new') }}" class="{{ Route::is('dashboard.dishes.new*') ? 'active' : '' }}">
                    <img src="{{ asset('img/sidebar/icon-gre.svg') }}" />
                    <img src="{{ asset('img/sidebar/icon-gre-active.svg') }}" />
                    Gerechten
                </a>
            </div>

            <div class="dashboard-item {{ Route::is('dashboard.orders*') ? 'active-parent' : '' }}" data-intro="This is the Bestellingen dashboard item" data-step="4">
                <a href="{{ route('dashboard.orders.home') }}" class="{{ Route::is('dashboard.orders*') ? 'active' : '' }}">
                    <img src="{{ asset('img/sidebar/icon-best.svg') }}" />
                    <img src="{{ asset('img/sidebar/icon-best-active.svg') }}" />
                    Bestellingen
                </a>
            </div>

            <div class="dashboard-item {{ Route::is('dashboard.settings*') && (Route::is('*settings.home') || Route::is('*settings.details*') || Route::is('*settings.reports*') || Route::is('*settings.privacy*')) ? 'open' : '' }} {{ Route::is('dashboard.settings*') ? 'active-parent' : '' }}" data-intro="This is the Instellingen dashboard item" data-step="3">
                <a href="{{ route('dashboard.settings.home') }}" class="{{ Route::is('dashboard.settings*') ? 'active' : '' }}">
                    <img src="{{ asset('img/sidebar/icon-inst.svg') }}" />
                    <img src="{{ asset('img/sidebar/icon-inst-active.svg') }}" />
                    Instellingen
                </a>
                <div class="second">
                    <a href="{{ route('dashboard.settings.home') }}" class="{{ Route::is('*settings.home') ? 'active' : '' }}">Profiel</a>
                    <a href="{{ route('dashboard.settings.details.home') }}" class="{{ Route::is('*settings.details*') ? 'active' : '' }}">Gegevens</a>
                    <a href="{{ route('dashboard.settings.reports.home') }}" class="{{ Route::is('*settings.reports*') ? 'active' : '' }}">Meldingen</a>
                    <a href="{{ route('dashboard.settings.privacy.home') }}" class="{{ Route::is('*settings.privacy*') ? 'active' : '' }}">Privacy</a>
                </div>
            </div>

            <div class="dashboard-item {{ Route::is('dashboard.wallet*') && (Route::is('dashboard.wallet.home') || Route::is('*wallet.iban')) ? 'open' : '' }} {{ Route::is('dashboard.wallet*') ? 'active-parent' : '' }}" data-intro="This is the Portemonnee dashboard item" data-step="5">
                <a href="{{ route('dashboard.wallet.home') }}" class="{{ Route::is('dashboard.wallet*') ? 'active' : '' }}">
                    <img src="{{ asset('img/sidebar/icon-wallet.svg') }}" />
                    <img src="{{ asset('img/sidebar/icon-wallet-active.svg') }}" />
                    Portemonnee
                </a>
                <div class="second">
                    <a href="{{ route('dashboard.wallet.home') }}" class="{{ Route::is('dashboard.wallet.home') ? 'active' : '' }}">Saldo</a>
                    <a href="{{ route('dashboard.wallet.iban') }}" class="{{ Route::is('*wallet.iban') ? 'active' : '' }}">IBAN</a>
                </div>
            </div>
        @endrole
        @role('admin')
            <!-- Admin menu items -->
            <!-- <div class="dashboard-item {{ Route::is('dashboard.admin.accounts*') && (Route::is('dashboard.admin.accounts*') || Route::is('dashboard.admin.accounts.banking*')) ? 'open' : '' }} {{ Route::is('dashboard.admin.accounts*') ? 'active-parent' : '' }}">
                <a href="{{ route('dashboard.admin.accounts') }}" class="{{ Route::is('dashboard.admin.accounts*') ? 'active' : '' }}">
                    <img src="{{ asset('img/sidebar/icon-adv.svg') }}" />
                    <img src="{{ asset('img/sidebar/icon-adv-active.svg') }}" />
                    Accounts
                </a>
                <div class="second">
                    <a href="{{ route('dashboard.admin.accounts') }}" class="{{ Route::is('dashboard.admin.accounts*') && !Route::is('dashboard.admin.accounts.banking*') ? 'active' : '' }}">Gegevens</a>
                    <a href="{{ route('dashboard.admin.accounts.banking') }}" class="{{ Route::is('dashboard.admin.accounts.banking*') ? 'active' : '' }}">Controle Bankrekening</a>
                </div>
            </div> -->
            <div class="dashboard-item {{ Route::is('dashboard.admin.accounts') ? 'active-parent' : '' }}">
                <a href="{{ route('dashboard.admin.accounts') }}" class="{{ Route::is('dashboard.admin.accounts') ? 'active' : '' }}">
                    <img src="{{ asset('img/sidebar/icon-adv.svg') }}" />
                    <img src="{{ asset('img/sidebar/icon-adv-active.svg') }}" />
                    Accounts
                </a>
            </div>

            <div class="dashboard-item {{ Route::is('dashboard.admin.accounts.banking') ? 'active-parent' : '' }}">
                <a href="{{ route('dashboard.admin.accounts.banking') }}" class="{{ Route::is('dashboard.admin.accounts.banking') ? 'active' : '' }}">
                    <img src="{{ asset('img/sidebar/icon-adv.svg') }}" />
                    <img src="{{ asset('img/sidebar/icon-adv-active.svg') }}" />
                    Controle Bankrekening
                </a>
            </div>

            <div class="dashboard-item {{ Route::is('dashboard.admin.dac7*') ? 'active-parent' : '' }}">
                <a href="{{ route('dashboard.admin.dac7') }}" class="{{ Route::is('dashboard.admin.dac7*') ? 'active' : '' }}">
                    <img src="{{ asset('img/sidebar/icon-adv.svg') }}" />
                    <img src="{{ asset('img/sidebar/icon-adv-active.svg') }}" />
                    Controle DAC7
                </a>
            </div>

            <div class="dashboard-item {{ Route::is('dashboard.admin.payouts*') ? 'active-parent' : '' }}">
                <a href="{{ route('dashboard.admin.payouts') }}" class="{{ Route::is('dashboard.admin.payouts*') ? 'active' : '' }}">
                    <img src="{{ asset('img/sidebar/icon-adv.svg') }}" />
                    <img src="{{ asset('img/sidebar/icon-adv-active.svg') }}" />
                    Uitbetalingsverzoeken
                </a>
            </div>

            <div class="dashboard-item {{ Route::is('dashboard.admin.audit-logs*') ? 'active-parent' : '' }}">
                <a href="{{ route('dashboard.admin.audit-logs') }}" class="{{ Route::is('dashboard.admin.audit-logs*') ? 'active' : '' }}">
                    <img src="{{ asset('img/sidebar/icon-adv.svg') }}" />
                    <img src="{{ asset('img/sidebar/icon-adv-active.svg') }}" />
                    Inzageregistratie
                </a>
            </div>

            <div class="dashboard-item {{ Route::is('dashboard.admin.adverts*') ? 'active-parent' : '' }}">
                <a href="{{ route('dashboard.admin.adverts') }}" class="{{ Route::is('dashboard.admin.adverts*') ? 'active' : '' }}">
                    <img src="{{ asset('img/sidebar/icon-adv.svg') }}" />
                    <img src="{{ asset('img/sidebar/icon-adv-active.svg') }}" />
                    Advertenties
                </a>
            </div>

            <div class="dashboard-item {{ Route::is('dashboard.admin.dishes*') ? 'active-parent' : '' }}">
                <a href="{{ route('dashboard.admin.dishes') }}" class="{{ Route::is('dashboard.admin.dishes*') ? 'active' : '' }}">
                    <img src="{{ asset('img/sidebar/icon-gre.svg') }}" />
                    <img src="{{ asset('img/sidebar/icon-gre-active.svg') }}" />
                    Gerechten
                </a>
            </div>

            <div class="dashboard-item {{ Route::is('dashboard.admin.reviews*') ? 'active-parent' : '' }}">
                <a href="{{ route('dashboard.admin.reviews') }}" class="{{ Route::is('dashboard.admin.reviews*') ? 'active' : '' }}">
                    <img src="{{ asset('img/sidebar/icon-adv.svg') }}" />
                    <img src="{{ asset('img/sidebar/icon-adv-active.svg') }}" />
                    Reviews
                </a>
            </div>

            <div class="dashboard-item {{ Route::is('dashboard.admin.dashboard*') ? 'active-parent' : '' }}">
                <a href="{{ route('dashboard.admin.dashboard.accounts') }}" class="{{ Route::is('dashboard.admin.dashboard*') ? 'active' : '' }}">
                    <img src="{{ asset('img/sidebar/icon-adv.svg') }}" />
                    <img src="{{ asset('img/sidebar/icon-adv-active.svg') }}" />
                    Dashboard
                </a>
            </div>

            <div class="dashboard-item {{ Route::is('dashboard.admin.settings*') ? 'active-parent' : '' }}">
                <a href="{{ route('dashboard.admin.settings') }}" class="{{ Route::is('dashboard.admin.settings*') ? 'active' : '' }}">
                    <img src="{{ asset('img/sidebar/icon-inst.svg') }}" />
                    <img src="{{ asset('img/sidebar/icon-inst-active.svg') }}" />
                    Instellingen
                </a>
            </div>
        @endrole

        <!-- Logout button (shown for both roles) -->
        <a href="{{ route('logout') }}" class="ico-logout">
            <img src="{{ asset('img/sidebar/icon-logout.svg') }}" />
            <img src="{{ asset('img/sidebar/icon-logout-active.svg') }}" />
            Uitloggen
        </a>
        </div>
    </body>
    @endsection
@endif

@section('content')
    @if (session('message'))
        <div class="message" id="message">
            <div class="content">
                {{ session('message') }}
            </div>
        </div>
    @endif
    
    @if (session('error'))
        <div class="message error" id="error-message">
            <div class="content">
                {{ session('error') }}
            </div>
        </div>
    @endif

    @if (session('success'))
        <div class="message success" id="success-message">
            <div class="content">
                {{ session('success') }}
            </div>
        </div>
    @endif
    
    @if(isset($firsttime) && $firsttime || isset($secondtime) && $secondtime || isset($hideMenu) && $hideMenu)
        <div class="main">
            @yield('dashboard')
        </div>
    @else
        <div class="dashboard">
            @if(isset($title))
                <div id='page-h' class="page-header">
                    <div class="container">
                        @if($title == 'Advertenties' && isset($past))
                            <h1>{{ $past ? 'Verlopen ' : 'Actieve ' }}{{ $title }}</h1>
                        @else
                            <h1>{{ $title }}</h1>
                        @endif
                    </div>
                </div>
            @endif
            <div class="main">
                @yield('dashboard')
            </div>
        </div>
    @endif
@endsection

@if (!$acceptedCookies)
    @include('layout.accept-cookies')
@endif

@section('global.scripts')
@role('cook')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/4.1.0/intro.min.js"></script>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    const dashboardItems = document.querySelectorAll('.dashboard-item');
    let isTransitioning = false;
    let currentOpenItem = null;

    function closeAllDropdowns(exceptItem = null) {
        dashboardItems.forEach(item => {
            if (item !== exceptItem && !item.closest('.admin-menu')) {
                item.classList.remove('open');
                if (item === currentOpenItem && item !== exceptItem) {
                    currentOpenItem = null;
                }
            }
        });
    }

    // Initialize active dropdown
    dashboardItems.forEach(item => {
        if (item.classList.contains('active-parent')) {
            item.classList.add('open');
            currentOpenItem = item;
        }
    });

    dashboardItems.forEach(item => {
        const link = item.querySelector('a');
        const second = item.querySelector('.second');

        if (!link || item.closest('.admin-menu')) return;

        // Handle main link clicks
        link.addEventListener('click', (e) => {
            if (isTransitioning) return;

            const rect = link.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            const isArrowClick = clickX > rect.width - 50;

            if (isArrowClick && second) {
                e.preventDefault();
                e.stopPropagation();
                isTransitioning = true;

                if (currentOpenItem === item) {
                    // Close current if clicking on the same item
                    item.classList.remove('open');
                    currentOpenItem = null;
                } else {
                    // Close others and open this one
                    closeAllDropdowns(item);
                    item.classList.add('open');
                    currentOpenItem = item;
                }

                setTimeout(() => {
                    isTransitioning = false;
                }, 300);
            } else if (!second) {
                // If clicking a main link without dropdown, close all dropdowns
                closeAllDropdowns();
                currentOpenItem = null;
            } else {
                // If clicking a main link with dropdown, navigate directly without closing the dropdown
                e.stopPropagation(); // Prevent the dropdown from closing
                window.location.href = link.href; // Navigate immediately
            }
        });

        // Handle clicks inside the dropdown
        if (second) {
            second.addEventListener('click', (e) => {
                e.stopPropagation();
                
                // Keep the parent dropdown open when clicking inside
                if (item !== currentOpenItem) {
                    closeAllDropdowns(item);
                    item.classList.add('open');
                    currentOpenItem = item;
                }
            });
        }
    });

    // Tutorial logic
    if (!<?php echo json_encode(isset($stepsCompleted) && $stepsCompleted); ?>) {
        // Detect if on mobile
        const isMobile = window.innerWidth <= 767 || /iPad|iPhone|iPod|Android/i.test(navigator.userAgent);
        
        if (isMobile) {
            // Mobile tutorial content
            const mobileSteps = [
                {
                    title: "Welkom",
                    content: "Welkom in jouw omgeving. We nemen je graag mee met een korte instructie zodat je direct aan de slag kunt."
                },
                {
                    title: "Gerecht aanmaken", 
                    content: "Maak allereerst een gerecht aan. Een gerecht dient geselecteerd te worden bij het aanmaken van een advertentie."
                },
                {
                    title: "Advertentie aanmaken",
                    content: "Met een gerecht kun je een advertentie aanmaken. Selecteer het gerecht en voeg de benodigde informatie toe."
                },
                {
                    title: "Aanvullende informatie",
                    content: "Voordat je als Thuiskok vindbaar bent en een advertentie online kunt plaatsen hebben we aanvullende informatie van je nodig."
                },
                {
                    title: "Contact en adresgegevens",
                    content: "Contact en adresgegevens zodat klanten na een bestelling weten waar ze hun gerecht kunnen afhalen."
                },
                {
                    title: "Bankgegevens",
                    content: "Bankgegevens zodat we het betalingsproces kunnen automatiseren."
                },
                {
                    title: "Bestellingen",
                    content: "Je kunt bij Bestellingen je actuele bestellingen bekijken."
                },
                {
                    title: "Portemonnee",
                    content: "Je kunt bij Portemonnee je saldo en uitbetalingen terugvinden."
                }
            ];
            
            // Create mobile tutorial UI
            showMobileTutorial(mobileSteps);
        } else {
            // Desktop tutorial with intro.js
            const introSteps = [
                {
                    element: document.querySelector('.dashboard-item[data-step="0"]'),
                    intro: 'Welkom in jouw omgeving. We nemen je graag mee met een korte instructie zodat je direct aan de slag kunt.',
                    position: 'right'
                },
                {
                    element: document.querySelector('.dashboard-item[data-step="1"]'),
                    intro: 'Maak allereerst een gerecht aan. Een gerecht dient geselecteerd te worden bij het aanmaken van een advertentie.',
                    position: 'right'
                },
                {
                    element: document.querySelector('.dashboard-item[data-step="2"]'),
                    intro: 'Hier kan je een advertentie aanmaken. Selecteer het gerecht en voeg de benodigde informatie toe.',
                    position: 'right'
                },
                {
                    element: document.querySelector('.dashboard-item[data-step="3"]'),
                    intro: 'Voordat je als Thuiskok vindbaar bent en een advertentie online kunt plaatsen hebben we aanvullende informatie van je nodig.',
                    position: 'right'
                },
                {
                    element: document.querySelector('.dashboard-item[data-step="3"]'),
                    intro: 'Contact en adresgegevens zodat klanten na een bestelling weten waar ze hun gerecht kunnen afhalen.',
                    position: 'right'
                },
                {
                    element: document.querySelector('.dashboard-item[data-step="3"]'),
                    intro: 'Bankgegevens zodat we het betalingsproces kunnen automatiseren.',
                    position: 'right'
                },
                {
                    element: document.querySelector('.dashboard-item[data-step="4"]'),
                    intro: 'Klik hier om de bestellingen te bekijken.',
                    position: 'right'
                },
                {
                    element: document.querySelector('.dashboard-item[data-step="5"]'),
                    intro: 'Bekijk hier je saldo en uitbetalingen.',
                    position: 'right'
                }
            ];

            const intro = introJs();
            intro.addSteps(introSteps);

            intro.setOptions({
                showStepNumbers: false, 
                exitOnOverlayClick: false, 
                nextLabel: 'Volgende', 
                prevLabel: 'Vorige',
                doneLabel: 'Klaar', 
            });

            intro.oncomplete(function() {
                console.log('Introductie voltooid');
                window.location.href = '/dashboard/mark-intro-as-completed'; 
            });

            intro.start();
        }
    }

    // Function to show mobile tutorial
    function showMobileTutorial(steps) {
        let currentStep = 0;
        
        // Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'mobile-tutorial-overlay';
        
        // Create modal
        const modal = document.createElement('div');
        modal.className = 'mobile-tutorial-modal';
        
        // Create content
        const content = document.createElement('div');
        content.className = 'mobile-tutorial-content';
        
        // Create dots container
        const dots = document.createElement('div');
        dots.className = 'mobile-tutorial-dots';
        
        // Create dots for each step
        for (let i = 0; i < steps.length; i++) {
            const dot = document.createElement('div');
            dot.className = 'mobile-tutorial-dot';
            if (i === 0) dot.classList.add('active');
            dots.appendChild(dot);
        }
        
        // Create buttons container
        const buttons = document.createElement('div');
        buttons.className = 'mobile-tutorial-buttons';
        
        // Create prev button
        const prevButton = document.createElement('button');
        prevButton.className = 'mobile-tutorial-button prev';
        prevButton.textContent = 'Vorige';
        prevButton.disabled = true;
        
        // Create next button
        const nextButton = document.createElement('button');
        nextButton.className = 'mobile-tutorial-button';
        nextButton.textContent = 'Volgende';
        
        // Add buttons to container
        buttons.appendChild(prevButton);
        buttons.appendChild(nextButton);
        
        // Update content function
        function updateContent() {
            const step = steps[currentStep];
            
            content.innerHTML = `
                <h3>${step.title}</h3>
                <p>${step.content}</p>
            `;
            
            // Update dots
            const allDots = dots.querySelectorAll('.mobile-tutorial-dot');
            allDots.forEach((dot, i) => {
                dot.classList.toggle('active', i === currentStep);
            });
            
            // Update buttons
            prevButton.disabled = currentStep === 0;
            nextButton.textContent = currentStep === steps.length - 1 ? 'Klaar' : 'Volgende';
        }
        
        // Set initial content
        updateContent();
        
        // Add event listeners
        prevButton.addEventListener('click', () => {
            if (currentStep > 0) {
                currentStep--;
                updateContent();
            }
        });
        
        nextButton.addEventListener('click', () => {
            if (currentStep < steps.length - 1) {
                currentStep++;
                updateContent();
            } else {
                // Tutorial completed
                document.body.removeChild(overlay);
                window.location.href = '/dashboard/mark-intro-as-completed';
            }
        });
        
        // Assemble and add to DOM
        modal.appendChild(content);
        modal.appendChild(dots);
        modal.appendChild(buttons);
        overlay.appendChild(modal);
        document.body.appendChild(overlay);
    }
});
    </script>
    @endrole
    
    <!-- Flash berichten automatisch laten verdwijnen na 5 seconden -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Alle message elementen selecteren
        const messages = document.querySelectorAll('.message');
        
        if (messages.length > 0) {
            // Timeout instellen voor elk message element
            setTimeout(function() {
                messages.forEach(function(message) {
                    // Voeg fade-out klasse toe voor animatie
                    message.classList.add('fade-out');
                    
                    // Verwijder het element volledig na de animatie
                    setTimeout(function() {
                        if (message.parentNode) {
                            message.parentNode.removeChild(message);
                        }
                    }, 500); // 500ms is de duur van de fadeOut animatie
                });
            }, 5000); // Berichten verdwijnen na 5 seconden
        }
    });
    </script>
@endsection
