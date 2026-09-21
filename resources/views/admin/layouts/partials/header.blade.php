<header class="topbar">
    <button class="icon-btn" type="button" id="sidebarCollapse" aria-label="Collapse sidebar">
        <i class="bi bi-layout-sidebar-inset"></i>
    </button>
    <button class="icon-btn d-lg-none" type="button" id="sidebarToggle" aria-label="Open sidebar">
        <i class="bi bi-list"></i>
    </button>
    <div class="topbar-actions">
        <button class="icon-btn" type="button" id="themeToggle" aria-label="Toggle color mode">
            <i class="bi bi-moon-stars"></i>
        </button>

        <div class="dropdown">
            <button class="profile-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <span class="avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}</span>
                <span class="profile-copy">{{ auth()->user()?->name ?? 'Admin' }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-item-text">Signed in as Admin</span></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item" type="submit">Sign out</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
