<style>

    .grid-selector .wrap {
        position: relative;
        line-height: 34px;
        border-bottom: 1px dashed #eee;
        padding: 0 30px;
        font-size: 13px;
        overflow:auto;
    }

    .grid-selector .wrap:last-child {
        border-bottom: none;
    }

    .grid-selector .select-label {
        float: left;
        width: 100px;
        padding-left: 10px;
        color: #999;
    }

    .grid-selector .select-options {
        margin-left: 100px;
    }

    .grid-selector ul {
        height: 25px;
        list-style: none;
    }

    .grid-selector ul > li {
        margin-right: 30px;
        float: left;
    }

    .grid-selector ul > li a {
        color: #666;
        text-decoration: none;
    }

    .grid-selector .select-options a.active {
        color: #dd4b39;
        font-weight: 600;
    }

    .grid-selector li .add {
        visibility: hidden;
    }

    .grid-selector li:hover .add {
        visibility: visible;
    }

    .grid-selector ul .clear {
        visibility: hidden;
    }

    .grid-selector ul:hover .clear {
        color: #3c8dbc;
        visibility: visible;
    }
</style>
<div class="grid-selector"> 
    <div class="wrap">
        <div class="select-label">一级栏目</div>
        <div class="select-options">
            <ul>
                <li>
                    <a href="{{ route('admin.articles.index', ['parent_id' => 0]) }}"
                       class="{{ $parent_id === 0 ? 'active' : ''}}">
                        全部
                    </a>
                </li>
                @foreach($cateParents as $id => $item)
                <li>
                    <a href="{{ route('admin.articles.index', ['parent_id' => $id]) }}"
                       class="{{ $parent_id === $id ? 'active' : ''}}">
                        {{ $item['name'] }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @if (!$cateChildren->isEmpty())
    <div class="wrap">
        <div class="select-label">二级栏目</div>
        <div class="select-options">
            <ul>
                <li>
                    <a href="{{ route('admin.articles.index', ['parent_id' => $parent_id, 'category_id' => 0]) }}"
                       class="{{ $category_id === 0 ? 'active' : ''}}">
                        全部
                    </a>
                </li>
                @foreach($cateChildren as $id => $item)
                <li>
                    <a href="{{ route('admin.articles.index', ['parent_id' => $parent_id, 'category_id' => $id]) }}"
                        class="{{ $category_id === $id ? 'active' : ''}}">
                        {{ $item['name'] }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif
    @if (!$cateGradson->isEmpty())
    <div class="wrap">
        <div class="select-label">三级栏目</div>
        <div class="select-options">
            <ul>
                <li>
                    <a href="{{ route('admin.articles.index', ['parent_id' => $parent_id, 'category_id' => $category_id, 'sub_category_id' => 0]) }}"
                       class="{{ $sub_category_id === 0 ? 'active' : ''}}">
                        全部
                    </a>
                </li>
                @foreach($cateGradson as $id => $item)
                <li>
                    <a href="{{ route('admin.articles.index', ['parent_id' => $parent_id, 'category_id' => $category_id, 'sub_category_id' => $id]) }}"
                        class="{{ $sub_category_id === $id ? 'active' : ''}}">
                        {{ $item['name'] }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

</div>


