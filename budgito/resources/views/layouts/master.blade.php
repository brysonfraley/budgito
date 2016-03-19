<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Budgito | {{ $pageTitle }}</title>

    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" type="text/css" href="{{ url('bower_components/bootstrap/dist/css/bootstrap.min.css') }}" />
    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="{{ url('dist/css/sb-admin-2.css') }}" rel="stylesheet">
    <!-- Custom Fonts -->
    <link rel="stylesheet" type="text/css" href="{{ url('bower_components/font-awesome/css/font-awesome.min.css') }}" />
    <!-- MetisMenu CSS -->
    <!--<link href="{{ url('bower_components/metisMenu/dist/metisMenu.min.css') }}" rel="stylesheet">
    <!-- Timeline CSS -->
    <!--<link href="{{ url('dist/css/timeline.css') }}" rel="stylesheet">
    <!-- datepicker CSS -->
    <link rel="stylesheet" type="text/css" href="{{ url('dist/css/bootstrap-datepicker-1.5.1.min.css') }}" />
    <!-- DateRangePicker css -->
    <link rel="stylesheet" type="text/css" href="{{ url("dist/css/daterangepicker-v2.1.13.css") }}" />
    <!-- Morris Charts CSS -->
    <!-- <link href="{{ url('bower_components/morrisjs/morris.css') }}"" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style type="text/css">

    </style>

    <!-- jQuery -->
    <script type="text/javascript" src="{{ url("bower_components/jquery/dist/jquery.min.js") }}"></script>
    <!-- DatePicker plugin -->
    <script type="text/javascript" src="{{ url("dist/js/bootstrap-datepicker-1.5.1.js") }}"></script>
    <!-- Moment (date/time) plugin -->
    <script type="text/javascript" src="{{ url("dist/js/moment-v2.10.3.min.js") }}"></script>
    <!-- DateRangePicker plugin -->
    <script type="text/javascript" src="{{ url("dist/js/daterangepicker-v2.1.13.js") }}"></script>

