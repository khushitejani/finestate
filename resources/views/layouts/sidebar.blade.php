<div class="container-fluid page-body-wrapper">
    <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
            <li class="nav-item navbar-brand-mini-wrapper">
                <a class="nav-link navbar-brand brand-logo-mini" href="index.html"><img src="assets/images/logo-mini.svg"
                        alt="logo" /></a>
            </li>
            <li class="nav-item nav-category mt-2">
                <a href="{{ route('dashboard') }}" class="nav-link" style="cursor: pointer;">
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ Route('cards.index') }}" aria-expanded="false" aria-controls="icons">
                    <span class="menu-title">Cards</span>
                    <i class="icon-credit-card menu-icon"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ Route('shares.index') }}" aria-expanded="false" aria-controls="icons">
                    <span class="menu-title">Shares</span>
                    <i class="icon-pie-chart menu-icon"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ Route('properties.index') }}" aria-expanded="false" aria-controls="icons">
                    <span class="menu-title">Real Estate Properties</span>
                    <i class="icon-home menu-icon"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ Route('improvements.index') }}" aria-expanded="false"
                    aria-controls="icons">
                    <span class="menu-title">Improvements</span>
                    <i class="icon-settings menu-icon"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('carshowrooms.index') }}" aria-expanded="false"
                    aria-controls="carshowrooms">
                    <span class="menu-title">Car Showrooms</span>
                    <i class="icon-speedometer menu-icon"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('aircraftshops.index') }}" aria-expanded="false"
                    aria-controls="aircraftshops">
                    <span class="menu-title">Aircraft Shops</span>
                    <i class="icon-plane menu-icon"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('coins.index') }}" aria-expanded="false" aria-controls="coins">
                    <span class="menu-title">Coins</span>
                    <i class="icon-wallet menu-icon"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('paintings.index') }}" aria-expanded="false"
                    aria-controls="paintings">
                    <span class="menu-title">Paintings</span>
                    <i class="icon-layers menu-icon"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('nfts.index') }}" aria-expanded="false" aria-controls="nfts">
                    <span class="menu-title">NFTs</span>
                    <i class="icon-layers menu-icon"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('unique_items.index') }}" aria-expanded="false"
                    aria-controls="unique_items">
                    <span class="menu-title">Unique Items</span>
                    <i class="icon-diamond menu-icon"></i>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('retro_cars.index') }}" aria-expanded="false"
                    aria-controls="retro_cars">
                    <span class="menu-title">Retro Cars</span>
                    <i class="icon-speedometer menu-icon"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('islands.index') }}" aria-expanded="false" aria-controls="islands">
                    <span class="menu-title">Islands</span>
                    <i class="icon-globe-alt menu-icon"></i>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('business-taxis.index') }}" aria-expanded="false"
                    aria-controls="business-taxis">
                    <span class="menu-title">Business Taxis</span>
                    <i class="icon-directions menu-icon"></i>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('business-shippings.index') }}" aria-expanded="false"
                    aria-controls="business-shippings">
                    <span class="menu-title">Business Shippings</span>
                    <i class="icon-directions menu-icon"></i>
                </a>
            </li>
            <li class="nav-item {{ Request::is('constructions*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('constructions.index') }}" aria-expanded="false"
                    aria-controls="constructions">
                    <span class="menu-title">Constructions</span>
                    <i class="icon-home menu-icon"></i>
                </a>
            </li>
            <li class="nav-item {{ Request::is('business_slots*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('business_slots.index') }}" aria-expanded="false"
                    aria-controls="business-slots">
                    <span class="menu-title">Business Slots</span>
                    <i class="icon-grid menu-icon"></i>
                </a>
            </li>
            <li class="nav-item {{ Request::is('forbs_slots*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('forbs_slots.index') }}" aria-expanded="false"
                    aria-controls="forbs-slots">
                    <span class="menu-title">Forbs</span>
                    <i class="icon-grid menu-icon"></i>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('jewelleds.index') }}" aria-expanded="false"
                    aria-controls="jewels">
                    <span class="menu-title">Jewels</span>
                    <i class="icon-diamond menu-icon"></i>
                </a>
            </li>
            <li class="nav-item {{ Request::is('stamps*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('stamps.index') }}" aria-expanded="false"
                    aria-controls="stamps">
                    <span class="menu-title">Stamps</span>
                    <i class="icon-note menu-icon"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('yatchshop.index') }}" aria-expanded="false"
                    aria-controls="yachtshop">
                    <span class="menu-title">Yacht Shop</span>
                    <i class="icon-diamond menu-icon"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('cryptos.index') }}" aria-expanded="false"
                    aria-controls="cryptos">
                    <span class="menu-title">Cryptocurrencies</span>
                    <i class="icon-wallet menu-icon"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('insights.index') }}" aria-expanded="false"
                    aria-controls="insights">
                    <span class="menu-title">Insights</span>
                    <i class="icon-layers menu-icon"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('policy.index') }}" aria-expanded="false"
                    aria-controls="policy">
                    <span class="menu-title">Policies</span>
                    <i class="icon-doc menu-icon"></i>
                </a>
            </li>
        </ul>
    </nav>
    {{-- 

    <style>
        /* Make sidebar sticky */
        .sidebar {
            position: sticky;
            top: 0;
            /* Distance from the top of the viewport */
            height: 100vh;
            /* Full height of viewport */
            overflow-y: auto;
            /* Add scrollbar only for sidebar if needed */
            background-color: #181824;
            /* optional: to ensure background covers content */
            z-index: 1000;
            /* make sure it stays above other content */
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
        }

      
        .content-wrapper {
            margin-left: 240px;
            /* width of your sidebar */
        }
    </style> --}}
