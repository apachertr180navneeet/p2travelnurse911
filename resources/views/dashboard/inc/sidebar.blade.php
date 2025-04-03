<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route("dashboard.home") }}" class="brand-link">
        <span class="brand-text font-weight-bold px-2">News</span>
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset("public/assets/images/author/".(auth()->user()->profile ?? "default.webp")) }}" class="img-circle elevation-2" alt="{{ auth()->user()->name }}"/>
            </div>
            <div class="info">
                <a target="_blank" href="{{ route("dashboard.settings.profile") }}" class="d-block">{{ auth()->user()->name }}</a>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route("dashboard.home") }}" class="nav-link {{ request()->route()->getName() == "dashboard.home" ? "active" : "" }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item {{ in_array(request()->route()->getName(), ["dashboard.posts.index", "dashboard.posts.create","dashboard.posts.edit", "dashboard.posts.trashed"]) ? "menu-open" : "" }}">
                    <a href="#" class="nav-link {{ in_array(request()->route()->getName(), ["dashboard.posts.index", "dashboard.posts.create", "dashboard.posts.edit", "dashboard.posts.trashed"]) ? "active" : "" }}">
                        <i class="nav-icon fas fa-pencil-alt"></i>
                        <p>News<i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route("dashboard.posts.index") }}" class="nav-link {{ request()->route()->getName() == "dashboard.posts.index" ? "active" : "" }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All News</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("dashboard.posts.create") }}" class="nav-link {{ request()->route()->getName() == "dashboard.posts.create" ? "active" : "" }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add News</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("dashboard.posts.trashed") }}" class="nav-link {{ request()->route()->getName() == "dashboard.posts.trashed" ? "active" : "" }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Trashed News</p>
                            </a>
                        </li>
                    </ul>
                </li>             
               
               
                <li class="nav-item {{ in_array(request()->route()->getName(), ["dashboard.categories.index", "dashboard.categories.create","dashboard.categories.edit", "dashboard.categories.trashed"]) ? "menu-open" : "" }}">
                    <a href="#" class="nav-link {{ in_array(request()->route()->getName(), ["dashboard.categories.index", "dashboard.categories.create", "dashboard.categories.edit", "dashboard.categories.trashed"]) ? "active" : "" }}">
                        <i class="nav-icon fas fa-th-list"></i>
                        <p>Categories<i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route("dashboard.categories.index") }}" class="nav-link {{ request()->route()->getName() == "dashboard.categories.index" ? "active" : "" }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Categories</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("dashboard.categories.create") }}" class="nav-link {{ request()->route()->getName() == "dashboard.categories.create" ? "active" : "" }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Category</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("dashboard.categories.trashed") }}" class="nav-link {{ request()->route()->getName() == "dashboard.categories.trashed" ? "active" : "" }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Trashed Categories</p>
                            </a>
                        </li>
                        
                    </ul>
                </li>  


                <li class="nav-item {{ in_array(request()->route()->getName(), ["dashboard.marketplaces.index", "dashboard.marketplaces.create","dashboard.marketplaces.edit", "dashboard.marketplaces.trashed"]) ? "menu-open" : "" }}">
                    <a href="#" class="nav-link {{ in_array(request()->route()->getName(), ["dashboard.marketplaces.index", "dashboard.marketplaces.create", "dashboard.marketplaces.edit", "dashboard.marketplaces.trashed"]) ? "active" : "" }}">
                        <i class="nav-icon fas fa-th-list"></i>
                        <p>Marketplaces<i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route("dashboard.marketplaces.index") }}" class="nav-link {{ request()->route()->getName() == "dashboard.marketplaces.index" ? "active" : "" }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Marketplaces</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("dashboard.marketplaces.create") }}" class="nav-link {{ request()->route()->getName() == "dashboard.marketplaces.create" ? "active" : "" }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add MarketPlace</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("dashboard.marketplaces.trashed") }}" class="nav-link {{ request()->route()->getName() == "dashboard.marketplaces.trashed" ? "active" : "" }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Trashed MarketPlace</p>
                            </a>
                        </li>
                        
                    </ul>
                </li>  


                <!--classified Ads-->

                <li class="nav-item {{ in_array(request()->route()->getName(), ["dashboard.classifieds.index","dashboard.classifieds.contactseller","dashboard.services.index"]) ? "menu-open" : "" }}">
                    <a href="#" class="nav-link {{ in_array(request()->route()->getName(), ["dashboard.classifieds.index","dashboard.classifieds.contactseller","dashboard.services.index"]) ? "active" : "" }}">
                        <i class="nav-icon fas fa-th-list"></i>
                        <p>Classified Ads<i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route("dashboard.classifieds.index") }}" class="nav-link {{ request()->route()->getName() == "dashboard.classifieds.index" ? "active" : "" }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Classified Ads</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("dashboard.classifieds.contactseller") }}" class="nav-link {{ request()->route()->getName() == "dashboard.classifieds.contactseller" ? "active" : "" }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Contact Us</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route("dashboard.services.index") }}" class="nav-link {{ request()->route()->getName() == "dashboard.services.index" ? "active" : "" }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Service</p>
                            </a>
                        </li>
                        
                        
                    </ul>
                </li>  
                
                

                
                <li class="nav-item">
                    <a href="{{ route("dashboard.tags.index") }}" class="nav-link {{ request()->route()->getName() == "dashboard.tags.index" ? "active" : "" }}">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>Tags<i class="right fas fa-angle-left"></i></p>
                    </a>
                </li>
                <li class="nav-item {{ in_array(request()->route()->getName(), ["dashboard.pages.index", "dashboard.pages.create", "dashboard.pages.edit", "dashboard.pages.trashed"]) ? "menu-open" : "" }}">
                    <a href="#" class="nav-link {{ in_array(request()->route()->getName(), ["dashboard.pages.index", "dashboard.pages.create", "dashboard.pages.edit", "dashboard.pages.trashed"]) ? "active" : "" }}">
                        <i class="nav-icon fas fa-copy"></i>
                        <p>Pages<i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route("dashboard.pages.index") }}" class="nav-link {{ request()->route()->getName() == "dashboard.pages.index" ? "active" : "" }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Pages</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("dashboard.pages.create") }}" class="nav-link {{ request()->route()->getName() == "dashboard.pages.create" ? "active" : "" }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>New Page</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("dashboard.pages.trashed") }}" class="nav-link {{ request()->route()->getName() == "dashboard.pages.trashed" ? "active" : "" }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Trashed Pages</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item {{ in_array(request()->route()->getName(), ["dashboard.subscribes.index"]) ? "menu-open" : "" }}">
                    <a href="#" class="nav-link {{ in_array(request()->route()->getName(), ["dashboard.subscribes.index"]) ? "active" : "" }}">
                        <i class="nav-icon fas fa-th-list"></i>
                        <p>Subscribes<i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route("dashboard.subscribes.index") }}" class="nav-link {{ request()->route()->getName() == "dashboard.subscribes.index" ? "active" : "" }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Subcribes</p>
                            </a>
                        </li>
                      
                    </ul>
                </li>



                <li class="nav-item {{ in_array(request()->route()->getName(), ["dashboard.vendorcategories.index", "dashboard.vendorcategories.create", "dashboard.vendorcategories.edit", "dashboard.vendorcategories.trashed","dashboard.vendorsubcategories.index", "dashboard.vendorsubcategories.create", "dashboard.vendorsubcategories.edit", "dashboard.vendorsubcategories.trashed","dashboard.vendor_agencies.index","dashboard.vendor_agency.getContactList" ,'dashboard.vendor_agency.getReviewsFeedback','dashboard.vendor_agency.getEmailList'  ]) ? "menu-open" : "" }}">
                    <a href="#" class="nav-link {{ in_array(request()->route()->getName(), ["dashboard.vendorcategories.index", "dashboard.vendorcategories.create", "dashboard.vendorcategories.edit", "dashboard.vendorcategories.trashed","dashboard.vendorsubcategories.index", "dashboard.vendorsubcategories.create", "dashboard.vendorsubcategories.edit", "dashboard.vendorsubcategories.trashed", "dashboard.vendor_agencies.index","dashboard.vendor_agency.getContactList",'dashboard.vendor_agency.getReviewsFeedback','dashboard.vendor_agency.getEmailList' ]) ? "active" : "" }}">
                        <i class="nav-icon fas fa-copy"></i>
                        <p>Vendor Directories<i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('dashboard.vendorcategories.index') }}" 
                               class="nav-link {{ in_array(request()->route()->getName(), ['dashboard.vendorcategories.index', 'dashboard.vendorcategories.create', 'dashboard.vendorcategories.edit', 'dashboard.vendorcategories.trashed']) ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Categories</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('dashboard.vendorsubcategories.index') }}" 
                               class="nav-link {{ in_array(request()->route()->getName(), ['dashboard.vendorsubcategories.index', 'dashboard.vendorsubcategories.create', 'dashboard.vendorsubcategories.edit', 'dashboard.vendorsubcategories.trashed']) ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Subcategories</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('dashboard.vendor_agencies.index') }}" class="nav-link {{ request()->routeIs('dashboard.vendor_agencies.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Vendors</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('dashboard.vendor_agency.getReviewsFeedback') }}" class="nav-link {{ request()->routeIs('dashboard.vendor_agency.getReviewsFeedback') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Reviews & Feedback</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('dashboard.vendor_agency.getContactList') }}" class="nav-link {{ request()->routeIs('dashboard.vendor_agency.getContactList') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Company List</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('dashboard.vendor_agency.getEmailList') }}" class="nav-link {{ request()->routeIs('dashboard.vendor_agency.getEmailList') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Email List</p>
                            </a>
                        </li>
                        
                    </ul>
                </li>

                
                <li class="nav-item {{ in_array(request()->route()->getName(), ["dashboard.settings.password", "dashboard.settings.menus.footer", "dashboard.settings.menus.header", "dashboard.settings.site", "dashboard.settings.profile","dashboard.settings.social.media"]) ? "menu-open" : "" }}">
                    <a href="#" class="nav-link {{ in_array(request()->route()->getName(), ["dashboard.settings.password", "dashboard.settings.menus.footer", "dashboard.settings.menus.header", "dashboard.settings.site", "dashboard.settings.profile", "dashboard.settings.social.media"]) ? "active" : "" }}">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Settings<i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route("dashboard.settings.profile") }}" class="nav-link {{ request()->route()->getName() == "dashboard.settings.profile" ? "active" : "" }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Profile</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("dashboard.settings.password") }}" class="nav-link {{ request()->route()->getName() == "dashboard.settings.password" ? "active" : "" }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Change Password</p>
                            </a>
                        </li>
                       
                        <li class="nav-item">
                            <a href="{{ route("dashboard.settings.site") }}" class="nav-link {{ request()->route()->getName() == "dashboard.settings.site" ? "active" : "" }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Site Settings</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("dashboard.settings.social.media") }}" class="nav-link {{ request()->route()->getName() == "dashboard.settings.social.media" ? "active" : "" }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Social Media</p>
                            </a>
                        </li>
                        <li class="nav-item {{ in_array(request()->route()->getName(), ["dashboard.settings.menus.header", "dashboard.settings.menus.footer"]) ? "menu-open" : "" }}">
                            <a href="#" class="nav-link {{ in_array(request()->route()->getName(), ["dashboard.settings.menus.header", "dashboard.settings.menus.footer"]) ? "active" : "" }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    Menus
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route("dashboard.settings.menus.header") }}" class="nav-link {{ request()->route()->getName() == "dashboard.settings.menus.header" ? "active" : "" }}">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Header Menu</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route("dashboard.settings.menus.footer") }}" class="nav-link {{ request()->route()->getName() == "dashboard.settings.menus.footer" ? "active" : "" }}">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Footer Menu</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                      
                    </ul>
                </li>
                <li class="nav-header"></li>
                <li class="nav-item">
                    <a class="btn nav-link text-left" onclick="document.getElementById('logout').submit()">
                        <form method="POST" id="logout" action="{{ route("auth.logout") }}">
                            @csrf
                            <i class="nav-icon fa fa-sign-out-alt"></i>
                            <p>Log Out</p>
                        </form>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
