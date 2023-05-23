@if ($paginator->hasPages())
<nav>
    <ul class="pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
            <span class="page-link" aria-hidden="true">&lsaquo;</span>
        </li>
        @else
        <li class="page-item">
            <button class="page-link" type="submit" name="page" rel="prev" aria-label="@lang('pagination.previous')" value="{{ Helper::getPageFromURL($paginator->previousPageUrl()) }}">&lsaquo;</button>
       </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
        <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
        @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
        <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
        @else
        <li class="page-item"><button class="btn-page" type="submit" name="page" value="{{ $page }}"><span class="page-link" href="{{ $url }}">{{ $page }}</span></button></li>
        @endif
        @endforeach
        @endif
        @endforeach
        <style>
            .btn-page {
                margin: 0;
                padding: 0;
                border: none;
            }
            .btn-page:focus {
                outline: none;
            }
        </style>
        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
        <li class="page-item">
            <button class="page-link" type="submit" name="page" rel="next" aria-label="@lang('pagination.next')" value="{{ Helper::getPageFromURL($paginator->nextPageUrl()) }}">&rsaquo;</button>
        </li>
        @else
        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
            <span class="page-link" aria-hidden="true">&rsaquo;</span>
        </li>
        @endif
    </ul>
</nav>
@endif