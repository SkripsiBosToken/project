<x-layout.admin-v2>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div>
        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>Dashboard</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li class="active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content mt-3">

            <div class="col-lg-3 col-md-6">
                <div class="social-box">
                    <i class="fa fa-calendar-o"></i>
                    <ul>
                        <li>
                            <span>Daily</span>
                            <span>order</span>
                        </li>
                        <li>
                            <span class="count">{{ $daily }}</span>
                            <span>Order</span>
                        </li>
                    </ul>
                </div>
                <!--/social-box-->
            </div>
            <!--/.col-->


            <div class="col-lg-3 col-md-6">
                <div class="social-box">
                    <i class="fa fa-calendar-o"></i>
                    <ul>
                        <li>
                            <span>Weekly</span>
                            <span>order</span>
                        </li>
                        <li>
                            <span class="count">{{ $weekly }}</span>
                            <span>Order</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="social-box">
                    <i class="fa fa-calendar-o"></i>
                    <ul>
                        <li>
                            <span>Monthly</span>
                            <span>order</span>
                        </li>
                        <li>
                            <span class="count">{{ $monthly }}</span>
                            <span>Order</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="social-box">
                    <i class="fa fa-calendar-o"></i>
                    <ul>
                        <li>
                            <span>Yearly</span>
                            <span>order</span>
                        </li>
                        <li>
                            <span class="count">{{ $yearly }}</span>
                            <span>Order</span>
                        </li>
                    </ul>
                </div>
            </div>
            <!--/.col-->

            <div class="col-xl-3 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-widget-one">
                            <div class="stat-text">
                                <i class="fa fa-user"></i> Jumlah Pengguna
                            </div>
                            <div class="stat-digit">{{$user}}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-widget-one">
                            <div class="stat-text">
                                <i class="fa fa-product-hunt"></i> Jumlah Produk
                            </div>
                            <div class="stat-digit">{{$product}}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-widget-one">
                            <div class="stat-text">
                                <i class="fa fa-list"></i> Jumlah Orderan Berlangsung
                            </div>
                            <div class="stat-digit">{{$progressOrders}}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-widget-one">
                            <div class="stat-text">
                                <i class="fa fa-check-square"></i> Jumlah Berhasil
                            </div>
                            <div class="stat-digit">{{$successOrders}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.admin-v2>
