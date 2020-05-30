@extends('admin.layout')



@section('main')

<div class="row">

        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="ion ion-ios-cart-outline"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">{{ trans('admin.total_order') }}</span>
              <span class="info-box-number">{{ number_format($orders->count()) }}</span>
              <a href="{{ route('admin_order.index') }}" class="small-box-footer">
                  {{ trans('admin.more') }}&nbsp;
                  <i class="fa fa-arrow-circle-right"></i>
              </a>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-tags"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">{{ trans('admin.total_product') }}</span>
              <span class="info-box-number">{{ number_format($products->count()) }}</span>
              <a href="{{ route('admin_product.index') }}" class="small-box-footer">
                  {{ trans('admin.more') }}&nbsp;
                  <i class="fa fa-arrow-circle-right"></i>
              </a>

            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>


        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">{{ trans('admin.total_customer') }}</span>
              <span class="info-box-number">{{ number_format($users->count()) }}</span>
              <a href="{{ route('admin_customer.index') }}" class="small-box-footer">
                  {{ trans('admin.more') }}&nbsp;
                  <i class="fa fa-arrow-circle-right"></i>
              </a>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->



        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12 hide">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-map-signs"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">{{ trans('admin.total_blogs') }}</span>
              <span class="info-box-number">{{ number_format($blogs->count()) }}</span>
              <a href="{{ route('admin_news.index') }}" class="small-box-footer">
                  {{ trans('admin.more') }}&nbsp;
                  <i class="fa fa-arrow-circle-right"></i>
              </a>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->


      </div>


