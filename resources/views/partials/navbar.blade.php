<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="{{ url('/') }}">Finance Project</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="{{ Request::is('/') ? 'active' : '' }}">
                    <a href="{{ url('/') }}">Chart of Accounts</a>
                </li>
                <li class="{{ Request::is('journals') ? 'active' : '' }}">
                    <a href="{{ url('/journals') }}">Journals</a>
                </li>
                <li class="{{ Request::is('invoices') ? 'active' : '' }}">
                    <a href="{{ url('/invoices') }}">Invoices</a>
                </li>
                <li class="{{ Request::is('payments') ? 'active' : '' }}">
                    <a href="{{ url('/payments') }}">Payments</a>
                </li>
                <li class="{{ Request::is('reports/trial-balance') ? 'active' : '' }}">
                    <a href="{{ url('/reports/trial-balance') }}">Trial Balance</a>
                </li>
            </ul>
        </div>
    </div>
</nav>