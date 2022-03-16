<div>Current page：{{ $data->currentPage() }} Total Pages：{{ $data->lastPage() }} Total numbers：{{ $data->total() }}</div>
<ul class="uk-pagination uk-margin-medium-top">
    @if($data->currentPage() > 1)
        <li><a href="javascript:gotoPage('1');"><i class="uk-icon-angle-double-left"></i></a></li> 
    @else
        <li class="uk-disabled"><span><i class="uk-icon-angle-double-left"></i></span></li> 
    @endif

    @if($data->currentPage() > 1)
        <li><a href="javascript:gotoPage('{{$data->currentPage()-1}}');"><i class="uk-icon-angle-left"></i></a></li> 
    @else
        <li class="uk-disabled"><span><i class="uk-icon-angle-left"></i></span></li> 
    @endif

    @if($data->currentPage() == $data->lastPage() &&  $data->currentPage()- 4 > 0) 
        <li><a href="javascript:gotoPage('{{$data->currentPage() - 2}}');">{{$data->currentPage() - 4}}</a></li>
    @endif

    @if($data->currentPage() >= $data->lastPage() - 1 &&  $data->currentPage()- 3 > 0) 
        <li><a href="javascript:gotoPage('{{$data->currentPage() - 2}}');">{{$data->currentPage() - 3}}</a></li>
    @endif

    @if($data->currentPage()- 2 > 0) 
        <li><a href="javascript:gotoPage('{{$data->currentPage() - 2}}');">{{$data->currentPage() - 2}}</a></li>
    @endif
    @if($data->currentPage()- 1 > 0) 
        <li><a href="javascript:gotoPage('{{$data->currentPage() - 1}}');">{{$data->currentPage() - 1}}</a></li>
    @endif
        <li class="uk-active"><span>{{$data->currentPage()}}</span></li>
    @if($data->currentPage() + 1 <= $data->lastPage()) 
        <li><a href="javascript:gotoPage('{{$data->currentPage() + 1}}');">{{$data->currentPage() + 1}}</a></li>
    @endif

    @if($data->currentPage() + 2 <= $data->lastPage()) 
        <li><a href="javascript:gotoPage('{{$data->currentPage() + 2}}');">{{$data->currentPage() + 2}}</a></li>
    @endif

    @if($data->currentPage() <= 2 && $data->currentPage() + 3 <= $data->lastPage()) 
        <li><a href="javascript:gotoPage('{{$data->currentPage() + 3}}');">{{$data->currentPage() + 3}}</a></li>
    @endif

    @if($data->currentPage() == 1 && $data->currentPage() + 4 <= $data->lastPage()) 
        <li><a href="javascript:gotoPage('{{$data->currentPage() + 4}}');">{{$data->currentPage() + 4}}</a></li>
    @endif

    @if($data->currentPage() < $data->lastPage())
        <li><a href="javascript:gotoPage('{{$data->currentPage()+1}}');"><i class="uk-icon-angle-right"></i></a></li>
    @else
        <li class="uk-disabled"><span><i class="uk-icon-angle-right"></i></span></li> 
    @endif
    @if($data->currentPage() < $data->lastPage())
        <li><a href="javascript:gotoPage('{{$data->lastPage()}}');"><i class="uk-icon-angle-double-right"></i></a></li>
    @else
        <li class="uk-disabled"><span><i class="uk-icon-angle-double-right"></i></span></li> 
    @endif
</ul>