{{-- Chart --}}
<div class="row">

  <div class="col-md-12">

	<div class="box pad">
		<div class="d-flex text-right">
			<label for="from_date">Desde: &nbsp;</label>
			<input type="text" id="date_from" name="expires_at" value="{{ \Carbon\Carbon::now()->firstOfMonth()->format('d/m/Y') }}" 
				class="inline form-control date_time" style="width: 100px;" placeholder="" />
			&nbsp;&nbsp;
			<label for="from_date">Hasta: &nbsp;</label>
			<input type="text" id="date_to" name="expires_at" value="{{ \Carbon\Carbon::now()->endOfMonth()->format('d/m/Y') }}" 
				class="inline form-control date_time" style="width: 100px;" placeholder="" />
		</div>
	</div>
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">{{ trans('admin.order_month') }}</h3>

        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
          </button>
          <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
      </div>

      <div class="box-body table-responsive no-padding box-primary">
        <div class="box">			
          <div id="chart-days" style="width:100%; height:auto;"></div>
        </div>
      </div>
    </div>
  </div>


  <div class="col-md-12 hide">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">{{ trans('admin.order_year') }}</h3>

        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
          </button>
          <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
      </div>

      <div class="box-body table-responsive no-padding box-primary">
          <div class="box">
            {{-- <div class="row"> --}}
              <div class="col-md-4">
                <div id="chart-pie" style="width:100%; height:auto;"></div>
              </div>
              <div class="col-md-8">
                <div id="chart-month" style="width:100%; height:auto;"></div>
              </div>
            {{-- </div> --}}
          </div>
      </div>
    </div>
  </div>
  </div>
{{-- //End chart --}}


<div class="row">

{{-- Top order --}}
@php
  $topOrder = $orders->with('orderStatus')->orderBy('id','desc')->limit(10)->get();
@endphp

  <div class="col-md-6">

    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">{{ trans('admin.top_order_new') }}</h3>

        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
          </button>
          <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
      </div>

      <div class="box-body table-responsive no-padding box-primary">
            <div class="box-body">
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th>{{ trans('order.id') }}</th>
                    <th>{{ trans('order.email') }}</th>
                    <th>{{ trans('order.status') }}</th>
                    <th>{{ trans('order.created_at') }}</th>
                  </tr>
                  </thead>
                  <tbody>
@if (count($topOrder))
  @foreach ($topOrder as $order)
                    <tr>
                      <td><a href="{{ route('admin_order.detail',['id'=>$order->id]) }}">Order#{{ $order->id }}</a></td>
                      <td>{{ $order->email }}</td>
                      <td><span class="label label-{{ $mapStyleStatus[$order->status]??'' }}">{{ $order->orderStatus->name }}</span></td>
                      <td>{{ $order->created_at }}</td>
                    </tr>
  @endforeach
@endif
                  </tbody>
                </table>
              </div>
            </div>
      </div>
    </div>
  </div>
{{-- //End top order --}}

{{-- Top customer --}}
@php
  $topCustomer = $users->orderBy('id','desc')->limit(10)->get();
@endphp
  <div class="col-md-6">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">{{ trans('admin.top_customer_new') }}</h3>

        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
          </button>
          <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
      </div>

      <div class="box-body table-responsive no-padding box-primary">
            <div class="box-body">
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th>{{ trans('customer.id') }}</th>
                    <th>{{ trans('customer.email') }}</th>
                    <th>{{ trans('customer.name') }}</th>
                    <th>{{ trans('customer.created_at') }}</th>
                  </tr>
                  </thead>
                  <tbody>
@if (count($topCustomer))
  @foreach ($topCustomer as $customer)
                    <tr>
                      <td><a href="{{ route('admin_customer.edit',['id'=>$customer->id]) }}">ID#{{ $customer->id }}</a></td>
                      <td>{{ $customer->email }}</td>
                      <td>{{ $customer->name }}</td>
                      <td>{{ $customer->created_at }}</td>
                    </tr>
  @endforeach
@endif
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
      </div>
    </div>
  </div>
  {{-- //End customer --}}
  </div>

@endsection


@push('styles')
@endpush

@push('scripts')
  <script src="{{ asset('admin/plugin/chartjs/highcharts.js') }}"></script>
  <script src="{{ asset('admin/plugin/chartjs/highcharts-3d.js') }}"></script>
  <script type="text/javascript">
  document.addEventListener('DOMContentLoaded', function () {
	let dateFormat = "DD/MM/YYYY"
    
    //Date picker
    $('.date_time').datepicker({
		autoclose: true,
		format: "dd/mm/yyyy",
		isRTL: false,
		language: 'es'
    })

	$('.date_time').on("changeDate", function (ev) {
		let from = moment($("#date_from").val(), dateFormat)
		let to = moment($("#date_to").val(), dateFormat)
		if(to.isSameOrAfter(from)){
			updateMainChart(from, to);
		}    		
	})

	function updateMainChart(from, to) {
		$.ajax({
			url: "{{ route('admin.mainChartData') }}",
			data:{
				from: from.format('YYYY-MM-DD'),
				to: to.format('YYYY-MM-DD')
			},
			success: function (data) {							
				myChart.axes[0].setCategories(Object.keys(data.orderInMonth))
				myChart.series[0].setData(Object.values(data.orderInMonth));
				myChart.series[1].setData(Object.values(data.amountInMonth));
				myChart.series[2].setData(Object.values(data.costInMonth));
				myChart.series[3].setData(Object.values(data.profitsInMonth));
			}
		})
	}

      var myChart = Highcharts.chart('chart-days', {
          credits: {
              enabled: false
          },
          title: {
              text: '{{ trans('chart.profits') }}'
          },
          xAxis: {
              categories: [], //{!! json_encode(array_keys($orderInMonth)) !!},
              crosshair: false

          },

          yAxis: [{
              min: 0,
              title: {
                  text: '{{ trans('chart.order') }}'
              },
          }, {
              title: {
                  text: '{{ trans('chart.amount') }}'
              },
              opposite: true
          },
          ],

          legend: {
                align: 'left',
                verticalAlign: 'top',
                borderWidth: 0
            },

          tooltip: {
              headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
              pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                  '<td style="padding:0"><b>{point.y:.0f} </b></td></tr>',
              footerFormat: '</table>',
              shared: true,
              useHTML: true
          },
          plotOptions: {
            column: {
                      pointPadding: 0.2,
                      borderWidth: 0
                  },
          },

          series: [
          {
              type: 'column',
			  name: '{{ trans('chart.order') }}',
			  color: '#746ef5',
              data:  [],//{!! json_encode(array_values($orderInMonth)) !!},
              dataLabels: {
                  enabled: true,
                  format: '{point.y:.0f}'
              }
          },
          {
              type: 'spline',
              name: '{{ trans('chart.total_amount') }}',
              color: '#00c0ef',
              yAxis: 1,
              data: [],//{!! json_encode(array_values($amountInMonth)) !!},
              borderWidth: 0,
              dataLabels: {
                  enabled: true,
                  borderRadius: 3,
                  backgroundColor: 'rgba(252, 255, 197, 0.7)',
                  borderWidth: 0.5,
                  borderColor: '#AAA',
                  y: -6
              }
          },
          {
            yAxis: 1,
            type : 'spline',
            color: '#d05135',
            name : '{{ trans('chart.cost') }}',
            data: []//{!! json_encode(array_values($costInMonth)) !!}
          },
          {
            yAxis: 1,
            type : 'spline',
            color: '#32ca0c',
            name : '{{ trans('chart.profit') }}',
            data: []//{!! json_encode(array_values($profitsInMonth)) !!}
          }
        ]
	  });
	  
	  //initial load of main chart
	  $('.date_time').trigger('changeDate')
  });



// Set up the chart
var chart = new Highcharts.Chart({
    chart: {
        renderTo: 'chart-month',
        type: 'column',
        options3d: {
            enabled: true,
            alpha: 0,
            beta: 10,
            depth: 50,
            viewDistance: 25
        }
    },
    title: {
        text: '{{ trans('chart.static_month') }}'
    },
    subtitle: {
        text: '{{ trans('chart.static_month_help') }}'
    },
    legend: {
            enabled: false,
      },
    credits: {
              enabled: false
          },
    xAxis: {
        categories: {!! json_encode(array_keys($dataInYear)) !!},
        crosshair: false,
    },
    yAxis: [
            {
                min: 0,
                title: {
                    text: '{{ trans('chart.amount') }}'
                },
            }
          ],
    plotOptions: {
        column: {
            depth: 25
        },
        series: {
            dataLabels: {
                enabled: true,
                borderRadius: 3,
                backgroundColor: 'rgba(252, 255, 197, 0.7)',
                borderWidth: 0.5,
                borderColor: '#AAA',
                y: -6
            }
        }
    },
    series: [
      {
        name : '{{ trans('chart.amount') }}',
        data: {!! json_encode(array_values($dataInYear)) !!},
      },
      {
          type : 'spline',
          color: '#d05135',
          name : '{{ trans('chart.amount') }}',
          data: {!! json_encode(array_values($dataInYear)) !!}
      }
  ]
});

function showValues() {
    $('#alpha-value').html(chart.options.chart.options3d.alpha);
    $('#beta-value').html(chart.options.chart.options3d.beta);
    $('#depth-value').html(chart.options.chart.options3d.depth);
}

// Activate the sliders
$('#sliders input').on('input change', function () {
    chart.options.chart.options3d[this.id] = parseFloat(this.value);
    showValues();
    chart.redraw(false);
});

showValues();
</script>

{{-- <script>
  Highcharts.chart('chart-pie', {
    chart: {
        type: 'pie',
        options3d: {
            enabled: true,
            alpha: 45,
            beta: 0
        }
    },
    credits: {
              enabled: false
          },
    title: {
        text: '{{ trans('chart.static_country') }}'
    },
    accessibility: {
        point: {
            valueSuffix: '%'
        }
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            depth: 35,
            dataLabels: {
                enabled: true,
                format: '{point.name}:{point.y}'
            }
        }
    },
    series: [{
        type: 'pie',
        name: '{{ trans('chart.country') }}',
        data: {!! $dataPie !!},
    }]
});
</script> --}}

@endpush
