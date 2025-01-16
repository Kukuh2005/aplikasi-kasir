@extends('layout.app')

@section('title', ' - Dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Dashboard</h1>
    </div>

    <div class="alert alert-success">
        <p>Hallo <span class="font-weight-bold">{{auth()->user()->nama}}</span>, Kamu Login Sebagai <span class="font-weight-bold">{{auth()->user()->level}}</span>.</p>
    </div>

    @if(auth()->user()->level=='admin')
    <div class="section-body">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Barang</h4>
                        </div>
                        <div class="card-body">
                            {{$barang->count()}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1" id="stokCard">
                    <div class="card-icon bg-info">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Stok Kosong</h4>
                        </div>
                        <div class="card-body">
                            {{$stok_kosong->count()}}
                        </div>
                        <a href="stok-kosong" data-toggle="modal" data-target="#stok-kosong">Detail</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Transaksi Hari ini</h4>
                        </div>
                        <div class="card-body">
                            {{$transaksi_hari_ini->count()}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Transaksi Bulan {{$bulan_ini}}</h4>
                        </div>
                        <div class="card-body">
                            {{$transaksi_bulan_ini->count()}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4> Omset {{$tahun}}</h4>
                        </div>
                        <div class="card-body">
                            Rp{{number_format($omset, 0, ',', '.')}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                  <div class="card-header">
                    <h4>Transaksi Tahun {{$tahun}}</h4>
                  </div>
                  <div class="card-body">
                    <canvas id="transaksi-tahun"></canvas>
                  </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                  <div class="card-header">
                    <h4>Barang Terlaris</h4>
                  </div>
                  <div class="card-body">
                    <canvas id="barang-laris"></canvas>
                  </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</section>
@include('dashboard.kosong')
@endsection

@push('script')
<script>
    $(document).ready(function () {
        $('#table').DataTable();
    });

    $(document).ready(function () {
        $('#data-table').DataTable();
    });

    var colors = ['#FCFF52', '#ffff']; // Array of colors
    var lastColorIndex = -1; // Initialize last color index

    function changeBackgroundColor() {
        var card = document.getElementById('stokCard');
        var randomColorIndex = Math.floor(Math.random() * colors.length); // Pick a random color index

        // Ensure the next random color is different from the previous one
        while (randomColorIndex === lastColorIndex) {
            randomColorIndex = Math.floor(Math.random() * colors.length);
        }

        var randomColor = colors[randomColorIndex]; // Get the random color
        card.style.backgroundColor = randomColor; // Set background color
        lastColorIndex = randomColorIndex; // Update last color index
    }

    var stockCount = {{$stok_kosong->count()}};
    if (stockCount > 0) {
        setInterval(changeBackgroundColor, 300); // Call the function every second
    }

    var ctx = document.getElementById("transaksi-tahun").getContext('2d');
    var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($bulan); ?>,
        datasets: [{
        label: 'Statistics',
        data: <?php echo json_encode($total); ?>,
        borderWidth: 2,
        backgroundColor: '#7fff00',
        borderColor: '#7fff00',
        borderWidth: 2.5,
        pointBackgroundColor: '#7fff00',
        pointRadius: 4
        }]
    },
    options: {
        legend: {
        display: false
        },
        scales: {
        yAxes: [{
            gridLines: {
            drawBorder: false,
            color: '#f2f2f2',
            },
            ticks: {
            beginAtZero: true,
                callback: function(value, index, values) {
                return value.toLocaleString('id-ID'); // Format Rupiah
                }
            }
        }],
        xAxes: [{
            ticks: {
            display: false
            },
            gridLines: {
            display: false
            }
        }]
        },
    }
    });

    var ctx = document.getElementById("barang-laris").getContext('2d');
    var myChart = new Chart(ctx, {
    type: 'pie',
    data: {
        datasets: [{
        data: <?php echo json_encode($data_barang) ?>,
        backgroundColor: [
            '#00ffff',  /* Cyan cerah */
            '#ee82ee',  /* Violet cerah */
            '#ff7eb3',  /* Pink cerah */
            '#ffd700',  /* Kuning cerah */
            '#7fff00',  /* Hijau limau */
            '#00ff7f',  /* Hijau musim semi cerah */
            '#1e90ff',  /* Biru dodger cerah */
            '#ff6347',  /* Merah tomat cerah */
            '#ff4500',  /* Oranye merah cerah */
            '#ff1493',  /* Merah muda dalam cerah */
            '#00fa9a',  /* Hijau laut cerah */
            '#adff2f',  /* Hijau kekuningan cerah */
            '#ff69b4',  /* Merah muda cerah */
            '#ffff54',  /* Kuning pastel cerah */
            '#40e0d0',  /* Turquoise cerah */
        ],
        label: 'Dataset 1'
        }],
        labels: <?php echo json_encode($label_barang) ?>,
    },
    options: {
        responsive: true,
        legend: {
        position: 'bottom',
        },
    }
    });
</script>
@endpush