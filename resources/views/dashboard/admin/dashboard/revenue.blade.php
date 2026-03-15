@extends('layout.dashboard')
@section('chart.script')
    <script>
        <?php
            $xValues = [];
            $yValues = [];

            foreach($months as $month) {
                foreach ($month as $key => $items) {
                    $xValues[] = $key;
                    $yValues[] = $items['price'];
                }
            }
        ?>

        const xValues = {!! json_encode($xValues) !!};
        const yValues = {!! json_encode($yValues) !!};

        console.log(yValues);

        new Chart("myChart", {
            type: "bar",
            data: {
                labels: xValues,
                datasets: [{
                    data: yValues
                }]
            },
            options: {
                legend: {display: false},
                title: {display: false},
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: false,
                            labelString: 'Month'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        ticks: {
                            beginAtZero: true,
                            steps: 10,
                            stepValue: 5,
                            max: {!! $highest !!}
                        }
                    }]
                },
            }
        });
    </script>
@endsection
@section('dashboard')
    <div class="page-header neg-header mb-30">
        <div class="container"><h1>Inkomsten</h1></div>
    </div>

    <div class="secondnav">
        <a href="{{route('dashboard.admin.dashboard.accounts')}}" class="{{Route::is('dashboard.admin.dashboard.accounts*') ? 'active' : ''}} btn">Accounts</a>
        <a href="{{route('dashboard.admin.dashboard.dishes')}}" class="{{Route::is('dashboard.admin.dashboard.dishes*') ? 'active' : ''}} btn btn-outline-fat">Gerechten</a>
        <a href="{{route('dashboard.admin.dashboard.orders')}}" class="{{Route::is('dashboard.admin.dashboard.orders*') ? 'active' : ''}} btn">Bestellingen</a>
        <a href="{{route('dashboard.admin.dashboard.revenue')}}" class="{{Route::is('dashboard.admin.dashboard.revenue*') ? 'active' : ''}} btn btn-outline-fat">Inkomsten</a>
    </div>


    <div class="dashboard container">
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td width="50%">Totaal aantal bestellingen:</td>
                    <td width="50%">{{$orderCount}}</td>
                </tr>
                <tr>
                    <td>Inkomsten van bestellingen:</td>
                    <td>{{$revenue}}</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <canvas id="myChart" style="width:100%;max-width:700px"></canvas>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection


