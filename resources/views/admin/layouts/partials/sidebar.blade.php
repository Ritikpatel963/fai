@php
    $activePage = trim($__env->yieldContent('page', 'index'));
    $navItems = [
        [
            'page' => 'index',
            'route' => 'admin.dashboard',
            'icon' => 'bi-grid-1x2-fill',
            'label' => 'Dashboard',
        ],
        [
            'page' => 'blogs',
            'route' => 'admin.blogs.index',
            'icon' => 'bi-file-text',
            'label' => 'Blogs',
            'submenus' => [
                ['page' => 'blogs.all', 'route' => 'admin.blogs.index', 'label' => 'All Blogs'],
                ['page' => 'blogs.create', 'route' => 'admin.blogs.create', 'label' => 'Add New'],
                ['page' => 'categories', 'route' => 'admin.categories.index', 'label' => 'Categories'],
                ['page' => 'tags', 'route' => 'admin.tags.index', 'label' => 'Tags'],
            ]
        ],
        [
            'page' => 'media',
            'route' => 'admin.media.index',
            'icon' => 'bi-images',
            'label' => 'Media Library',
        ],
        [
            'page' => 'resources',
            'route' => 'admin.resources.index',
            'icon' => 'bi-folder',
            'label' => 'Resources',
        ],
        [
            'page' => 'contact_leads',
            'route' => 'admin.contact-leads.index',
            'icon' => 'bi-envelope',
            'label' => 'Contact Leads',
            'id' => 'navContactLeads'
        ],
        [
            'page' => 'settings',
            'route' => 'admin.settings.index',
            'icon' => 'bi-gear',
            'label' => 'Site Settings',
        ],
        [
            'page' => 'testimonials',
            'route' => 'admin.testimonials.index',
            'icon' => 'bi-chat-quote',
            'label' => 'Testimonials',
        ],
        [
            'page' => 'seo-settings',
            'route' => 'admin.seo-settings.index',
            'icon' => 'bi-search',
            'label' => 'SEO Settings',
        ],
        [
            'page' => 'profile',
            'route' => 'admin.profile.index',
            'icon' => 'bi-person',
            'label' => 'My Profile',
        ]
    ];
@endphp
<aside class="sidebar" id="sidebar">
    <a class="brand text-decoration-none" href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : url('/') }}">
        <div><strong>Bio Agriculture</strong><small>Admin Panel</small></div>
    </a>
    <nav class="sidebar-nav">
        @foreach ($navItems as $item)
            @if(isset($item['submenus']))
                <div class="nav-item has-submenu">
                    <a class="nav-link d-flex align-items-center {{ str_starts_with($activePage, 'blogs') || in_array($activePage, ['categories', 'tags']) ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#submenu-{{ $item['page'] }}">
                        <i class="bi {{ $item['icon'] }}"></i><span>{{ $item['label'] }}</span>
                        <i class="bi bi-chevron-down ms-auto submenu-icon" style="font-size: 0.8rem;"></i>
                    </a>
                    <div class="collapse {{ str_starts_with($activePage, 'blogs') || in_array($activePage, ['categories', 'tags']) ? 'show' : '' }}" id="submenu-{{ $item['page'] }}">
                        <ul class="list-unstyled ps-4 ms-2 mt-1 border-start border-2 border-secondary-subtle">
                            @foreach($item['submenus'] as $submenu)
                                <li class="mb-1">
                                    <a class="nav-link py-1 px-3 {{ $activePage === $submenu['page'] ? 'text-primary fw-bold' : '' }}" style="font-size: 0.9rem; opacity: 0.85;" href="{{ Route::has($submenu['route']) ? route($submenu['route']) : '#' }}">
                                        {{ $submenu['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @else
                <a class="nav-link {{ $activePage === $item['page'] ? 'active' : '' }}" href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}" id="{{ $item['id'] ?? '' }}">
                    <i class="bi {{ $item['icon'] }}"></i><span>{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="logout" type="submit">
            <i class="bi bi-box-arrow-right"></i><span>Logout</span>
        </button>
    </form>
</aside>
