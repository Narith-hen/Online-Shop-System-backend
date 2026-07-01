@if ($paginator->hasPages())
    <nav aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span aria-disabled="true" aria-label="Previous"><span aria-hidden="true">&lsaquo;</span></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous">&lsaquo;</a>
        @endif

        @php
            $current = $paginator->currentPage();
            $last = $paginator->lastPage();
            $start = max(1, $current - 1);
            $end = min($last, $current + 1);
        @endphp

        @if ($start > 1)
            <span aria-disabled="true"><span>...</span></span>
        @endif

        @for ($page = $start; $page <= $end; $page++)
            @if ($page == $current)
                <span aria-current="page">{{ $page }}</span>
            @else
                <a href="{{ $paginator->url($page) }}">{{ $page }}</a>
            @endif
        @endfor

        @if ($end < $last)
            <span aria-disabled="true"><span>...</span></span>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next">&rsaquo;</a>
        @else
            <span aria-disabled="true" aria-label="Next"><span aria-hidden="true">&rsaquo;</span></span>
        @endif
    </nav>
@endif
