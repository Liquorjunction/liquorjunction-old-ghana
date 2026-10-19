@if ($paginator->hasPages())
    <ul class="d-flex">       
        @if ($paginator->onFirstPage())
            <li class="disabled prev arrow"><span>←</span></li>
        @else
            <li ><a class="previous-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">←</a></li>
        @endif
       
        @foreach ($elements as $element)           
            @if (is_string($element))
                <li class="disabled"><span>{{ $element }}</span></li>
            @endif
           
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="active my-active"><span>{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach
        
        @if ($paginator->hasMorePages())
            <li><a class="next-btn" href="{{ $paginator->nextPageUrl() }}" rel="next">→</a></li>
        @else
            <li class="disabled next arrow"><span>→</span></li>
        @endif
    </ul>
@endif 