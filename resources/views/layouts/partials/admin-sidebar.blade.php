<div class="sidebar">
    <h4>Clinic Admin</h4>

    @can('view dashboard')
    <a href="{{ route('admin.dashboard') }}"
        class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        🏠 Dashboard
    </a>
    @endcan

    @can('view specialties')
    <a href="{{ route('admin.specialties.index') }}"
        class="{{ request()->routeIs('admin.specialties.*') ? 'active' : '' }}">
        🩺 Specialties
    </a>
    @endcan

    @can('view doctors')
    <a href="{{ route('admin.doctors.index') }}"
        class="{{ request()->routeIs('admin.doctors.*') ? 'active' : '' }}">
        👨‍⚕️ Doctors
    </a>
    @endcan

    @can('view availabilities')
    <a href="{{ route('admin.availabilities.index') }}"
        class="{{ request()->routeIs('admin.availabilities.*') ? 'active' : '' }}">
        ⏰ Availabilities
    </a>
    @endcan

    @can('view services')
    <a href="{{ route('admin.services.index') }}"
        class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
        📋 Services
    </a>
    @endcan

    @can('view appointments')
    <a href="{{ route('admin.appointments.index') }}"
        class="{{ request()->routeIs('admin.appointments.*') ? 'active' : '' }}">
        📅 Appointments
    </a>
    @endcan

    @can('view banners')
    <a href="{{ route('admin.banners.index') }}"
        class="{{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
        🖼️ Banners
    </a>
    @endcan

    @canany(['view diagnostic categories', 'view diagnostic tests'])
    <!-- Diagnostic Master Setup Dropdown -->
    <div class="submenu-wrapper">
        <button class="submenu-toggle {{ (request()->routeIs('admin.diagnostic-categories.*') || request()->routeIs('admin.diagnostics.indexDiag') || request()->routeIs('admin.diagnostics.indexPath')) ? 'active' : '' }}">
            <span>📁 Diagnostic Master</span>
            <span class="arrow">▾</span>
        </button>
        <div class="submenu {{ (request()->routeIs('admin.diagnostic-categories.*') || request()->routeIs('admin.diagnostics.indexDiag') || request()->routeIs('admin.diagnostics.indexPath')) ? 'show' : '' }}">
            @can('view diagnostic categories')
            <a href="{{ route('admin.diagnostic-categories.index') }}"
                class="{{ request()->routeIs('admin.diagnostic-categories.*') ? 'active' : '' }}">
                📁 Categories
            </a>
            @endcan
            @can('view diagnostic tests')
            <a href="{{ route('admin.diagnostics.indexDiag') }}"
                class="{{ request()->routeIs('admin.diagnostics.indexDiag') ? 'active' : '' }}">
                🩺 Diagnostics
            </a>
            <a href="{{ route('admin.diagnostics.indexPath') }}"
                class="{{ request()->routeIs('admin.diagnostics.indexPath') ? 'active' : '' }}">
                🧪 Pathology
            </a>
            @endcan
        </div>
    </div>
    @endcanany

    @can('view bookings')
    <!-- Bookings Dropdown -->
    <div class="submenu-wrapper">
        <button class="submenu-toggle {{ (request()->routeIs('admin.diagnostic-bookings.*') || request()->routeIs('admin.pathology-bookings.*') || request()->routeIs('admin.health-package-bookings.*')) ? 'active' : '' }}">
            <span>📅 Lab Bookings</span>
            <span class="arrow">▾</span>
        </button>
        <div class="submenu {{ (request()->routeIs('admin.diagnostic-bookings.*') || request()->routeIs('admin.pathology-bookings.*') || request()->routeIs('admin.health-package-bookings.*')) ? 'show' : '' }}">
            <a href="{{ route('admin.diagnostic-bookings.index') }}"
                class="{{ request()->routeIs('admin.diagnostic-bookings.*') ? 'active' : '' }}">
                🩺 Diagnostic Bookings
            </a>
            <a href="{{ route('admin.pathology-bookings.index') }}"
                class="{{ request()->routeIs('admin.pathology-bookings.*') ? 'active' : '' }}">
                🧪 Pathology Bookings
            </a>
            <a href="{{ route('admin.health-package-bookings.index') }}"
                class="{{ request()->routeIs('admin.health-package-bookings.*') ? 'active' : '' }}">
                ❤️ Package Bookings
            </a>
        </div>
    </div>
    @endcan

    @can('view health packages')
    <a href="{{ route('admin.health-packages.index') }}"
        class="{{ request()->routeIs('admin.health-packages.*') ? 'active' : '' }}">
        📦 Health Packages
    </a>
    @endcan

    @canany(['view roles', 'assign user roles', 'view permissions', 'view users'])
    <!-- Access Control Dropdown -->
    <div class="submenu-wrapper">
        <button class="submenu-toggle {{ (request()->routeIs('admin.roles.*') || request()->routeIs('admin.assign-role.*') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.users.*')) ? 'active' : '' }}">
            <span>🔐 Access Control</span>
            <span class="arrow">▾</span>
        </button>
        <div class="submenu {{ (request()->routeIs('admin.roles.*') || request()->routeIs('admin.assign-role.*') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.users.*')) ? 'show' : '' }}">
            @can('view roles')
            <a href="{{ route('admin.roles.index') }}"
                class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                - Role List
            </a>
            @endcan
            @can('assign user roles')
            <a href="{{ route('admin.assign-role.index') }}"
                class="{{ request()->routeIs('admin.assign-role.*') ? 'active' : '' }}">
                - Assign Role
            </a>
            @endcan
            @can('view permissions')
            <a href="{{ route('admin.permissions.index') }}"
                class="{{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                - Permission List
            </a>
            @endcan
            @can('view users')
            <a href="{{ route('admin.users.index') }}"
                class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                - User List
            </a>
            @endcan
        </div>
    </div>
    @endcanany

    @can('view settings')
    <a href="{{ route('admin.settings.index') }}"
        class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
        ⚙️ Settings
    </a>
    @endcan

</div>