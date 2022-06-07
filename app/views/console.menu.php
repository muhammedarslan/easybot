<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto"><a class="navbar-brand" href="/console/dashboard">
                    <div class="brand-logo"></div>
                    <h2 class="brand-text mb-0">EasyBot</h2>
                </a></li>
            <li class="nav-item nav-toggle">
                <a onclick="MenuType();" id="ChnMenuType" class="nav-link modern-nav-toggle pr-0"
                    data-toggle="collapse"><i
                        class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i><i
                        class="toggle-icon feather <?= $MenuOptionsArray['iconType'] ?> font-medium-4 d-none d-xl-block collapse-toggle-icon primary"
                        data-ticon="<?= $MenuOptionsArray['iconType'] ?>"></i></a>
            </li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class=" nav-item"><a href="/console/dashboard"><i class="feather icon-home"></i><span class="menu-title"
                        data-i18n="Dashboard">Dashboard</span><span
                        class="badge badge badge-warning badge-pill float-right mr-2">2</span></a>
                <ul class="menu-content">
                    <li class="active"><a onclick="barba.go('/console/dashboard');return false;"
                            href="/console/dashboard"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Analytics"><?= Staticfunctions::lang('Anasayfa') ?></span></a>
                    </li>
                    <li><a onclick="barba.go('/console/create/bot');return false;" href="/console/create/bot"><i
                                class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="eCommerce"><?= Staticfunctions::lang('Bot Oluştur') ?></span></a>
                    </li>
                </ul>
            </li>
            <li class=" navigation-header"><span>Apps</span>
            </li>
            <li class=" nav-item"><a href="/console/app-email"><i class="feather icon-mail"></i><span class="menu-title"
                        data-i18n="Email">Email</span></a>
            </li>
            <li class=" nav-item"><a href="/console/app-chat"><i class="feather icon-message-square"></i><span
                        class="menu-title" data-i18n="Chat">Chat</span></a>
            </li>
            <li class=" nav-item"><a href="/console/app-todo"><i class="feather icon-check-square"></i><span
                        class="menu-title" data-i18n="Todo">Todo</span></a>
            </li>
            <li class=" nav-item"><a href="/console/app-calender"><i class="feather icon-calendar"></i><span
                        class="menu-title" data-i18n="Calender">Calender</span></a>
            </li>
            <li class=" nav-item"><a href="/console/#"><i class="feather icon-shopping-cart"></i><span
                        class="menu-title" data-i18n="Ecommerce">Ecommerce</span></a>
                <ul class="menu-content">
                    <li><a href="/console/app-ecommerce-shop"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Shop">Shop</span></a>
                    </li>
                    <li><a href="/console/app-ecommerce-details"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Details">Details</span></a>
                    </li>
                    <li><a href="/console/app-ecommerce-wishlist"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Wish List">Wish List</span></a>
                    </li>
                    <li><a href="/console/app-ecommerce-checkout"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Checkout">Checkout</span></a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item"><a href="/console/#"><i class="feather icon-user"></i><span class="menu-title"
                        data-i18n="User">User</span></a>
                <ul class="menu-content">
                    <li><a href="/console/app-user-list"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="List">List</span></a>
                    </li>
                    <li><a href="/console/app-user-view"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="View">View</span></a>
                    </li>
                    <li><a href="/console/app-user-edit"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Edit">Edit</span></a>
                    </li>
                </ul>
            </li>
            <li class=" navigation-header"><span>UI Elements</span>
            </li>
            <li class=" nav-item"><a href="/console/#"><i class="feather icon-list"></i><span class="menu-title"
                        data-i18n="Data List">Data List</span><span
                        class="badge badge badge-primary badge-pill float-right mr-2">New</span></a>
                <ul class="menu-content">
                    <li><a href="/console/data-list-view"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="List View">List View</span></a>
                    </li>
                    <li><a href="/console/data-thumb-view"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Thumb View">Thumb View</span></a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item"><a href="/console/#"><i class="feather icon-layout"></i><span class="menu-title"
                        data-i18n="Content">Content</span></a>
                <ul class="menu-content">
                    <li><a href="/console/content-grid"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Grid">Grid</span></a>
                    </li>
                    <li><a href="/console/content-typography"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Typography">Typography</span></a>
                    </li>
                    <li><a href="/console/content-text-utilities"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Text Utilities">Text Utilities</span></a>
                    </li>
                    <li><a href="/console/content-syntax-highlighter"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Syntax Highlighter">Syntax Highlighter</span></a>
                    </li>
                    <li><a href="/console/content-helper-classes"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Helper Classes">Helper Classes</span></a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item"><a href="/console/colors"><i class="feather icon-droplet"></i><span class="menu-title"
                        data-i18n="Colors">Colors</span></a>
            </li>
            <li class=" nav-item"><a href="/console/#"><i class="feather icon-eye"></i><span class="menu-title"
                        data-i18n="Icons">Icons</span></a>
                <ul class="menu-content">
                    <li><a href="/console/icons-feather"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Feather">Feather</span></a>
                    </li>
                    <li><a href="/console/icons-font-awesome"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Font Awesome">Font Awesome</span></a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item"><a href="/console/#"><i class="feather icon-credit-card"></i><span class="menu-title"
                        data-i18n="Card">Card</span></a>
                <ul class="menu-content">
                    <li><a href="/console/card-basic"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Basic">Basic</span></a>
                    </li>
                    <li><a href="/console/card-advance"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Advance">Advance</span></a>
                    </li>
                    <li><a href="/console/card-statistics"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Statistics">Statistics</span></a>
                    </li>
                    <li><a href="/console/card-analytics"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Analytics">Analytics</span></a>
                    </li>
                    <li><a href="/console/card-actions"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Card Actions">Card Actions</span></a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item"><a href="/console/#"><i class="feather icon-briefcase"></i><span class="menu-title"
                        data-i18n="Components">Components</span></a>
                <ul class="menu-content">
                    <li><a href="/console/component-alerts"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Alerts">Alerts</span></a>
                    </li>
                    <li><a href="/console/component-buttons-basic"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Buttons">Buttons</span></a>
                    </li>
                    <li><a href="/console/component-breadcrumbs"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Breadcrumbs">Breadcrumbs</span></a>
                    </li>
                    <li><a href="/console/component-carousel"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Carousel">Carousel</span></a>
                    </li>
                    <li><a href="/console/component-collapse"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Collapse">Collapse</span></a>
                    </li>
                    <li><a href="/console/component-dropdowns"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Dropdowns">Dropdowns</span></a>
                    </li>
                    <li><a href="/console/component-list-group"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="List Group">List Group</span></a>
                    </li>
                    <li><a href="/console/component-modals"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Modals">Modals</span></a>
                    </li>
                    <li><a href="/console/component-pagination"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Pagination">Pagination</span></a>
                    </li>
                    <li><a href="/console/component-navs-component"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Navs Component">Navs Component</span></a>
                    </li>
                    <li><a href="/console/component-navbar"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Navbar">Navbar</span></a>
                    </li>
                    <li><a href="/console/component-tabs-component"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Tabs Component">Tabs Component</span></a>
                    </li>
                    <li><a href="/console/component-pills-component"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Pills Component">Pills Component</span></a>
                    </li>
                    <li><a href="/console/component-tooltips"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Tooltips">Tooltips</span></a>
                    </li>
                    <li><a href="/console/component-popovers"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Popovers">Popovers</span></a>
                    </li>
                    <li><a href="/console/component-badges"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Badges">Badges</span></a>
                    </li>
                    <li><a href="/console/component-pill-badges"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Pill Badges">Pill Badges</span></a>
                    </li>
                    <li><a href="/console/component-progress"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Progress">Progress</span></a>
                    </li>
                    <li><a href="/console/component-media-objects"><i class="feather icon-circle"></i><span
                                class="menu-item">Media Objects</span></a>
                    </li>
                    <li><a href="/console/component-spinner"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Spinner">Spinner</span></a>
                    </li>
                    <li><a href="/console/component-bs-toast"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Toasts">Toasts</span></a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item"><a href="/console/#"><i class="feather icon-box"></i><span class="menu-title"
                        data-i18n="Extra Components">Extra Components</span></a>
                <ul class="menu-content">
                    <li><a href="/console/ex-component-avatar"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Avatar">Avatar</span></a>
                    </li>
                    <li><a href="/console/ex-component-chips"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Chips">Chips</span></a>
                    </li>
                    <li><a href="/console/ex-component-divider"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Divider">Divider</span></a>
                    </li>
                </ul>
            </li>
            <li class=" navigation-header"><span>Forms &amp; Tables</span>
            </li>
            <li class=" nav-item"><a href="/console/#"><i class="feather icon-copy"></i><span class="menu-title"
                        data-i18n="Form Elements">Form Elements</span></a>
                <ul class="menu-content">
                    <li><a href="/console/form-select"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Select">Select</span></a>
                    </li>
                    <li><a href="/console/form-switch"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Switch">Switch</span></a>
                    </li>
                    <li><a href="/console/form-checkbox"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Checkbox">Checkbox</span></a>
                    </li>
                    <li><a href="/console/form-radio"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Radio">Radio</span></a>
                    </li>
                    <li><a href="/console/form-inputs"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Input">Input</span></a>
                    </li>
                    <li><a href="/console/form-input-groups"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Input Groups">Input Groups</span></a>
                    </li>
                    <li><a href="/console/form-number-input"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Number Input">Number Input</span></a>
                    </li>
                    <li><a href="/console/form-textarea"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Textarea">Textarea</span></a>
                    </li>
                    <li><a href="/console/form-date-time-picker"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Date &amp; Time Picker">Date &amp; Time Picker</span></a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item"><a href="/console/form-layout"><i class="feather icon-box"></i><span
                        class="menu-title" data-i18n="Form Layout">Form Layout</span></a>
            </li>
            <li class=" nav-item"><a href="/console/form-wizard"><i class="feather icon-package"></i><span
                        class="menu-title" data-i18n="Form Wizard">Form Wizard</span></a>
            </li>
            <li class=" nav-item"><a href="/console/form-validation"><i class="feather icon-check-circle"></i><span
                        class="menu-title" data-i18n="Form Validation">Form Validation</span></a>
            </li>
            <li class=" nav-item"><a href="/console/table"><i class="feather icon-server"></i><span class="menu-title"
                        data-i18n="Table">Table</span></a>
            </li>
            <li class=" nav-item"><a href="/console/table-datatable"><i class="feather icon-grid"></i><span
                        class="menu-title" data-i18n="Datatable">Datatable</span></a>
            </li>
            <li class=" nav-item"><a href="/console/table-ag-grid"><i class="feather icon-grid"></i><span
                        class="menu-title" data-i18n="ag-grid">agGrid Table</span><span
                        class="badge badge badge-primary badge-pill float-right mr-2">New</span></a>
            </li>
            <li class=" navigation-header"><span>pages</span>
            </li>
            <li class=" nav-item"><a href="/console/page-user-profile"><i class="feather icon-user"></i><span
                        class="menu-title" data-i18n="Profile">Profile</span></a>
            </li>
            <li class=" nav-item"><a href="/console/page-account-settings"><i class="feather icon-settings"></i><span
                        class="menu-title" data-i18n="Account Settings">Account Settings</span></a>
            </li>
            <li class=" nav-item"><a href="/console/page-faq"><i class="feather icon-help-circle"></i><span
                        class="menu-title" data-i18n="FAQ">FAQ</span></a>
            </li>
            <li class=" nav-item"><a href="/console/page-knowledge-base"><i class="feather icon-info"></i><span
                        class="menu-title" data-i18n="Knowledge Base">Knowledge Base</span></a>
            </li>
            <li class=" nav-item"><a href="/console/page-search"><i class="feather icon-search"></i><span
                        class="menu-title" data-i18n="Search">Search</span></a>
            </li>
            <li class=" nav-item"><a href="/console/page-invoice"><i class="feather icon-file"></i><span
                        class="menu-title" data-i18n="Invoice">Invoice</span></a>
            </li>
            <li class=" nav-item"><a href="/console/#"><i class="feather icon-zap"></i><span class="menu-title"
                        data-i18n="Starter kit">Starter kit</span></a>
                <ul class="menu-content">
                    <li><a
                            href="/console/../../../starter-kit/ltr/vertical-menu-template-semi-dark/sk-layout-2-columns"><i
                                class="feather icon-circle"></i><span class="menu-item" data-i18n="2 columns">2
                                columns</span></a>
                    </li>
                    <li><a
                            href="/console/../../../starter-kit/ltr/vertical-menu-template-semi-dark/sk-layout-fixed-navbar"><i
                                class="feather icon-circle"></i><span class="menu-item" data-i18n="Fixed navbar">Fixed
                                navbar</span></a>
                    </li>
                    <li><a
                            href="/console/../../../starter-kit/ltr/vertical-menu-template-semi-dark/sk-layout-floating-navbar"><i
                                class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Floating navbar">Floating navbar</span></a>
                    </li>
                    <li><a href="/console/../../../starter-kit/ltr/vertical-menu-template-semi-dark/sk-layout-fixed"><i
                                class="feather icon-circle"></i><span class="menu-item" data-i18n="Fixed layout">Fixed
                                layout</span></a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item"><a href="/console/#"><i class="feather icon-unlock"></i><span class="menu-title"
                        data-i18n="Authentication">Authentication</span></a>
                <ul class="menu-content">
                    <li><a href="/console/auth-login"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Login">Login</span></a>
                    </li>
                    <li><a href="/console/auth-register"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Register">Register</span></a>
                    </li>
                    <li><a href="/console/auth-forgot-password"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Forgot Password">Forgot Password</span></a>
                    </li>
                    <li><a href="/console/auth-reset-password"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Reset Password">Reset Password</span></a>
                    </li>
                    <li><a href="/console/auth-lock-screen"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Lock Screen">Lock Screen</span></a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item"><a href="/console/#"><i class="feather icon-file-text"></i><span class="menu-title"
                        data-i18n="Miscellaneous">Miscellaneous</span></a>
                <ul class="menu-content">
                    <li><a href="/console/page-coming-soon"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Coming Soon">Coming Soon</span></a>
                    </li>
                    <li><a href="/console/#"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Error">Error</span></a>
                        <ul class="menu-content">
                            <li><a href="/console/error-404"><i class="feather icon-circle"></i><span class="menu-item"
                                        data-i18n="404">404</span></a>
                            </li>
                            <li><a href="/console/error-500"><i class="feather icon-circle"></i><span class="menu-item"
                                        data-i18n="500">500</span></a>
                            </li>
                        </ul>
                    </li>
                    <li><a href="/console/page-not-authorized"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Not Authorized">Not Authorized</span></a>
                    </li>
                    <li><a href="/console/page-maintenance"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Maintenance">Maintenance</span></a>
                    </li>
                </ul>
            </li>
            <li class=" navigation-header"><span>Charts &amp; Maps</span>
            </li>
            <li class=" nav-item"><a href="/console/#"><i class="feather icon-pie-chart"></i><span class="menu-title"
                        data-i18n="Charts">Charts</span><span
                        class="badge badge badge-pill badge-success float-right mr-2">3</span></a>
                <ul class="menu-content">
                    <li><a href="/console/chart-apex"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Apex">Apex</span></a>
                    </li>
                    <li><a href="/console/chart-chartjs"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Chartjs">Chartjs</span></a>
                    </li>
                    <li><a href="/console/chart-echarts"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Echarts">Echarts</span></a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item"><a href="/console/maps-google"><i class="feather icon-map"></i><span
                        class="menu-title" data-i18n="Google Maps">Google Maps</span></a>
            </li>
            <li class=" navigation-header"><span>Extensions</span>
            </li>
            <li class=" nav-item"><a href="/console/ext-component-sweet-alerts"><i
                        class="feather icon-alert-circle"></i><span class="menu-title" data-i18n="Sweet Alert">Sweet
                        Alert</span></a>
            </li>
            <li class=" nav-item"><a href="/console/ext-component-toastr"><i class="feather icon-zap"></i><span
                        class="menu-title" data-i18n="Toastr">Toastr</span></a>
            </li>
            <li class=" nav-item"><a href="/console/ext-component-noui-slider"><i class="feather icon-sliders"></i><span
                        class="menu-title" data-i18n="NoUi Slider">NoUi
                        Slider</span></a>
            </li>
            <li class=" nav-item"><a href="/console/ext-component-file-uploader"><i
                        class="feather icon-upload-cloud"></i><span class="menu-title" data-i18n="File Uploader">File
                        Uploader</span></a>
            </li>
            <li class=" nav-item"><a href="/console/ext-component-quill-editor"><i class="feather icon-edit"></i><span
                        class="menu-title" data-i18n="Quill Editor">Quill Editor</span></a>
            </li>
            <li class=" nav-item"><a href="/console/ext-component-drag-drop"><i class="feather icon-droplet"></i><span
                        class="menu-title" data-i18n="Drag &amp; Drop">Drag &amp; Drop</span></a>
            </li>
            <li class=" nav-item"><a href="/console/ext-component-tour"><i class="feather icon-info"></i><span
                        class="menu-title" data-i18n="Tour">Tour</span></a>
            </li>
            <li class=" nav-item"><a href="/console/ext-component-clipboard"><i class="feather icon-copy"></i><span
                        class="menu-title" data-i18n="Clipboard">Clipboard</span></a>
            </li>
            <li class=" nav-item"><a href="/console/ ext-component-plyr"><i class="feather icon-film"></i><span
                        class="menu-title" data-i18n="Media player">Media player</span></a>
            </li>
            <li class=" nav-item"><a href="/console/ext-component-context-menu"><i
                        class="feather icon-more-horizontal"></i><span class="menu-title"
                        data-i18n="Context Menu">Context Menu</span></a>
            </li>
            <li class=" nav-item"><a href="/console/ext-component-swiper"><i class="feather icon-smartphone"></i><span
                        class="menu-title" data-i18n="swiper">swiper</span></a>
            </li>
            <li class=" nav-item"><a href="/console/ext-component-i18n"><i class="feather icon-globe"></i><span
                        class="menu-title" data-i18n="l18n">l18n</span></a>
            </li>
            <li class=" navigation-header"><span>Others</span>
            </li>
            <li class=" nav-item"><a href="/console/#"><i class="feather icon-menu"></i><span class="menu-title"
                        data-i18n="Menu Levels">Menu Levels</span></a>
                <ul class="menu-content">
                    <li><a href="/console/#"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Second Level">Second Level</span></a>
                    </li>
                    <li><a href="/console/#"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Second Level">Second Level</span></a>
                        <ul class="menu-content">
                            <li><a href="/console/#"><i class="feather icon-circle"></i><span class="menu-item"
                                        data-i18n="Third Level">Third Level</span></a>
                            </li>
                            <li><a href="/console/#"><i class="feather icon-circle"></i><span class="menu-item"
                                        data-i18n="Third Level">Third Level</span></a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="disabled nav-item"><a href="/console/#"><i class="feather icon-eye-off"></i><span
                        class="menu-title" data-i18n="Disabled Menu">Disabled Menu</span></a>
            </li>
            <li class=" navigation-header"><span>Support</span>
            </li>
            <li class=" nav-item"><a
                    href="/console/https://pixinvent.com/demo/vuexy-html-bootstrap-admin-template/documentation"><i
                        class="feather icon-folder"></i><span class="menu-title"
                        data-i18n="Documentation">Documentation</span></a>
            </li>
            <li class=" nav-item"><a href="/console/https://pixinvent.ticksy.com/"><i
                        class="feather icon-life-buoy"></i><span class="menu-title" data-i18n="Raise Support">Raise
                        Support</span></a>
            </li>
        </ul>
    </div>
</div>
<!-- END: Main Menu-->

<div id="SpinnerApp" style="display: none;" class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div id="SpinnerBreadCrumb" class="content-header row">

        </div>
        <div style="text-align: center;margin-top: 20%;min-height:0px;position: relative;" class="content-body">

            <div class="spinner-border" style="width: 3rem; height: 3rem;">
                <span class="sr-only"><?= StaticFunctions::lang('Yükleniyor...') ?></span>
            </div>

        </div>
    </div>
</div>