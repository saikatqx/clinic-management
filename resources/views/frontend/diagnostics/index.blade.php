@extends('frontend.layout')

@section('title', 'Diagnostic & Pathology Tests')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="fw-bold text-primary"><i class="fa fa-microscope me-2"></i> Book Diagnostic & Pathology Tests</h1>
            <p class="text-muted">Select from our wide range of certified lab tests, add them to your booking, and schedule a home sample collection or clinic visit.</p>
        </div>

        <div class="row g-4">
            <!-- Catalog & Filters Column -->
            <div class="col-lg-8">
                <!-- Search & Filters -->
                <div class="card shadow-sm border-0 p-3 mb-4">
                    <form action="{{ route('diagnostics.index.public') }}" method="GET" class="row g-2">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control" placeholder="Search tests (e.g. CBC, ECG...)" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-5">
                            <select name="category" class="form-select select2">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }} ({{ ($category->type === 'diag') ? 'Diagnostic' : 'Pathology' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="fa fa-search"></i> Search</button>
                        </div>
                    </form>
                </div>

                <!-- Tests Catalog Grid -->
                <div class="row g-3">
                    @forelse($tests as $test)
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm border-0 test-card">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="test-image">
                                        <img src="{{ $test->image_url ? $test->image_url : asset('images/default.png') }}" class="rounded-3" style="width: 70px; height: 70px; object-fit: cover;">
                                    </div>
                                    <div class="test-details flex-grow-1">
                                        <span class="badge bg-light text-primary border mb-1 small">{{ optional($test->category)->name ?? 'General' }}</span>
                                        <h5 class="card-title mb-1 fw-bold fs-6 text-dark">{{ $test->name }}</h5>
                                        <div class="fw-bold text-success">₹{{ number_format($test->price, 2) }}</div>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-0 text-end pb-3 pt-0">
                                    <button class="btn btn-outline-primary btn-sm add-to-cart-btn" data-id="{{ $test->id }}" data-name="{{ $test->name }}" data-price="{{ $test->price }}">
                                        <i class="fa fa-plus me-1"></i> Add to Bookings
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="card p-5 text-center text-muted border-0 shadow-sm">
                                <i class="fa fa-search fa-3x mb-3 text-secondary"></i>
                                <h5>No tests found matching your criteria.</h5>
                                <p class="small">Try adjusting filters or searching for other keywords.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Interactive Cart Column -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 sticky-top" style="top: 90px; z-index: 100;">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="fa fa-shopping-basket me-2"></i> Selected Bookings</h5>
                    </div>
                    <div class="card-body p-3">
                        <div id="cart_empty_state" class="text-center py-5 text-muted">
                            <i class="fa fa-notes-medical fa-3x mb-3 text-secondary"></i>
                            <p class="mb-0">No tests selected yet.</p>
                            <small class="d-block mt-1">Select diagnostic tests from the catalog to book your appointment.</small>
                        </div>

                        <div id="cart_items_wrapper" class="d-none">
                            <ul class="list-group list-group-flush mb-3 overflow-y-auto" style="max-height: 250px;" id="cart_items_list">
                                <!-- Cart items dynamically inserted here -->
                            </ul>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="fw-semibold text-muted">Subtotal:</span>
                                <span class="fw-bold fs-4 text-success" id="cart_total_price">₹0.00</span>
                            </div>

                            <form action="{{ route('diagnostics.book.public') }}" method="POST">
                                @csrf
                                <input type="hidden" name="test_ids" id="test_ids_input">
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold fs-5">
                                    Proceed to Booking <i class="fa fa-arrow-right ms-1"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let cart = [];

    // Load from LocalStorage
    if (localStorage.getItem('diag_cart')) {
        try {
            cart = JSON.parse(localStorage.getItem('diag_cart'));
            updateCartUI();
        } catch(e) {
            cart = [];
        }
    }

    // Add button handler
    $(document).on('click', '.add-to-cart-btn', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const price = parseFloat($(this).data('price'));

        // Check if already in cart
        if (cart.some(item => item.id === id)) {
            toastr.warning(name + ' is already added to bookings.');
            return;
        }

        cart.push({ id, name, price });
        localStorage.setItem('diag_cart', JSON.stringify(cart));
        updateCartUI();
        toastr.success(name + ' added to bookings!');
    });

    // Remove from cart handler
    $(document).on('click', '.remove-cart-item', function() {
        const id = $(this).data('id');
        cart = cart.filter(item => item.id !== id);
        localStorage.setItem('diag_cart', JSON.stringify(cart));
        updateCartUI();
    });

    function updateCartUI() {
        const $list = $('#cart_items_list');
        const $empty = $('#cart_empty_state');
        const $wrapper = $('#cart_items_wrapper');
        const $total = $('#cart_total_price');
        const $input = $('#test_ids_input');

        // Reset Add buttons styling
        $('.add-to-cart-btn').removeClass('btn-success text-white').addClass('btn-outline-primary').html('<i class="fa fa-plus me-1"></i> Add to Bookings');

        if (cart.length === 0) {
            $empty.removeClass('d-none');
            $wrapper.addClass('d-none');
            $input.val('');
        } else {
            $empty.addClass('d-none');
            $wrapper.removeClass('d-none');

            $list.empty();
            let total = 0;
            let ids = [];

            cart.forEach(item => {
                total += item.price;
                ids.push(item.id);

                // highlight catalog button
                const btn = $(`.add-to-cart-btn[data-id="${item.id}"]`);
                if (btn.length) {
                    btn.removeClass('btn-outline-primary').addClass('btn-success text-white').html('<i class="fa fa-check me-1"></i> Added');
                }

                $list.append(`
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <div class="pe-2 small text-dark fw-medium" style="max-width: 75%;">${item.name}</div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-success small fw-bold">₹${item.price.toFixed(2)}</span>
                            <button type="button" class="btn btn-link text-danger p-0 remove-cart-item" data-id="${item.id}" aria-label="Remove item">
                                <i class="fa fa-times-circle"></i>
                            </button>
                        </div>
                    </li>
                `);
            });

            $total.text('₹' + total.toFixed(2));
            $input.val(ids.join(','));
        }
    }
});
</script>
@endpush
