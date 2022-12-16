<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="renderer" content="webkit" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<title>页面标题</title>
		<meta name="keywords" content="">
		<meta name="description" content="">
		<!-- 全局css start -->

		<!-- 全局css end -->

		<!-- css 插槽 -->
		@yield('css')
	</head>
	<body>

		<div class="header">
			页头<br>
			@foreach (get_navbars() as $parent)
				{{ $parent['name'] }}<br>

				@if (isset($parent['children']))

					@foreach ($parent['children'] as $child)
						|-{{ $child['name'] }}<br>
					@endforeach
				@endif
			@endforeach

			{{ get_latest_duty() }}
		</div>

		<div class="body">
			@yield('body')
		</div>
		
		<div class="footer">
			页脚<br>
			@foreach (get_friend_links() as $parent)
				{{ $parent['name'] }}<br>

				@if (isset($parent['children']))

					@foreach ($parent['children'] as $child)
						|-{{ $child['name'] }}<br>
					@endforeach
				@endif
			@endforeach
		</div>

		<!-- 全局js start -->

		<!-- 全局js end -->

		<!-- js 插槽，不通用的页面可以单独写js -->
		@yield('js')
	</body>
</html>
