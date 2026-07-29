<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">
                    <span>Main Menu</span>
                </li>
                <li class="submenu {{ set_active(['home', 'teacher/dashboard', 'student/dashboard']) }}">
                    <a>
                        <i class="fas fa-tachometer-alt"></i>
                        <span> Dashboard</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li><a href="{{ route('home') }}" class="{{ set_active(['home']) }}">Admin Dashboard</a></li>
                    </ul>
                </li>
                @if (Session::get('role_name') === 'PIC Verifikator' ||
                        Session::get('role_name') === 'Pengawas Verifikator' ||
                        Session::get('role_name') === 'Super Admin' ||
                        Session::get('role_name') === 'Admin')
                    <li class="submenu {{ set_active(['kasir*']) }} {{ request()->is('kasir/*') ? 'active' : '' }}">
                        <a href="#">
                            <i class="fas fa-shield-alt"></i>
                            <span>Apotik</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li><a href="{{ route('list-customers') }}"
                                    class="{{ set_active(['kasir*']) }} {{ request()->is('kasir/*') ? 'active' : '' }}">Kasir</a>
                            </li>
                        </ul>
                    </li>
                @endif
                {{-- @if (Session::get('role_name') === 'Keuangan Admin' || Session::get('role_name') === 'Super Admin' || Session::get('role_name') === 'Admin')
                    <li
                        class="submenu {{ set_active(['sp3-keuangan*']) }} {{ request()->is('sp3-keuangan/*') ? 'active' : '' }}">
                        <a href="#">
                            <i class="fas fa-money-bill"></i>
                            <span>Unit Keuangan</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li><a href="{{ route('sp3-keuangan/list') }}"
                                    class="{{ set_active(['sp3-keuangan/list']) }} {{ request()->is('sp3-keuangan/edit/*') ? 'active' : '' }}">List
                                    SP3</a></li>
                        </ul>
                    </li>
                @endif
                @if (Session::get('role_name') === 'PIC Unit' || Session::get('role_name') === 'Keuangan Admin' || Session::get('role_name') === 'Super Admin' || Session::get('role_name') === 'Admin')
                    <li
                        class="submenu {{ set_active(['sp3-verifikasi*']) }} {{ request()->is('sp3-verifikasi/*') ? 'active' : '' }}">
                        <a href="#">
                            <i class="fas fa-shield-alt"></i>
                            <span>RKAP</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li><a href="{{ route('billing-verifikasi/list') }}" class="{{set_active(['billing-verifikasi/list'])}} {{ (request()->is('billing/edit/*')) ? 'active' : '' }}">List Billing</a></li>
                            <li><a href="{{ route('unit/list') }}"
                                    class="{{ set_active(['rkap*']) }} {{ request()->is('rkap/*') ? 'active' : '' }}">List
                                    Unit</a></li>
                            <li><a href="{{ route('billing-verifikasi/list') }}" class="{{set_active(['billing-verifikasi/list'])}} {{ (request()->is('billing/edit/*')) ? 'active' : '' }}">List Billing</a></li>
                            <li><a href="{{ route('pendapatan/list') }}"
                                    class="{{ set_active(['rkap/ref-pendapatan*']) }} {{ request()->is('rkap/ref-pendapatan*') ? 'active' : '' }}">Referensi
                                    Pendapatan</a></li>
                            <li><a href="{{ route('biaya/list') }}"
                                    class="{{ set_active(['rkap/ref-biaya*']) }} {{ request()->is('rkap/ref-biaya*') ? 'active' : '' }}">Referensi
                                    Biaya</a></li>
                        </ul>
                    </li>
                @endif
                @if (Session::get('role_name') === 'Admin' || Session::get('role_name') === 'Super Admin')
                    <li
                        class="submenu {{ set_active(['list/users']) }} {{ request()->is('view/user/edit/*') ? 'active' : '' }}">
                        <a href="#">
                            <i class="fas fa-users"></i>
                            <span>User Management</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li><a href="{{ route('list/users') }}"
                                    class="{{ set_active(['list/users']) }} {{ request()->is('view/user/edit/*') ? 'active' : '' }}">List
                                    Users</a></li>
                        </ul>
                    </li>
                @endif --}}
                {{-- <li
                    class="submenu {{ set_active(['student/list', 'student/grid', 'student/add/page']) }} {{ request()->is('student/edit/*') ? 'active' : '' }} {{ request()->is('student/profile/*') ? 'active' : '' }}">
                    <a href="#"><i class="fas fa-graduation-cap"></i>
                        <span> Students</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li><a href="{{ route('student/list') }}"
                                class="{{ set_active(['student/list', 'student/grid']) }}">Student List</a></li>
                        <li><a href="{{ route('student/add/page') }}"
                                class="{{ set_active(['student/add/page']) }}">Student Add</a></li>
                        <li><a class="{{ request()->is('student/edit/*') ? 'active' : '' }}">Student Edit</a></li>
                        <li><a href="" class="{{ request()->is('student/profile/*') ? 'active' : '' }}">Student
                                View</a>
                        </li>
                    </ul>
                </li> --}}

                {{-- <li class="submenu  {{ set_active(['eselon/*']) }} {{ request()->is('eselon/*') ? 'active' : '' }}">
                    <a href="#"><i class="fas fa-chalkboard-teacher"></i>
                        <span> Data Master</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li><a href="{{ route('eselon/list') }}" class="{{ set_active(['eselon/*']) }}">Eselon</a>
                        </li>
                        <li><a href="{{ route('layanan/list') }}" class="{{ set_active(['layanan/*']) }}">Layanan</a>
                        </li>
                        <li><a href="{{ route('sub-layanan/list') }}" class="{{ set_active(['sub-layanan/*']) }}">Sub
                                Layanan</a></li>
                    </ul>
                </li> --}}
                {{--
                <li class="submenu {{set_active(['department/add/page','department/edit/page'])}} {{ request()->is('department/edit/*') ? 'active' : '' }}">
                    <a href="#"><i class="fas fa-building"></i>
                        <span> Departments</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li><a href="{{ route('department/list/page') }}" class="{{set_active(['department/list/page'])}} {{ request()->is('department/edit/*') ? 'active' : '' }}">Department List</a></li>
                        <li><a href="{{ route('department/add/page') }}" class="{{set_active(['department/add/page'])}}">Department Add</a></li>
                        <li><a>Department Edit</a></li>
                    </ul>
                </li>

                <li class="submenu {{set_active(['subject/list/page','subject/add/page'])}} {{ request()->is('subject/edit/*') ? 'active' : '' }}">
                    <a href="#"><i class="fas fa-book-reader"></i>
                        <span> Subjects</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li><a class="{{set_active(['subject/list/page'])}} {{ request()->is('subject/edit/*') ? 'active' : '' }}" href="{{ route('subject/list/page') }}">Subject List</a></li>
                        <li><a class="{{set_active(['subject/add/page'])}}" href="{{ route('subject/add/page') }}">Subject Add</a></li>
                        <li><a>Subject Edit</a></li>
                    </ul>
                </li>

                <li class="submenu {{set_active(['invoice/list/page','invoice/paid/page',
                    'invoice/overdue/page','invoice/draft/page','invoice/recurring/page',
                    'invoice/cancelled/page','invoice/grid/page','invoice/add/page',
                    'invoice/view/page','invoice/settings/page',
                    'invoice/settings/tax/page','invoice/settings/bank/page'])}}" {{ request()->is('invoice/edit/*') ? 'active' : '' }}>
                    <a href="#"><i class="fas fa-clipboard"></i>
                        <span> Invoices</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li><a class="{{set_active(['invoice/list/page','invoice/paid/page','invoice/overdue/page','invoice/draft/page','invoice/recurring/page','invoice/cancelled/page'])}}" href="{{ route('invoice/list/page') }}">Invoices List</a></li>
                        <li><a class="{{set_active(['invoice/grid/page'])}}" href="{{ route('invoice/grid/page') }}">Invoices Grid</a></li>
                        <li><a class="{{set_active(['invoice/add/page'])}}" href="{{ route('invoice/add/page') }}">Add Invoices</a></li>
                        <li><a class="{{ request()->is('invoice/edit/*') ? 'active' : '' }}" href="">Edit Invoices</a></li>
                        <li> <a class="{{ request()->is('invoice/view/*') ? 'active' : '' }}" href="">Invoices Details</a></li>
                        <li><a class="{{set_active(['invoice/settings/page','invoice/settings/tax/page','invoice/settings/bank/page'])}}" href="{{ route('invoice/settings/page') }}">Invoices Settings</a></li>
                    </ul>
                </li>

                <li class="menu-title">
                    <span>Management</span>
                </li>

                <li class="submenu {{set_active(['account/fees/collections/page','add/fees/collection/page'])}}">
                    <a href="#"><i class="fas fa-file-invoice-dollar"></i>
                        <span> Accounts</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li><a class="{{set_active(['account/fees/collections/page'])}}" href="{{ route('account/fees/collections/page') }}">Fees Collection</a></li>
                        <li><a href="expenses.html">Expenses</a></li>
                        <li><a href="salary.html">Salary</a></li>
                        <li><a class="{{set_active(['add/fees/collection/page'])}}" href="{{ route('add/fees/collection/page') }}">Add Fees</a></li>
                        <li><a href="add-expenses.html">Add Expenses</a></li>
                        <li><a href="add-salary.html">Add Salary</a></li>
                    </ul>
                </li>
                <li>
                    <a href="holiday.html"><i class="fas fa-holly-berry"></i> <span>Holiday</span></a>
                </li>
                <li>
                    <a href="fees.html"><i class="fas fa-comment-dollar"></i> <span>Fees</span></a>
                </li>
                <li>
                    <a href="exam.html"><i class="fas fa-clipboard-list"></i> <span>Exam list</span></a>
                </li>
                <li>
                    <a href="event.html"><i class="fas fa-calendar-day"></i> <span>Events</span></a>
                </li>
                <li>
                    <a href="library.html"><i class="fas fa-book"></i> <span>Library</span></a>
                </li> --}}
            </ul>
        </div>
    </div>
</div>

{{-- <div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">
                    <span>Main Menu</span>
                </li>
                @foreach ($menus as $menu)
                    <li class="submenu {{ set_active($menu->active_routes) }} 
                        {{ (isset($menu->pattern) && request()->is($menu->pattern)) ? 'active' : '' }}">
                        <a href="{{ $menu->route ? route($menu->route) : '#' }}">
                            <i class="{{ $menu->icon }}"></i>
                            <span>{{ $menu->title }}</span>
                            @if ($menu->children->count())
                                <span class="menu-arrow"></span>
                            @endif
                        </a>
                        @if ($menu->children->count())
                            <ul>
                                @foreach ($menu->children as $child)
                                    <li>
                                        <a href="{{ $child->route ? route($child->route) : '#' }}" 
                                           class="{{ set_active($child->active_routes) }} 
                                                  {{ (isset($child->pattern) && request()->is($child->pattern)) ? 'active' : '' }}">
                                            {{ $child->title }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div> --}}
