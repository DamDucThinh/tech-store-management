@if ($paginator->hasPages())
    <nav class="pagination" aria-label="Phân trang">
        <span class="pagination-info">
            Hiển thị {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} trên tổng {{ $paginator->total() }}
        </span>

        <div class="pagination-links">
            @if ($paginator->onFirstPage())
                <span class="page disabled" aria-hidden="true">&lsaquo;</span>
            @else
                <a class="page" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Trang trước">&lsaquo;</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="page disabled">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="page active" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="page" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a class="page" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Trang sau">&rsaquo;</a>
            @else
                <span class="page disabled" aria-hidden="true">&rsaquo;</span>
            @endif
        </div>
    </nav>
@endif
