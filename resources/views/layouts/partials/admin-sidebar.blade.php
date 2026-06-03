<div class="sidebar">
    <h4>Clinic Admin</h4>

    <a href="{{ route('admin.dashboard') }}"
        class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        🏠 Dashboard
    </a>

    <!-- Specialties Dropdown -->
    <!-- <div class="submenu-wrapper">
        <button class="submenu-toggle {{ request()->routeIs('admin.specialties.*') ? 'active' : '' }}">
            🩺 Specialties
            <span class="arrow">▾</span>
        </button>
        <div class="submenu {{ request()->routeIs('admin.specialties.*') ? 'show' : '' }}">
            <a href="{{ route('admin.specialties.index') }}"
                class="{{ request()->routeIs('admin.specialties.index') ? 'active' : '' }}">
                📋 All Specialties
            </a>
        </div>
    </div> -->
    <a href="{{ route('admin.specialties.index') }}"
        class="{{ request()->routeIs('admin.specialties.*') ? 'active' : '' }}">
        🩺 Specialties
    </a>

    <a href="{{ route('admin.doctors.index') }}"
        class="{{ request()->routeIs('admin.doctors.*') ? 'active' : '' }}">
        👨‍⚕️ Doctors
    </a>

    <a href="{{ route('admin.availabilities.index') }}"
        class="{{ request()->routeIs('admin.availabilities.*') ? 'active' : '' }}">
        ⏰ Availabilities
    </a>

    <a href="{{ route('admin.services.index') }}"
        class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
        📋 Services
    </a>

    <a href="{{ route('admin.appointments.index') }}"
        class="{{ request()->routeIs('admin.appointments.*') ? 'active' : '' }}">
        📅 Appointments
    </a>

    <a href="{{ route('admin.banners.index') }}"
        class="{{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
        🖼️ Banners
    </a>

    <a href="{{ route('admin.settings.index') }}"
        class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
        ⚙️ Settings
    </a>


</div>