</head>
	
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ url('/') }}">Budgito</a>
            </div>
            <!-- /.navbar-header -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

                @if (!Auth::guest())
                
                    <!-- left-side navbar -->
                    <ul class="nav navbar-nav">
                        <li class="dropdown {{ App\Library\ViewHelper::setActive('accounts') }}">
                            <a href="{{ url('accounts') }}" class="dropdown-toggle clickable-dropdown" 
                            data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    Accounts <i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                @if (isset($accounts) && $accounts)
                                    @foreach ($accounts as $account)
                                        <li><a href="{{ url(urlencode($account['name'])) }}">{{ $account['name'] }}</a></li>
                                    @endforeach
                                    <li role="separator" class="divider"></li>
                                @endif
                                <li><a href="{{ url('accounts') }}">View Accounts</a></li>
                                <li><a href="{{ url('accounts/add') }}">Add Account</a></li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                    </ul>
                    <!-- / left-side navbar -->
                    <!-- right-side navbar -->
                    <ul class="nav navbar-top-links navbar-right">
                        <!--<li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-envelope fa-fw"></i>  <i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-messages">
                                <li>
                                    <a href="#">
                                        <div>
                                            <strong>John Smith</strong>
                                            <span class="pull-right text-muted">
                                                <em>Yesterday</em>
                                            </span>
                                        </div>
                                        <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#">
                                        <div>
                                            <strong>John Smith</strong>
                                            <span class="pull-right text-muted">
                                                <em>Yesterday</em>
                                            </span>
                                        </div>
                                        <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#">
                                        <div>
                                            <strong>John Smith</strong>
                                            <span class="pull-right text-muted">
                                                <em>Yesterday</em>
                                            </span>
                                        </div>
                                        <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a class="text-center" href="#">
                                        <strong>Read All Messages</strong>
                                        <i class="fa fa-angle-right"></i>
                                    </a>
                                </li>
                            </ul>
                            <!-- /.dropdown-messages -->
                        <!--</li>-->
                        <!-- /.dropdown -->
                        <!--<li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-tasks fa-fw"></i>  <i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-tasks">
                                <li>
                                    <a href="#">
                                        <div>
                                            <p>
                                                <strong>Task 1</strong>
                                                <span class="pull-right text-muted">40% Complete</span>
                                            </p>
                                            <div class="progress progress-striped active">
                                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
                                                    <span class="sr-only">40% Complete (success)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#">
                                        <div>
                                            <p>
                                                <strong>Task 2</strong>
                                                <span class="pull-right text-muted">20% Complete</span>
                                            </p>
                                            <div class="progress progress-striped active">
                                                <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%">
                                                    <span class="sr-only">20% Complete</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#">
                                        <div>
                                            <p>
                                                <strong>Task 3</strong>
                                                <span class="pull-right text-muted">60% Complete</span>
                                            </p>
                                            <div class="progress progress-striped active">
                                                <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%">
                                                    <span class="sr-only">60% Complete (warning)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#">
                                        <div>
                                            <p>
                                                <strong>Task 4</strong>
                                                <span class="pull-right text-muted">80% Complete</span>
                                            </p>
                                            <div class="progress progress-striped active">
                                                <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                                    <span class="sr-only">80% Complete (danger)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a class="text-center" href="#">
                                        <strong>See All Tasks</strong>
                                        <i class="fa fa-angle-right"></i>
                                    </a>
                                </li>
                            </ul>
                            <!-- /.dropdown-tasks -->
                        <!--</li>
                        <!-- /.dropdown -->
                        <!--<li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-bell fa-fw"></i>  <i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-alerts">
                                <li>
                                    <a href="#">
                                        <div>
                                            <i class="fa fa-comment fa-fw"></i> New Comment
                                            <span class="pull-right text-muted small">4 minutes ago</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#">
                                        <div>
                                            <i class="fa fa-twitter fa-fw"></i> 3 New Followers
                                            <span class="pull-right text-muted small">12 minutes ago</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#">
                                        <div>
                                            <i class="fa fa-envelope fa-fw"></i> Message Sent
                                            <span class="pull-right text-muted small">4 minutes ago</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#">
                                        <div>
                                            <i class="fa fa-tasks fa-fw"></i> New Task
                                            <span class="pull-right text-muted small">4 minutes ago</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#">
                                        <div>
                                            <i class="fa fa-upload fa-fw"></i> Server Rebooted
                                            <span class="pull-right text-muted small">4 minutes ago</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a class="text-center" href="#">
                                        <strong>See All Alerts</strong>
                                        <i class="fa fa-angle-right"></i>
                                    </a>
                                </li>
                            </ul>
                            <!-- /.dropdown-alerts -->
                        <!--</li>
                        <!-- /.dropdown -->
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-user">
                                <li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a>
                                </li>
                                <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
                                </li>
                                <li class="divider"></li>
                                <li><a href="{{ url('logout') }}"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                                </li>
                            </ul>
                            <!-- /.dropdown-user -->
                        </li>
                        <!-- /.dropdown -->
                    </ul>
                    <!-- /.navbar-top-links -->

                @else   <!-- else user is a guest; show guest options -->

                    <ul class="nav navbar-nav navbar-right">
                        <li class="{{ App\Library\ViewHelper::setActive('login') }}">
                            <a href="{{ url('login') }}">Log In</a>
                        </li>
                        <li class="{{ App\Library\ViewHelper::setActive('signup') }}">
                            <a href="{{ url('signup') }}">Sign Up</a>
                        </li>
                    </ul>

                @endif

            </div>
        </div>
    </nav>  
    <!-- /.navigation -->

    <!-- main page -->
    <div id="page-wrapper">
        <div class="container">
            @if (Session::has('flash_message'))
                <div class="alert alert-success {{ Session::has('flash_message_important') ? 'alert-important' : ''}}">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close" aria-hidden="true">&times;</button>
                    {{ session('flash_message') }}
                </div>
            @endif
            
            <!--  Some pages (login, signup) don't need a header;
              --  check variable here to turn it off -->
            @if (!isset($showHeader) || $showHeader)
                <!-- header/title -->
                <div class="row">
                    <div class="col-lg-12">
                        @if (isset($accountName))
                            <h1 class="page-header">{{ $accountName }}</h1>
                        @else
                            <h1 class="page-header">{{ $pageTitle }}</h1>
                        @endif
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            @endif

            @if (isset($accountName))
                <div class="row">
                    <div class="col-xs-12">
                        <ul class="nav nav-tabs">
                            <li class="{{ App\Library\ViewHelper::setActive($accountNameEncoded.'/dashboard') }}">
                                <a href="{{ url($accountNameEncoded.'/dashboard') }}">Dashboard</a>
                            </li>

                            <!--<li class="dropdown {{ App\Library\ViewHelper::setActive('transactions') }}">
                                <a href="{{ url('transactions') }}" class="dropdown-toggle clickable-dropdown" 
                                data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" data-target="#">
                                        Transactions <i class="fa fa-caret-down"></i>
                                </a>-->

                            <li class="dropdown {{ App\Library\ViewHelper::setActive($accountNameEncoded.'/transactions') }}">
                                <a class="dropdown-toggle clickable-dropdown cursor-pointer" data-toggle="dropdown" href="{{ url($accountNameEncoded.'/transactions') }}" 
                                   role="button" aria-haspopup="true" aria-expanded="false" data-target="#">
                                  Transactions <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ url($accountNameEncoded.'/transactions') }}">View Transactions</a>
                                    </li>
                                    <li>
                                        <a href="{{ url($accountNameEncoded.'/transactions/add') }}">Add Transaction</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="{{ App\Library\ViewHelper::setActive($accountNameEncoded.'/budgets') }}">
                                <a href="{{ url($accountNameEncoded.'/budgets') }}">Budgets</a>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif

            @if (!isset($showHeader) || $showHeader)
                <div id="content-wrapper" class="content-borders {{ isset($accountName) ? '' : 'content-border-top' }}">
            @else
                <div id="content-wrapper">
            @endif
            
                @yield("content")
            </div>
            
        </div>
    </div>
    <!-- /#page-wrapper -->

    <!-- footer wrapper -->
    <div id="footer-wrapper">
        <div class="content-container container">
            Copywrite &copy; Budgito {{ date("Y") }}
        </div>
        <!-- /.content-container -->
    </div>
    <!-- /#footer-wrapper -->

    <!-- Bootstrap Core JavaScript -->
    <script src="{{ url("bower_components/bootstrap/dist/js/bootstrap.min.js") }}"></script>
    <!-- Custom Theme JavaScript -->
    <script src="{{ url("dist/js/sb-admin-2.js") }}"></script>
    
    <!-- Metis Menu Plugin JavaScript -->
    <script src="{{ url("bower_components/metisMenu/dist/metisMenu.min.js") }}"></script>
    <!-- Morris Charts JavaScript -->
    <!-- <script src="{{ url("bower_components/raphael/raphael-min.js") }}"></script>
    <script src="{{ url("bower_components/morrisjs/morris.min.js") }}"></script>
    <script src="{{ url("js/morris-data.js") }}"></script> -->

    <script type="text/javascript">
        $('.clickable-dropdown').on('click', function(){
        window.location.href=$(this).attr('href');
        });
        
        $('div.alert').not('.alert-important').delay(5000).slideUp(300);
        
    </script>

</body>
	
</html>