<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo ">
        <a href="{{ route('administrator.backend_dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('images/helpii-h-logo.png') }}">
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2"><img src="{{ asset('images/helpisidebarlogo.png') }}"></span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
        <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>
    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-item {{ request()->routeIs('administrator.backend_dashboard') ? 'active' : '' }}">
            <a href="{{ route('administrator.backend_dashboard') }}" class="menu-link">
                {{-- <i class="menu-icon tf-icons fas fa-"></i> --}}
                <i class="menu-icon fas fa-gauge-high"></i>
                <div class="text-truncate" data-i18n="{{ __('Dashboard') }}">{{ __('Dashboard') }}</div>
            </a>
        </li>

        <!-- User Management -->
        <li class="menu-header small">
          <span class="menu-header-text" data-i18n="{{ __('User Management') }}">{{ __('User Management') }}</span>
        </li>
        <li class="menu-item {{ request()->routeIs('administrator.backend_user_management*') || request()->routeIs('administrator.backend_create_user*') || request()->routeIs('administrator.backend_edit_user*') ? 'active' : '' }}">
            <a href="{{ route('administrator.backend_user_management') }}" class="menu-link">
                {{-- <i class="menu-icon tf-icons fas fa-"></i> --}}
                <i class="menu-icon fas fa-users"></i>
                <div class="text-truncate" data-i18n="{{ __('User Management') }}">{{ __('User Management') }}</div>
            </a>
        </li>

        <!-- Advertisement Management -->
        <li class="menu-header small">
          <span class="menu-header-text" data-i18n="{{ __('Advertisement Management') }}">{{ __('Advertisement Management') }}</span>
        </li>
        <li class="menu-item {{ request()->routeIs('administrator.backend_advertisements*') || request()->routeIs('administrator.backend_advertisement_create*') ? 'active' : '' }}">
            <a href="{{ route('administrator.backend_advertisements') }}" class="menu-link">
                {{-- <i class="menu-icon tf-icons fas fa-"></i> --}}
                <i class="menu-icon fas fa-ad"></i>
                <div class="text-truncate" data-i18n="{{ __('Advertisements') }}">{{ __('Advertisement') }}</div>
            </a>
        </li>

        <!-- Skill Management -->
        <li class="menu-header small">
          <span class="menu-header-text" data-i18n="{{ __('Skill Management') }}">{{ __('Skill Management') }}</span>
        </li>
        <li class="menu-item {{ request()->routeIs('administrator.backend_skills*') || request()->routeIs('administrator.backend_skill_create*') || request()->routeIs('administrator.backend_skill_edit*') || request()->routeIs('administrator.backend_import_skill*') ? 'active' : '' }}">
            <a href="{{ route('administrator.backend_skills') }}" class="menu-link">
                <i class="menu-icon fas fa-tasks"></i>
                <div class="text-truncate" data-i18n="{{ __('Skills Management') }}">{{ __('Skills Management') }}</div>
            </a>
        </li>

        <!-- Badge Management -->
        <li class="menu-header small">
          <span class="menu-header-text" data-i18n="{{ __('Badge Management') }}">{{ __('Badge Management') }}</span>
        </li>
        <li class="menu-item {{ request()->routeIs('administrator.backend_badges*') || request()->routeIs('administrator.backend_badge_create*') || request()->routeIs('administrator.backend_badge_edit*') ? 'active' : '' }}">
            <a href="{{ route('administrator.backend_badges') }}" class="menu-link">
                <i class="menu-icon fas fa-id-badge"></i>
                <div class="text-truncate" data-i18n="{{ __('Badge Management') }}">{{ __('Badge Management') }}</div>
            </a>
        </li>

        <!-- Site Settings -->
        <li class="menu-header small">
          <span class="menu-header-text" data-i18n="{{ __('Site Settings') }}">{{ __('Site Settings') }}</span>
        </li>
        <li class="menu-item {{ request()->routeIs('administrator.backend_basic_settings*')  ? 'active open' : '' }}" style="">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon fas fa-tools"></i>
                <div class="text-truncate" data-i18n="{{ __('Site Settings') }}">{{ __('Site Settings') }}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('administrator.backend_basic_settings*')  ? 'active' : '' }}">
                    <a href="{{ route('administrator.backend_basic_settings') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="{{ __('Basic Settings') }}">{{ __('Basic Settings') }}</div>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</aside>