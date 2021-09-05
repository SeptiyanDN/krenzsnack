<div class="row border-bottom">
    <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
            <form role="search" class="navbar-form-custom" action="search_results.html">
                {{-- <div class="form-group">
                    <input type="text" placeholder="Search for something..." class="form-control"
                        name="top-search" id="top-search">
                </div> --}}
            </form>
        </div>
        <ul class="nav navbar-top-links navbar-right">
            <li>
                {{-- <span class="m-r-sm text-muted welcome-message">Welcome to INSPINIA+ Admin Theme.</span> --}}
            </li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <li>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault();
                            this.closest('form').submit();">
                        <i class="fa fa-sign-out"></i> Log out
                    </a>
                </li>
            </form>
        </ul>

    </nav>
</div>
<div class="row  border-bottom white-bg dashboard-header">
    <h1>@yield('title')</h1>
</div>