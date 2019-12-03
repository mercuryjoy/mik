<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <ul class="sidebar-menu">
            <li class="{{{ (Request::is('admin') ? 'active' : '') }}}">
                <a href="{{ route('admin.dashboard.index') }}"><i class='fa fa-dashboard'></i> <span>首页</span></a>
            </li>
        </ul>

        <ul class="sidebar-menu">
            <li class="header">销售链管理</li>
            <li class="{{{ (Request::is('admin/distributors*') ? 'active' : '') }}}">
                <a href="{{ route('admin.distributors.index') }}"><i class='fa fa-sitemap'></i> <span>经销商管理</span></a>
            </li>
            <li class="{{{ (Request::is('admin/shops*') ? 'active' : '') }}}">
                <a href="{{ route('admin.shops.index') }}"><i class='fa fa-map-marker'></i> <span>终端管理</span></a>
            </li>
            <li class="{{{ (Request::is('admin/users*') ? 'active' : '') }}}">
                <a href="{{ route('admin.users.index') }}"><i class='fa fa-female'></i> <span>服务员管理</span></a>
            </li>
            <li class="{{{ (Request::is('admin/salesmen*') ? 'active' : '') }}}">
                <a href="{{ route('admin.salesmen.index') }}"><i class='fa fa-users'></i> <span>营销员管理</span></a>
            </li>
        </ul><!-- /.sidebar-menu -->

        <ul class="sidebar-menu">
            <li class="header">扫码管理</li>
            <li class="{{{ (Request::is('admin/scans*') ? 'active' : '') }}}">
                <a href="{{ route('admin.scans.index') }}"><i class='fa fa-barcode'></i> <span>扫码记录</span></a>
            </li>
            <li class="{{{ (Request::is('admin/extra*') ? 'active' : '') }}}">
                <a href="{{ route('admin.extra.index') }}"><i class='fa fa-cny'></i> <span>扫码额外收入记录</span></a>
            </li>
            <li class="{{{ (Request::is('admin/net*') ? 'active' : '') }}}">
                <a href="{{ route('admin.net.index') }}"><i class='fa fa-cny'></i> <span>用户扫码记录</span></a>
            </li>
            <li class="{{{ (Request::is('admin/coupons*') ? 'active' : '') }}}">
                <a href="{{ route('admin.coupons.index') }}"><i class='fa fa-gg-circle'></i> <span>优惠券核销记录</span></a>
            </li>
            @if(in_array(Auth::user()->level, [2, 99]))
            {{--<li class="{{{ (Request::is('admin/drawrules*') ? 'active' : '') }}}">
                <a href="{{ route('admin.drawrules.index') }}"><i class='fa fa-money'></i> <span>中奖设置</span></a>
            </li>--}}
            <li class="{{{ (Request::is('admin/activities*') ? 'active' : '') }}}">
                <a href="{{ route('admin.activities.index') }}"><i class='fa fa-money'></i> <span>活动设置</span></a>
            </li>
            <li class="{{{ (Request::is('admin/codebatches*') ? 'active' : '') }}}">
                <a href="{{ route('admin.codebatches.index') }}"><i class='fa fa-book'></i> <span>二维码批次管理</span></a>
            </li>
            <li class="{{{ (Request::is('admin/codes*') ? 'active' : '') }}}">
                <a href="{{ route('admin.codes.index') }}"><i class='fa fa-qrcode'></i> <span>二维码管理</span></a>
            </li>
            @endif
        </ul><!-- /.sidebar-menu -->

        @if(Auth::user()->level !== 3)
        <ul class="sidebar-menu">
            <li class="header">商城</li>
            <li class="{{{ (Request::is('admin/store/item*') ? 'active' : '') }}}">
                <a href="{{ route('admin.store.items.index') }}"><i class='fa fa-shopping-bag'></i> <span>商品管理</span></a>
            </li>
            <li class="{{{ (Request::is('admin/store/order*') ? 'active' : '') }}}">
                <a href="{{ route('admin.store.orders.index') }}"><i class='fa fa-truck'></i> <span>订单管理</span></a>
            </li>
        </ul><!-- /.sidebar-menu -->
        @endif

        @if(Auth::user()->level !== 3)
        <ul class="sidebar-menu">
            <li class="header">采购管理</li>
            <li class="{{{ (Request::is('admin/goods/item*') ? 'active' : '') }}}">
                <a href="{{ route('admin.goods.items.index') }}"><i class='fa fa-buysellads'></i> <span>采购商品管理</span></a>
            </li>
            <li class="{{{ (Request::is('admin/goods/orders*') ? 'active' : '') }}}">
                <a href="{{ route('admin.goods.orders.index') }}"><i class='fa fa-calendar-check-o'></i> <span>采购订单管理</span></a>
            </li>
            <li class="{{{ (Request::is('admin/goods/canceled*') ? 'active' : '') }}}">
                <a href="{{ route('admin.goods.canceled.index') }}"><i class='fa fa-calendar-check-o'></i> <span>取消订单管理</span></a>
            </li>

            @if(in_array(Auth::user()->level, [2,99,5]))
            <li class="{{{ (Request::is('admin/goods/drawback*') ? 'active' : '') }}}">
                <a href="{{ route('admin.goods.drawback.index') }}"><i class='fa fa-calendar-check-o'></i> <span>退款单审核</span></a>
            </li>
            @endif
        </ul><!-- /.sidebar-menu -->
        @endif

        @if(in_array(Auth::user()->level, [2, 99,3,4]))
        <ul class="sidebar-menu">
            <li class="header">支付管理</li>
            <li class="{{{ (Request::is('admin/pays*') ? 'active' : '') }}}">
                <a href="{{ route('admin.pays.index') }}"><i class='fa fa-buysellads'></i> <span>支付方式管理</span></a>
            </li>
        </ul><!-- /.sidebar-menu -->
        <ul class="sidebar-menu">
            <li class="header">消息管理</li>
            <li class="{{{ (Request::is('admin/feedbacks*') ? 'active' : '') }}}">
                <a href="{{ route('admin.feedbacks.index') }}"><i class='fa fa-support'></i> <span>消息管理</span></a>
            </li>
            <li class="{{{ (Request::is('admin/news*') ? 'active' : '') }}}">
                <a href="{{ route('admin.news.index') }}"><i class='fa fa-newspaper-o'></i> <span>通知管理</span></a>
            </li>
        </ul><!-- /.sidebar-menu -->
        @endif

        <ul class="sidebar-menu">
            <li class="header">设置</li>

            @if(in_array(Auth::user()->level, [2, 99]))
            <li class="{{{ (Request::is('admin/notifications*') ? 'active' : '') }}}">
                <a href="{{ route('admin.notifications.index') }}"><i class='fa fa-bell'></i> <span>智能提醒设置</span></a>
            </li>
            <li class="{{{ (Request::is('admin/settings*') ? 'active' : '') }}}"><a href="{{ route('admin.settings.index') }}"><i class='fa fa-cog'></i> <span>系统设置</span></a></li>
                <li class="{{{ (Request::is('admin/versions*') ? 'active' : '') }}}"><a href="{{ route('admin.versions.index') }}"><i class='fa fa-vimeo-square'></i> <span>APP版本管理</span></a></li>
            @endif

            @if(in_array(Auth::user()->level, [2, 4, 5, 99]))
            <li class="{{{ (Request::is('admin/categories*') ? 'active' : '') }}}"><a href="{{ route('admin.categories.index') }}"><i class='fa fa-bookmark'></i> <span>餐饮类型管理</span></a></li>
            @endif

            @if(in_array(Auth::user()->level, [2, 99]))
            <li class="{{{ (Request::is('admin/scan/warnings') ? 'active' : '') }}}"><a href="{{ route('admin.scan.warning') }}"><i class='fa fa-bookmark'></i> <span>核销风控管理</span></a></li>
            @endif

            @if(in_array(Auth::user()->level, [2, 5, 99]))
            <li class="{{{ (Request::is('admin/fundingpool/summary') ? 'active' : '') }}}"><a href="{{ route('admin.fundingpool.summary') }}"><i class='fa fa-bank'></i> <span>资金池管理</span></a></li>
            @endif

            @if(in_array(Auth::user()->level, [2, 99]))
            <li class="{{{ (Request::is('admin/admins*') ? 'active' : '') }}}"><a href="{{ route('admin.admins.index') }}"><i class='fa fa-user-secret'></i> <span>管理员管理</span></a></li>
            @endif

            <li class="{{{ (Request::is('admin/news*') ? 'active' : '') }}}">
                <a href="{{ route('admin.banners.index') }}"><i class='fa fa-newspaper-o'></i> <span>轮播图管理</span></a>
            </li>
            <li class="{{{ (Request::is('admin/admins/*/edit') ? 'active' : '') }}}"><a href="{{ route('admin.admins.edit', ["id" => Auth::User()->id ]) }}"><i class='fa fa-key'></i> <span>修改密码</span></a></li>
        </ul>

        @can('show-debug-features')
            <ul class="sidebar-menu">
                <li class="header">调试功能</li>
                <li class="{{{ (Request::is('admin/areas*') ? 'active' : '') }}}"><a href="{{ route('admin.areas.index') }}"><i class='fa fa-map'></i> <span>地区管理</span></a></li>
                <li class="{{{ (Request::is('admin/sms*') ? 'active' : '') }}}"><a href="{{ route('admin.sms.index') }}"><i class='fa fa-comments'></i> <span>短信日志</span></a></li>
                <li class="{{{ (Request::is('admin/fundingpool*') ? 'active' : '') }}}"><a href="{{ route('admin.fundingpool.index') }}"><i class='fa fa-bank'></i> <span>资金池记录</span></a></li>
            </ul>
        @endcan
    </section>
    <!-- /.sidebar -->
</aside>
