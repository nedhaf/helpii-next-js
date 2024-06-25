<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo ">
        <a href="<?php echo e(route('administrator.backend_dashboard')); ?>" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="<?php echo e(asset('images/helpii-h-logo.png')); ?>">
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2"><img src="<?php echo e(asset('images/helpisidebarlogo.png')); ?>"></span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
        <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>
    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-item <?php echo e(request()->routeIs('administrator.backend_dashboard') ? 'active' : ''); ?>">
            <a href="<?php echo e(route('administrator.backend_dashboard')); ?>" class="menu-link">
                
                <i class="menu-icon fas fa-gauge-high"></i>
                <div class="text-truncate" data-i18n="<?php echo e(__('Dashboard')); ?>"><?php echo e(__('Dashboard')); ?></div>
            </a>
        </li>

        <!-- User Management -->
        <li class="menu-header small">
          <span class="menu-header-text" data-i18n="<?php echo e(__('User Management')); ?>"><?php echo e(__('User Management')); ?></span>
        </li>
        <li class="menu-item <?php echo e(request()->routeIs('administrator.backend_user_management*') || request()->routeIs('administrator.backend_create_user*') || request()->routeIs('administrator.backend_edit_user*') ? 'active' : ''); ?>">
            <a href="<?php echo e(route('administrator.backend_user_management')); ?>" class="menu-link">
                
                <i class="menu-icon fas fa-users"></i>
                <div class="text-truncate" data-i18n="<?php echo e(__('User Management')); ?>"><?php echo e(__('User Management')); ?></div>
            </a>
        </li>

        <!-- Advertisement Management -->
        <li class="menu-header small">
          <span class="menu-header-text" data-i18n="<?php echo e(__('Advertisement Management')); ?>"><?php echo e(__('Advertisement Management')); ?></span>
        </li>
        <li class="menu-item <?php echo e(request()->routeIs('administrator.backend_advertisements*') || request()->routeIs('administrator.backend_advertisement_create*') ? 'active' : ''); ?>">
            <a href="<?php echo e(route('administrator.backend_advertisements')); ?>" class="menu-link">
                
                <i class="menu-icon fas fa-ad"></i>
                <div class="text-truncate" data-i18n="<?php echo e(__('Advertisements')); ?>"><?php echo e(__('Advertisement')); ?></div>
            </a>
        </li>

        <!-- Skill Management -->
        <li class="menu-header small">
          <span class="menu-header-text" data-i18n="<?php echo e(__('Skill Management')); ?>"><?php echo e(__('Skill Management')); ?></span>
        </li>
        <li class="menu-item <?php echo e(request()->routeIs('administrator.backend_skills*') || request()->routeIs('administrator.backend_skill_create*') || request()->routeIs('administrator.backend_skill_edit*') || request()->routeIs('administrator.backend_import_skill*') ? 'active' : ''); ?>">
            <a href="<?php echo e(route('administrator.backend_skills')); ?>" class="menu-link">
                <i class="menu-icon fas fa-tasks"></i>
                <div class="text-truncate" data-i18n="<?php echo e(__('Skills Management')); ?>"><?php echo e(__('Skills Management')); ?></div>
            </a>
        </li>

        <!-- Badge Management -->
        <li class="menu-header small">
          <span class="menu-header-text" data-i18n="<?php echo e(__('Badge Management')); ?>"><?php echo e(__('Badge Management')); ?></span>
        </li>
        <li class="menu-item <?php echo e(request()->routeIs('administrator.backend_badges*') || request()->routeIs('administrator.backend_badge_create*') || request()->routeIs('administrator.backend_badge_edit*') ? 'active' : ''); ?>">
            <a href="<?php echo e(route('administrator.backend_badges')); ?>" class="menu-link">
                <i class="menu-icon fas fa-id-badge"></i>
                <div class="text-truncate" data-i18n="<?php echo e(__('Badge Management')); ?>"><?php echo e(__('Badge Management')); ?></div>
            </a>
        </li>

        <!-- Site Settings -->
        <li class="menu-header small">
          <span class="menu-header-text" data-i18n="<?php echo e(__('Site Settings')); ?>"><?php echo e(__('Site Settings')); ?></span>
        </li>
        <li class="menu-item <?php echo e(request()->routeIs('administrator.backend_basic_settings*')  ? 'active open' : ''); ?>" style="">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon fas fa-tools"></i>
                <div class="text-truncate" data-i18n="<?php echo e(__('Site Settings')); ?>"><?php echo e(__('Site Settings')); ?></div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php echo e(request()->routeIs('administrator.backend_basic_settings*')  ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('administrator.backend_basic_settings')); ?>" class="menu-link">
                        <div class="text-truncate" data-i18n="<?php echo e(__('Basic Settings')); ?>"><?php echo e(__('Basic Settings')); ?></div>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</aside><?php /**PATH /var/www/html/staging.helpii.se/backend/resources/views/backend/partials/sidebar.blade.php ENDPATH**/ ?>