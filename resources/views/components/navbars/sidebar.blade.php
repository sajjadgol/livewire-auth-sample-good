<aside
    class="sidenav navbar navbar-vertical navbar-expand-xs border-0  fixed-start bg-gradient-dark"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-0 p-4 h-50 d-flex align-items-center text-wrap" href="{{ route('dashboard') }}">
                @if(config('app_settings.app_logo.value'))
                    <img src="{{  Storage::disk(config('app_settings.filesystem_disk.value'))->url(config('app_settings.app_logo.value')) }} " class="navbar-brand-img h-65" alt="main_logo">
                @else
                    <img src="{{ asset('assets') }}/img/logo-ct.png  " class="navbar-brand-img h-65" alt="main_logo">
                @endif
                <span class="ms-2  mt-3 font-weight-bold text-white h5 ">
                    @role('Provider') {{session('store_name')}} |  @endrole {{ config('app_settings.app_name.value') ?? config('app.name')}}
                </span>                
            </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    
    <div class="collapse navbar-collapse  w-auto h-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            @can('user-management')
                <li class="nav-item mt-3">
                    <h6 class="ps-4  ms-2 text-uppercase text-xs font-weight-bolder text-white">USERS MANAGEMENT</h6>
                </li>          
             
                <li class="nav-item">
                    <a class="nav-link text-white {{ in_array(Route::currentRouteName(), ['user-management', 'ticket-management', 'unverified-driver', 'add-user', 'edit-user']) ? 'active' : ''  }}"
                        data-bs-toggle="collapse" aria-expanded="false" href="#users">
                        <span class="material-symbols-outlined">groups</span>
                        <span class="sidenav-normal ms-2 ps-1">Users<b class="caret"></b></span>
                    </a>                    
                    <div class="collapse {{ in_array(Route::currentRouteName(), ['user-management', 'ticket-management' , 'unverified-driver', 'add-user', 'edit-user']) ? 'show' : '' }} "
                            id="users">
                    <ul class="nav nav-sm flex-column ms-2">
              
                        @can('user-management')
                            <li class="nav-item {{ in_array(Route::currentRouteName(), ['user-management', 'add-user', 'edit-user']) && Route::current()->parameter('role') == '' ? 'active' : '' }}">
                                <a data-bs-toggle="" href="{{ route('user-management') }}"
                                    class="nav-link text-white  {{ in_array(Route::currentRouteName(), ['user-management', 'add-user', 'edit-user']) && Route::current()->parameter('role') == '' ? 'active' : '' }} "
                                    aria-controls="dashboardsExamples" role="button" aria-expanded="false">
                                    <span class="material-symbols-outlined">
                                    group
                                    </span>
                                    <span class="nav-link-text ms-2 ps-1">All Users</span>
                                </a>
                            </li> 
                        @endcan
            
                        @can('ticket-management')
                            <li class="nav-item {{ strpos(Request::route()->uri(), 'tickets') === false ? '' : 'active'  }}">
                                <a data-bs-toggle="" href="{{ route('ticket-management') }}"
                                    class="nav-link text-white  {{ strpos(Request::route()->uri(), 'tickets') === false ? '' : 'active'  }} "
                                    aria-controls="dashboardsExamples" role="button" aria-expanded="false">
                                    <span class="material-symbols-outlined">
                                        manage_accounts
                                    </span>
                                    <span class="nav-link-text ms-2 ps-1">Update Requests</span>
                                </a>
                            </li> 
                        @endcan   

                        @can('unverified-driver')
                        <li class="nav-item">
                            <a class="nav-link text-white {{ Route::currentRouteName() == 'unverified-driver' ? 'active' : '' }}"
                                href="{{  route('unverified-driver', ['role' => 'driver', 'account_status' => 'waiting'] ) }}">
                                <span class="material-symbols-outlined">
                                no_accounts
                                </span>
                                <span class="sidenav-normal  ms-2 ps-1">Unverified Drivers </span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>               
                

            @endcan
            <li class="nav-item mt-3">
                <h6 class="ps-4  ms-2 text-uppercase text-xs font-weight-bolder text-white">ECOMMERCE</h6>
            </li>
            @can('store-management', 'unverified-stores', 'store-type-management')
                <li class="nav-item">
                        <a class="nav-link text-white {{ in_array(Route::currentRouteName(), ['store-management', 'unverified-stores', 'store-type-management']) ? 'active' : ''  }}"
                            data-bs-toggle="collapse" aria-expanded="false" href="#stores">
                            <span class="material-symbols-outlined">
                                store
                            </span>
                            <span class="sidenav-normal ms-2 ps-1">Stores<b class="caret"></b></span>
                        </a>
                    
                        <div class="collapse {{ in_array(Route::currentRouteName(), ['store-management', 'unverified-stores',  'store-type-management']) ? 'show' : '' }} "
                            id="stores">
                   <ul class="nav nav-sm flex-column ms-2">
                    @can('store-management')
                        <li class="nav-item">
                            <a data-bs-toggle="" href="{{ route('store-management') }}"
                                class="nav-link text-white {{  in_array(Route::currentRouteName(), ['store-management', 'add-store', 'edit-store']) ? 'active' : '' }} "
                                aria-controls="dashboardsExamples" role="button" aria-expanded="false">
                                <span class="material-symbols-outlined">
                                    store
                                    </span>
                                <span class="nav-link-text ms-2 ps-1">All Stores</span>
                            </a>
                        </li>
                    @endcan
                    @can('unverified-stores')
                        <li class="nav-item">
                            <a data-bs-toggle="" href="{{ route('unverified-stores', ['application_status' => 'waiting']) }}"
                                class="nav-link text-white {{ Route::currentRouteName() == 'unverified-stores'? 'active' : '' }}"
                                aria-controls="dashboardsExamples" role="button" aria-expanded="false">
                                <span class="material-symbols-outlined">
                                    unpublished
                                    </span>
                                <span class="nav-link-text ms-2 ps-1">Unverified Stores</span>
                            </a>
                        </li>
                    @endcan
                    @can('store-type-management')
                        <li class="nav-item ">
                            <a class="nav-link text-white {{ strpos(Request::route()->uri(), 'store-types') === false ? '' : 'active' }}" href="{{ route('store-type-management') }}">
                                <span class="material-symbols-outlined">
                                    local_convenience_store
                                    </span>
                                <span class="sidenav-normal ms-2 ps-1"> Store Types</span>
                            </a>
                        </li>
                    @endcan  
                    
                    </ul>
                </div>
            </li>
             @endcan

                @can('product-management', 'product-category-management', 'product-tag-management', 'product-addon-management')
                  
                       <li class="nav-item ">
                            <a class="nav-link text-white {{ strpos(Request::route()->uri(), 'products') === false ? '' : 'active' }}  "
                                data-bs-toggle="collapse" aria-expanded="false" href="#projectsExample">
                                <span class="material-symbols-outlined">
                                    inventory_2
                                </span>
                                <span class="sidenav-normal  ms-2 ps-1"> Products <b class="caret"></b></span>
                            </a>
                            <div class="collapse {{ strpos(Request::route()->uri(), 'products') === false ? '' : 'show' }}   "
                                id="projectsExample">
                                <ul class="nav nav-sm flex-column ms-2">
                                @can('product-management')
                                    <li class="nav-item">
                                        <a class="nav-link text-white {{ Route::currentRouteName() == 'product-management' ? 'active' : '' }} "
                                            href="{{ route('product-management') }}">
                                            <span class="material-symbols-outlined">
                                            inventory
                                            </span>
                                            <span class="sidenav-normal ms-2 ps-1"> Products </span>
                                        </a>
                                    </li>
                                @endcan
                                @can('product-category-management')
                                    <li class="nav-item">
                                        <a class="nav-link text-white {{ Route::currentRouteName() == 'product-category-management' ? 'active' : '' }}"
                                            href="{{ route('product-category-management') }}">
                                            <span class="material-symbols-outlined">
                                                category
                                            </span>
                                            <span class="sidenav-normal ms-2 ps-1"> Categories </span>
                                         </a>
                                    </li>
                                @endcan
                                @can('product-addon-management')
                                    <li class="nav-item">
                                        <a class="nav-link text-white {{ Route::currentRouteName() == 'product-addon-management' ? 'active' : '' }}"
                                            href="{{ route('product-addon-management') }}">
                                            <span class="material-symbols-outlined">
                                            menu_book
                                            </span>
                                            <span class="sidenav-normal ms-2 ps-1"> Addon Options </span>
                                        </a>
                                    </li>
                                @endcan
                                @can('product-tag-management')
                                    <li class="nav-item">
                                        <a class="nav-link text-white {{ strpos(Request::route()->uri(), 'products/tags')=== false ? '' : 'active' }}"
                                            href="{{ route('product-tag-management') }}">
                                            <span class="material-symbols-outlined">
                                            label
                                            </span>
                                            <span class="sidenav-normal ms-3 ps-1"> Tags </span>
                                        </a>
                                    </li>
                                @endcan
                                </ul>
                            </div>
                        </li>
                    @endcan

                    @can('order-management')

                        <li class="nav-item">
                            <a data-bs-toggle="" href="{{ route('order-management') }}"
                                class="nav-link text-white {{ (Route::currentRouteName() == 'order-management') ? 'active' : ''  }}"
                                aria-controls="review" role="button" aria-expanded="false">
                                <span class="material-symbols-outlined">
                                    shopping_cart
                                </span>
                                <span class="nav-link-text ms-2 ps-1">Orders</span>
                            </a>
                        </li>
 
                    @endcan

                   @can('transaction-management')
                        <li class="nav-item">
                            <a data-bs-toggle="" href="{{ route('transaction-management') }}"
                                class="nav-link text-white {{ strpos(Request::route()->uri(), 'transactions') === false ? '' : 'active'  }}"
                                aria-controls="transaction-management" role="button" aria-expanded="false">
                                <span class="material-symbols-outlined">
                                    payments
                                </span>
                                <span class="nav-link-text ms-2 ps-1">Transactions</span>
                            </a>
                        </li>
                   @endcan

                    @can('review-management')
                        <li class="nav-item">
                            <a data-bs-toggle="" href="{{ route('review-management') }}"
                                class="nav-link text-white {{ strpos(Request::route()->uri(), 'reviews') === false ? '' : 'active'  }}"
                                aria-controls="review" role="button" aria-expanded="false">
                                <span class="material-symbols-outlined">
                                    reviews
                                </span>
                                <span class="nav-link-text ms-2 ps-1">Reviews</span>
                            </a>
                        </li>
                    @endcan
 
                    @can('promotion-management') 
                        <li class="nav-item">
                            <a data-bs-toggle="" href="{{ route('promotion-management') }}"
                                class="nav-link text-white {{ strpos(Request::route()->uri(), 'promotions') === false ? '' : 'active'  }}"
                                aria-controls="dashboardsExamples" role="button" aria-expanded="false">
                                <span class="material-symbols-outlined">
                                    campaign
                                    </span>
                                <span class="nav-link-text ms-2 ps-1">Promotions</span>
                            </a>
                        </li>
                    @endcan

                    @role('Provider') 
                       @can('store-promotion') 
                            <li class="nav-item">
                                <a data-bs-toggle="" href="{{ route('store-promotion') }}"
                                    class="nav-link text-white {{ strpos(Request::route()->uri(), 'promotion/store/promotion') === false ? '' : 'active'  }}"
                                    aria-controls="dashboardsExamples" role="button" aria-expanded="false">
                                    <span class="material-symbols-outlined">
                                        campaign
                                        </span>
                                    <span class="nav-link-text ms-2 ps-1">Promotions</span>
                                </a>
                            </li>
                        @endcan
                    @endrole 

           @role('Provider') 
                @can('revenues-management') 
                    <li class="nav-item">
                        <a data-bs-toggle="" href="{{ route('revenues-management') }}"
                            class="nav-link text-white {{ strpos(Request::route()->uri(), 'revenues') === false ? '' : 'active'  }}"
                            aria-controls="dashboardsExamples" role="button" aria-expanded="false">
                            <span class="material-symbols-outlined">
                                 account_balance_wallet
                             </span>
                            <span class="nav-link-text ms-2 ps-1">Revenues</span>
                        </a>
                    </li>
                @endcan
                      
                <li class="nav-item mt-3">
                    <h6 class="ps-4  ms-2 text-uppercase text-xs font-weight-bolder text-white">SETTINGS</h6>
                </li>
               @can('provider-manage-store')
                    <li class="nav-item">
                        <a data-bs-toggle="" href="{{ route('provider-manage-store') }}"
                            class="nav-link text-white {{ Route::currentRouteName() == 'provider-manage-store'  ? 'active' : ''  }}"
                            aria-controls="provider-manage-store" role="button" aria-expanded="false">
                            <span class="material-symbols-outlined">
                                settings_applications
                            </span>
                            <span class="nav-link-text ms-2 ps-1">Settings</span>
                        </a>
                    </li>
                @endcan                        
            @endrole 

        @role('Admin')                
            <li class="nav-item mt-3">
                <h6 class="ps-4  ms-2 text-uppercase text-xs font-weight-bolder text-white">SETTINGS</h6>
            </li>
            <li class="nav-item ">
                <a class="nav-link text-white {{ in_array(Route::currentRouteName(), ['slider-management', 'tax-management', 'faq-management', 'page-management']) ? 'active' : '' }}"
                    data-bs-toggle="collapse" aria-expanded="false" href="#settings">
                    <span class="material-symbols-outlined">
                        display_settings
                    </span>
                    <span class="sidenav-normal ms-2 ps-1">Settings<b class="caret"></b></span>
                </a>
                <div class="collapse {{ in_array(Route::currentRouteName(), ['slider-management', 'tax-management', 'faq-management', 'page-management']) ? 'show' : '' }}"
                    id="settings">
                    <ul class="nav nav-sm flex-column ms-2">
                    @can('slider-management')
                        <li class="nav-item">
                            <a data-bs-toggle="" href="{{ route('slider-management') }}"
                                class="nav-link text-white {{ strpos(Request::route()->uri(), 'slider') === false ? '' : 'active'  }}"
                                aria-controls="dashboardsExamples" role="button" aria-expanded="false">
                                <span class="material-symbols-outlined">
                                    tune
                                    </span>
                                <span class="nav-link-text ms-2 ps-1">Sliders</span>
                            </a>
                        </li> 
                    @endcan            
                    @can('tax-management')
                        <li class="nav-item">
                            <a data-bs-toggle="" href="{{ route('tax-management') }}"
                                class="nav-link text-white {{ strpos(Request::route()->uri(), 'taxes') === false ? '' : 'active'  }}"
                                aria-controls="dashboardsExamples" role="button" aria-expanded="false">
                                <span class="material-symbols-outlined">
                                    Percent
                                    </span>
                                <span class="nav-link-text ms-2 ps-1">Taxes</span>
                            </a>
                        </li>
                    @endcan                
                    @can('faq-management')
                        <li class="nav-item">
                            <a data-bs-toggle="" href="{{ route('faq-management') }}"
                                class="nav-link text-white {{ strpos(Request::route()->uri(), 'faq') === false ? '' : 'active'  }}"
                                aria-controls="dashboardsExamples" role="button" aria-expanded="false">
                                <span class="material-symbols-outlined">
                                    quiz
                                    </span>
                                <span class="nav-link-text ms-2 ps-1">FAQ</span>
                            </a>
                        </li>
                    @endcan 
                    @can('page-management')
                        <li class="nav-item">
                            <a data-bs-toggle="" href="{{ route('page-management') }}"
                                class="nav-link text-white {{ strpos(Request::route()->uri(), 'pages') === false ? '' : 'active' }}"
                                aria-controls="dashboardsExamples" role="button" aria-expanded="false">
                                <span class="material-symbols-outlined">
                                    pages
                                    </span>
                                <span class="nav-link-text ms-2 ps-1">Pages</span>
                            </a>
                        </li>
                    @endcan
                    </ul>
                </div>
                
                @can('site-cache', 'site-settings', 'country-management', 'state-management', 'city-management', 'role-management', 'ecommerce-settings')
                    <li class="nav-item ">
                        <a class="nav-link text-white {{ in_array(Route::currentRouteName(), ['ecommerce-settings', 'site-cache', 'site-settings', 'country-management', 'state-management', 'city-management', 'role-management']) ? 'active' : ''}}   "
                            data-bs-toggle="collapse" aria-expanded="false" href="#systemSetting">
                            <span class="material-symbols-outlined">
                                admin_panel_settings
                            </span>
                            <span class="sidenav-normal ms-2 ps-1">Administration <b class="caret"></b></span>
                        </a>
                        <div class="collapse {{ in_array(Route::currentRouteName(), ['ecommerce-settings', 'site-cache', 'site-settings', 'country-management', 'state-management', 'city-management', 'role-management']) ? 'show' : '' }} "
                            id="systemSetting">
                            <ul class="nav nav-sm flex-column ms-2">                            
                                @can('ecommerce-settings')
                                    <li class="nav-item">
                                        <a data-bs-toggle="" href="{{ route('ecommerce-settings') }}"
                                            class="nav-link text-white {{ strpos(Request::route()->uri(), 'ecommerce-settings') === false ? '' : 'active' }} "
                                            aria-controls="dashboardsExamples" role="button" aria-expanded="false">
                                            <span class="material-symbols-outlined">
                                                settings
                                            </span>
                                            <span class="nav-link-text ms-2 ps-1">Ecommerce Setting</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('site-settings')
                                    <li class="nav-item">
                                        <a href="{{ route('site-settings') }}"
                                            class="nav-link text-white {{ Route::currentRouteName() == 'site-settings' ? 'active' : '' }}"
                                            aria-controls="dashboardsExamples" role="button" aria-expanded="false">
                                            <span class="material-symbols-outlined">
                                                display_settings
                                            </span>
                                            <span class="nav-link-text ms-2 ps-1">General Settings</span>
                                        </a>
                                    </li>
                                @endcan                             
                                @can('country-management', 'state-management', 'city-management')
                                    <li class="nav-item ">
                                        <a class="nav-link text-white  {{ strpos(Request::route()->uri(), 'location')=== false ? '' : 'active' }}"
                                            data-bs-toggle="collapse" aria-expanded="false" href="#LocationExample">
                                            <span class="material-symbols-outlined">
                                            public
                                                </span>
                                            <span class="sidenav-normal  ms-2 ps-1"> Location <b class="caret"></b></span>
                                        </a>
                                        <div class="collapse {{ strpos(Request::route()->uri(), 'location')=== false ? '' : 'show' }}"
                                            id="LocationExample">
                                            <ul class="nav nav-sm flex-column ms-2">
                                                <li class="nav-item">
                                                    <a class="nav-link text-white {{ Route::currentRouteName() == 'country-management' ? 'active' : '' }}"
                                                        href="{{ route('country-management') }}">
                                                        <span class="material-symbols-outlined">
                                                            home_pin
                                                            </span>
                                                        <span class="nav-link-text ms-2 ps-1">Country</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link text-white {{ Route::currentRouteName() == 'state-management' ? 'active' : '' }}"
                                                        href="{{ route('state-management') }}">
                                                        <span class="material-symbols-outlined">
                                                            flag
                                                            </span>
                                                        <span class="nav-link-text ms-2 ps-1">State</span>

                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link text-white {{ Route::currentRouteName() == 'city-management' ? 'active' : '' }}"
                                                        href="{{ route('city-management') }} ">
                                                        <span class="material-symbols-outlined">
                                                            pin_drop
                                                            </span>
                                                        <span class="nav-link-text ms-2 ps-1">City</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                @endcan                      
                            
                                <!-- @can('role-management')
                                    <li class="nav-item ">
                                        <a class="nav-link text-white {{ strpos(Request::route()->uri(), 'roles') === false ? '' : 'active' }}   "
                                            data-bs-toggle="collapse" aria-expanded="false" href="#usersExample">
                                            <span class="material-symbols-outlined">
                                                group
                                                </span>
                                            <span class="sidenav-normal  ms-2 ps-1"> Roles <b class="caret"></b></span>
                                        </a>
                                        <div class="collapse {{ strpos(Request::route()->uri(), 'roles') === false ? '' : 'show' }} "
                                            id="usersExample">
                                            <ul class="nav nav-sm flex-column ms-2">
                                                <li class="nav-item">
                                                    <a class="nav-link text-white {{ Route::currentRouteName() == 'role-management'  || Route::currentRouteName() == 'edit-role' ? 'active' : '' }} "
                                                        href="{{ route('role-management') }}">
                                                        <span class="material-symbols-outlined">
                                                            table_view
                                                        </span>
                                                        <span class="sidenav-normal  ms-3  ps-1"> List </span>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link text-white {{ Route::currentRouteName() == 'new-role' ? 'active' : '' }}"
                                                        href="{{ route('new-role') }}">
                                                        <span class="material-symbols-outlined">
                                                            group_add
                                                        </span>
                                                        <span class="sidenav-normal  ms-3  ps-1"> Add New Role </span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                @endcan -->                                   
                                @can('site-cache')
                                    <li class="nav-item">
                                        <a class="nav-link text-white {{ Route::currentRouteName() == 'site-cache' ? 'active' : '' }} "
                                            href="{{ route('site-cache') }}">
                                            <span class="material-symbols-outlined">
                                                cached
                                            </span>
                                            <span class="sidenav-normal ms-3 ps-1"> Cache management</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcan
            @endrole                    
            </ul>
        </div>


</aside>
