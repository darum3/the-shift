<ul class="navbar-nav mr-auto">
    @can('MNG')
    <li class='nav-item'><a href="{{ route('manage.work_type') }}" class='nav-link'>職種設定</a></li>
    <li class='nav-item'><a href="{{ route('manage.group') }}" class='nav-link'>グループ設定</a></li>
    @elsecan('G-MNG')
    <li class='nav-item'><a href="{{ route('g-manage.user')}}" class='nav-link'>ユーザ設定</a></li>
    <li class='nav-item'><a href="{{ route('g-manage.shift.view') }}"class='nav-link'>シフト作成</a></li>
    <li class='nav-item'><a href="{{ route('g-manage.desired.list') }}" class='nav-link'>グループシフト確認</a></li>
    <li class='nav-item'><a href="{{ route('member.shift') }}" class='nav-link'>シフト表示</a></li>
    @elsecan('MEMBER')
    <li class='nav-item'><a href="{{ route('member.shift') }}" class='nav-link'>シフト表示</a></li>
    <li class='nav-item'><a href="{{ route('member.desired') }}" class="nav-link">シフト提出</a></li>
    @endcan
</ul>
