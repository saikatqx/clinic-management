<header class="d-flex justify-content-between align-items-center p-3 bg-white border-bottom">
    <h5 class="mb-0">@yield('page-title', 'Admin Dashboard')</h5>
    <div>
        <span class="me-3">{{ Auth::user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button class="btn btn-sm btn-danger">Logout</button>
        </form>
    </div>
</header>
