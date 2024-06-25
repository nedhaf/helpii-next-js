<!DOCTYPE html>
<!-- beautify ignore:start -->
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact " dir="ltr" data-theme="theme-default" data-assets-path="../Backend/assets/" data-template="vertical-menu-template">
    <!-- Mirrored from demos.themeselection.com/sneat-bootstrap-html-admin-template/html/vertical-menu-template/ by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 03 Jan 2024 09:59:42 GMT -->
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title>Helpii | <?php echo $__env->yieldContent('head-title'); ?></title>

        <!-- Helpii Metas -->
        <?php echo $__env->yieldContent('meta'); ?>

        <!-- Canonical SEO -->
        <link rel="canonical" href="https://helpii.se/">
        <!-- ? PROD Only: Google Tag Manager (Default ThemeSelection: GTM-5DDHKGP, PixInvent: GTM-5J3LMKC) -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            '../../../../www.googletagmanager.com/gtm5445.html?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-5DDHKGP');
        </script>
        <!-- End Google Tag Manager -->
        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="/favicon.ico" />
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com/">
        <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&amp;display=swap" rel="stylesheet">

        <?php echo $__env->yieldPushContent('before-styles'); ?>

        <!-- Icons -->
        <link rel="stylesheet" href="<?php echo e(asset('Backend/assets/vendor/fonts/boxicons.css')); ?>" />
        <link rel="stylesheet" href="<?php echo e(asset('Backend/assets/vendor/fonts/fontawesome.css')); ?>" />
        <link rel="stylesheet" href="<?php echo e(asset('Backend/assets/vendor/fonts/flag-icons.css')); ?>" />
        <!-- Core CSS -->
        <link rel="stylesheet" href="<?php echo e(asset('Backend/assets/vendor/css/rtl/core.css')); ?>" class="template-customizer-core-css" />
        <link rel="stylesheet" href="<?php echo e(asset('Backend/assets/vendor/css/rtl/theme-default.css')); ?>" class="template-customizer-theme-css" />
        <link rel="stylesheet" href="<?php echo e(asset('Backend/assets/css/demo.css')); ?>" />
        <!-- Vendors CSS -->
        <link rel="stylesheet" href="<?php echo e(asset('Backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')); ?>" />
        <link rel="stylesheet" href="<?php echo e(asset('Backend/assets/vendor/libs/typeahead-js/typeahead.css')); ?>" />

        <!-- Page CSS -->
        <!-- Helpers -->
        <script src="<?php echo e(asset('Backend/assets/vendor/js/helpers.js')); ?>"></script>
        <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
        <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
        
        <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
        <script src="<?php echo e(asset('Backend/assets/js/config.js')); ?>"></script>

        <?php echo $__env->yieldPushContent('after-styles'); ?>

    </head>
    <body>
        <!-- ?PROD Only: Google Tag Manager (noscript) (Default ThemeSelection: GTM-5DDHKGP, PixInvent: GTM-5J3LMKC) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5DDHKGP" height="0" width="0" style="display: none; visibility: hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
        <!-- Layout wrapper -->
        <div class="layout-wrapper layout-content-navbar  ">
            <div class="layout-container">
                <!-- Menu -->
                <?php echo $__env->make('backend.partials.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <!-- / Menu -->
                <!-- Layout container -->
                <div class="layout-page">
                    <!-- Navbar -->
                    <?php echo $__env->make('backend.partials.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <!-- / Navbar -->
                    <!-- Content wrapper -->
                    <div class="content-wrapper">
                        <!-- Content -->
                        <div class="container-xxl flex-grow-1 container-p-y">
                            <?php echo $__env->yieldContent('content'); ?>
                        </div>
                        <!-- / Content -->
                        <!-- Footer -->
                        <footer class="content-footer footer bg-footer-theme">
                            <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                                <div class="mb-2 mb-md-0">
                                    <strong><?php echo e(__('Copyright')); ?> © <script>document.write(new Date().getFullYear()) </script> <a href="https://www.helpii.se/" target="_blank" class="footer-link fw-medium text-primary">Helpii</a></strong>
                                </div>
                                <div class="d-none d-lg-inline-block">
                                    <strong><?php echo e(__('Created By')); ?></strong>
                                    <a href="https://raindrops.se/" class="footer-link me-4" target="_blank"><img src="<?php echo e(asset('images/raindrops-se-icon.png')); ?>" /></a>

                                    <a href="https://www.helpii.se/" target="_blank" class="footer-link d-none d-sm-inline-block">Support</a>
                                </div>
                            </div>
                        </footer>
                        <!-- / Footer -->
                        <div class="content-backdrop fade"></div>
                    </div>
                    <!-- Content wrapper -->
                </div>
                <!-- / Layout page -->
            </div>
            <!-- Overlay -->
            <div class="layout-overlay layout-menu-toggle"></div>
            <!-- Drag Target Area To SlideIn Menu On Small Screens -->
            <div class="drag-target"></div>
        </div>
        <!-- / Layout wrapper -->

        <!-- Core JS -->
        <!-- build:js assets/vendor/js/core.js -->
        <script src="<?php echo e(asset('Backend/assets/vendor/libs/jquery/jquery.js')); ?>"></script>
        <script src="<?php echo e(asset('Backend/assets/vendor/libs/popper/popper.js')); ?>"></script>
        <script src="<?php echo e(asset('Backend/assets/vendor/js/bootstrap.js')); ?>"></script>
        <script src="<?php echo e(asset('Backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')); ?>"></script>
        <script src="<?php echo e(asset('Backend/assets/vendor/libs/hammer/hammer.js')); ?>"></script>
        <script src="<?php echo e(asset('Backend/assets/vendor/libs/i18n/i18n.js')); ?>"></script>
        <script src="<?php echo e(asset('Backend/assets/vendor/libs/typeahead-js/typeahead.js')); ?>"></script>
        <script src="<?php echo e(asset('Backend/assets/vendor/js/menu.js')); ?>"></script>
        <?php echo $__env->yieldPushContent('before-scripts'); ?>
        <!-- endbuild -->

        <!-- Scripts -->
        <?php echo $__env->yieldPushContent('after-scripts'); ?>

        <!-- Main JS -->
        <script src="<?php echo e(asset('Backend/assets/js/main.js')); ?>"></script>
    </body>
</html>
<!-- beautify ignore:end --><?php /**PATH /var/www/html/staging.helpii.se/backend/resources/views/backend/layouts/administrator-app.blade.php ENDPATH**/ ?>