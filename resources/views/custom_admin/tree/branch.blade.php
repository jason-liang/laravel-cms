<!-- 
add style into bootstrap use admin::style
 -->
<li class="dd-item {{ (!(isset($branch['children'])) || count($branch['children']) <= 0) ? '' : '' }}" data-id="{{ $branch[$keyName] }}">
    <div class="dd-handle">
        {!! $branchCallback($branch) !!}
        <span class="pull-right dd-nodrag">
            {!! $resolveActions($branch) !!}
        </span>
    </div>
    @if(isset($branch['children']))
    <ol class="dd-list">
        @foreach($branch['children'] as $branch)
            @include($branchView, $branch)
        @endforeach
    </ol>
    @endif
</li>
