{{-- resources/views/components/custom-pagination.blade.php --}}
@if ($paginator->hasPages())
    <nav aria-label="Pagination Navigation" class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <p class="small text-muted mb-0">
                Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
            </p>
        </div>

        <div class="d-flex align-items-center">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="btn btn-outline-secondary btn-sm disabled me-2">
                    <i class="mdi mdi-chevron-left"></i> Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-outline-primary btn-sm me-2">
                    <i class="mdi mdi-chevron-left"></i> Previous
                </a>
            @endif

            {{-- Pagination Elements --}}
            <div class="btn-group me-2" role="group">
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="btn btn-outline-secondary btn-sm disabled">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="btn btn-primary btn-sm">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="btn btn-outline-secondary btn-sm">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-outline-primary btn-sm">
                    Next <i class="mdi mdi-chevron-right"></i>
                </a>
            @else
                <span class="btn btn-outline-secondary btn-sm disabled">
                    Next <i class="mdi mdi-chevron-right"></i>
                </span>
            @endif
        </div>
    </nav>
@endif
