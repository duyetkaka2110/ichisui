<header class="{{ Helper::activeMenu('r.*',true) }}">
    <!-- header2: mau do, null: xanh -->
    <div class="mg-title">
    <a href="/" title="工事管理/資材管理システム"><h4 class="p-3 font-weight-bold text-white d-inline-block ">工事管理/資材管理システム</h4></a>
        <div class="mg-login">
            <label>{{ @Auth::user()->UserNM }}</label>
            <a href="/logout" alt="ログアウト" title="ログアウト">ログアウト</a>
        </div>
    </div>
    <ul class="nav nav-tabs  nav-justified">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle {{ Helper::activeMenu('matterinput') }} "  data-toggle="dropdown" href="" role="button" aria-haspopup="true" aria-expanded="false">新規入力
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" id="matter" href="/matterinput">工事受付</a>
                <a class="dropdown-item" href="/kintaiedit">勤怠入力</a>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Helper::activeMenu('b.work') }}" href="/work">工事検索</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Helper::activeMenu('b.schedule.*') }}" href="/scheduleday">スケジュール</a>
        </li>

        <li class="nav-item">
            <a class="nav-link nav-link2 {{ Helper::activeMenu('r.order') }}" href="/order">資材発注</a>
        </li>
        <li class="nav-item">
            <a class="nav-link nav-link2  {{ Helper::activeMenu('r.orderlist') }}" href="/orderlist">発注一覧</a>
        </li>
        <li class="nav-item ">
            <a class="nav-link nav-link2  {{ Helper::activeMenu('r.stock') }}" href="/stock">棚卸</a>
        </li>
    </ul>
</header>