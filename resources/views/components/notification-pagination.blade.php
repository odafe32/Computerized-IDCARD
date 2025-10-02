{{-- resources/views/components/notification-pagination.blade.php --}}
@if ($notifications->hasPages())
    <div class="d-flex justify-content-between align-items-center py-3 px-4 bg-light border-top">
        {{-- Results Info --}}
        <div class="d-flex align-items-center">
            <div class="me-3">
                <small class="text-muted">
                    <i class="mdi mdi-information-outline me-1"></i>
                    Showing <strong>{{ $notifications->firstItem() }}</strong> to
                    <strong>{{ $notifications->lastItem() }}</strong> of
                    <strong>{{ $notifications->total() }}</strong> notifications
                </small>
            </div>

            {{-- Per Page Selector --}}
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    {{ $notifications->perPage() }} per page
                </button>
                <ul class="dropdown-menu">
                    @foreach([10, 20, 50, 100] as $perPage)
                        <li>
                            <a class="dropdown-item {{ request('per_page', 20) == $perPage ? 'active' : '' }}"
                               href="{{ request()->fullUrlWithQuery(['per_page' => $perPage, 'page' => 1]) }}">
                                {{ $perPage }} per page
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Pagination Controls --}}
        <div class="d-flex align-items-center">
            {{-- Jump to First --}}
            @if ($notifications->currentPage() > 3)
                <a href="{{ $notifications->url(1) }}"
                   class="btn btn-sm btn-outline-secondary me-1"
                   title="First Page">
                    <i class="mdi mdi-page-first"></i>
                </a>
            @endif

            {{-- Previous Page --}}
            @if ($notifications->onFirstPage())
                <span class="btn btn-sm btn-outline-secondary disabled me-1">
                    <i class="mdi mdi-chevron-left"></i>
                </span>
            @else
                <a href="{{ $notifications->previousPageUrl() }}"
                   class="btn btn-sm btn-outline-primary me-1"
                   title="Previous Page">
                    <i class="mdi mdi-chevron-left"></i>
                </a>
            @endif

            {{-- Page Numbers --}}
            <div class="btn-group me-1" role="group">
                @php
                    $start = max($notifications->currentPage() - 2, 1);
                    $end = min($start + 4, $notifications->lastPage());
                    $start = max($end - 4, 1);
                @endphp

                @if ($start > 1)
                    <a href="{{ $notifications->url(1) }}" class="btn btn-sm btn-outline-secondary">1</a>
                    @if ($start > 2)
                        <span class="btn btn-sm btn-outline-secondary disabled">...</span>
                    @endif
                @endif

                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $notifications->currentPage())
                        <span class="btn btn-sm btn-primary">{{ $i }}</span>
                    @else
                        <a href="{{ $notifications->url($i) }}" class="btn btn-sm btn-outline-secondary">{{ $i }}</a>
                    @endif
                @endfor

                @if ($end < $notifications->lastPage())
                    @if ($end < $notifications->lastPage() - 1)
                        <span class="btn btn-sm btn-outline-secondary disabled">...</span>
                    @endif
                    <a href="{{ $notifications->url($notifications->lastPage()) }}" class="btn btn-sm btn-outline-secondary">
                        {{ $notifications->lastPage() }}
                    </a>
                @endif
            </div>

            {{-- Next Page --}}
            @if ($notifications->hasMorePages())
                <a href="{{ $notifications->nextPageUrl() }}"
                   class="btn btn-sm btn-outline-primary me-1"
                   title="Next Page">
                    <i class="mdi mdi-chevron-right"></i>
                </a>
            @else
                <span class="btn btn-sm btn-outline-secondary disabled me-1">
                    <i class="mdi mdi-chevron-right"></i>
                </span>
            @endif

            {{-- Jump to Last --}}
            @if ($notifications->currentPage() < $notifications->lastPage() - 2)
                <a href="{{ $notifications->url($notifications->lastPage()) }}"
                   class="btn btn-sm btn-outline-secondary"
                   title="Last Page">
                    <i class="mdi mdi-page-last"></i>
                </a>
            @endif
        </div>
    </div>

    {{-- Quick Jump --}}
    <div class="d-flex justify-content-center mt-2">
        <div class="input-group" style="max-width: 200px;">
            <span class="input-group-text">
                <i class="mdi mdi-arrow-right-bold-circle-outline"></i>
            </span>
            <input type="number"
                   class="form-control form-control-sm"
                   placeholder="Go to page..."
                   min="1"
                   max="{{ $notifications->lastPage() }}"
                   id="jumpToPage">
            <button class="btn btn-sm btn-outline-primary" type="button" onclick="jumpToPage()">
                Go
            </button>
        </div>
    </div>
@endif

<script>
function jumpToPage() {
    const pageInput = document.getElementById('jumpToPage');
    const page = parseInt(pageInput.value);
    const maxPage = {{ $notifications->lastPage() }};

    if (page >= 1 && page <= maxPage) {
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('page', page);
        window.location.href = currentUrl.toString();
    } else {
        alert(`Please enter a page number between 1 and ${maxPage}`);
        pageInput.focus();
    }
}

// Allow Enter key to jump to page
document.getElementById('jumpToPage').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        jumpToPage();
    }
});
</script>
