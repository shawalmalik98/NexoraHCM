{{-- Footer for server paginated tables: record range, rows per page and page links. Expects $paginator (LengthAwarePaginator). --}}
@if(!empty($paginator))
    <div class="d-flex flex-stack flex-wrap gap-3 pt-5">
        <div class="d-flex align-items-center gap-3 fs-6 fw-semibold text-gray-700">
            <span>
                @if($paginator->total() > 0)
                    Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} records
                @else
                    No records found
                @endif
            </span>
            <select class="form-select form-select-sm w-auto" onchange="(function(v){var p=new URLSearchParams(window.location.search);p.set('per_page',v);p.delete('page');window.location.search=p.toString();})(this.value)">
                @foreach([25, 50, 100] as $size)
                    <option value="{{ $size }}" @if($paginator->perPage() == $size) selected @endif>{{ $size }} / page</option>
                @endforeach
            </select>
        </div>
        @if($paginator->hasPages())
            <ul class="pagination mb-0">
                <li class="page-item previous @if($paginator->onFirstPage()) disabled @endif">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() ?? '#' }}"><i class="previous"></i></a>
                </li>
                @php
                    $current_page = $paginator->currentPage();
                    $last_page = $paginator->lastPage();
                    $window_start = max(1, $current_page - 2);
                    $window_end = min($last_page, $current_page + 2);
                @endphp
                @if($window_start > 1)
                    <li class="page-item"><a class="page-link" href="{{ $paginator->url(1) }}">1</a></li>
                    @if($window_start > 2)<li class="page-item disabled"><span class="page-link">&hellip;</span></li>@endif
                @endif
                @for($page_number = $window_start; $page_number <= $window_end; $page_number++)
                    <li class="page-item @if($page_number == $current_page) active @endif"><a class="page-link" href="{{ $paginator->url($page_number) }}">{{ $page_number }}</a></li>
                @endfor
                @if($window_end < $last_page)
                    @if($window_end < $last_page - 1)<li class="page-item disabled"><span class="page-link">&hellip;</span></li>@endif
                    <li class="page-item"><a class="page-link" href="{{ $paginator->url($last_page) }}">{{ $last_page }}</a></li>
                @endif
                <li class="page-item next @if(!$paginator->hasMorePages()) disabled @endif">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() ?? '#' }}"><i class="next"></i></a>
                </li>
            </ul>
        @endif
    </div>
@endif
