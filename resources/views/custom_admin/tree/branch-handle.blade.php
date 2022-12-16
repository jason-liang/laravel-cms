<style>
    .dd3-content { 
        display: block;
        height: 30px;
        margin: 5px 0;
        padding: 5px 10px 5px 40px; 
        color: #333;
        text-decoration: none;
        font-weight: bold;
        border: 1px solid #ccc;
        background: #fafafa;
        -webkit-border-radius: 3px;
                border-radius: 3px;
        box-sizing: border-box; 
        -moz-box-sizing: border-box;
    }

    .dd3-content:hover {
        color: #2ea8e5; 
        background: #fff; 
    }

    .dd-dragel > .dd3-item > .dd3-content { 
        margin: 0;
    }

    .dd3-item > button { 
        margin-left: 30px; 
    }

    .dd3-handle { 
        position: absolute; 
        margin: 0; 
        left: 0; 
        top: 0; 
        cursor: pointer;
        width: 30px;
        height: 30px;
        text-indent: 100%;
        white-space: nowrap;
        overflow: hidden;
        border: 1px solid #ccc;
        background: #ddd;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    .dd3-handle:before { 
        content: '≡'; 
        display: block; 
        position: absolute; 
        left: 0; 
        top: 3px; 
        width: 100%; 
        text-align: center; 
        text-indent: 0; 
        color: #fff; 
        font-size: 20px; 
        font-weight: normal; 
    }
    .dd3-handle:hover { 
        background: #ddd; 
    }
</style>

<li class="dd-item dd3-item" data-id="{{ $branch[$keyName] }}">
    <div class="dd-handle dd3-handle"></div>
    <div class="dd3-content">
        {!! $branchCallback($branch) !!}
        <span class="pull-right dd-nodrag">
            <a href="{{ url("$path/$branch[$keyName]/edit") }}"><i class="fa fa-edit"></i></a>
            <a href="javascript:void(0);" data-id="{{ $branch[$keyName] }}" class="tree_branch_delete"><i class="fa fa-trash"></i></a>
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