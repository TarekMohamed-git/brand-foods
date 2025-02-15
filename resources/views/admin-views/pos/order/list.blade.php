@extends('layouts.admin.app')

@section('title', translate('Order List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>

      /*@media (min-width: 576px) {*/
      /*  .modal-dialog { min-width: 90%;}*/
      /*}*/

    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/empty-cart.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{translate('pos')}} {{translate('orders')}} <span class="badge badge-pill badge-soft-secondary ml-2">{{ $orders->total() }}</span>
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="card">

            <div class="card-header shadow flex-wrap p-20px border-0">
                <h5 class="form-bold w-100 mb-3">{{ translate('Select Date Range') }}</h5>
                <form class="w-100">
                    <div class="row g-3 g-sm-4 g-md-3 g-lg-4">
                        <div class="col-sm-6 col-md-4 col-lg-2">
                            <select class="custom-select custom-select-sm text-capitalize min-h-45px" name="branch_id">
                                <option disabled>--- {{translate('select')}} {{translate('branch')}} ---</option>
                                <option value="all" {{ $branch_id == 'all' ? 'selected': ''}}>{{translate('all')}} {{translate('branch')}}</option>
                                @foreach($branches as $branch)
                                    <option value="{{$branch['id']}}" {{ $branch['id'] == $branch_id ? 'selected' : ''}}>{{$branch['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="input-date-group">
                                <label class="input-label" for="start_date">{{ translate('Start Date') }}</label>
                                <label class="input-date">
                                    <input type="text" id="start_date" name="start_date" value="{{$start_date}}" class="js-flatpickr form-control flatpickr-custom min-h-45px" placeholder="yy-mm-dd" data-hs-flatpickr-options='{ "dateFormat": "Y-m-d"}'>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="input-date-group">
                                <label class="input-label" for="end_date">{{ translate('End Date') }}</label>
                                <label class="input-date">
                                    <input type="text" id="end_date" name="end_date" value="{{$end_date}}" class="js-flatpickr form-control flatpickr-custom min-h-45px" placeholder="yy-mm-dd" data-hs-flatpickr-options='{ "dateFormat": "Y-m-d"}'>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-12 col-lg-4 __btn-row">
                            <a href="{{ route('admin.pos.orders') }}" id="" class="btn w-100 btn--reset min-h-45px">{{translate('clear')}}</a>
                            <button type="submit" id="show_filter_data" class="btn w-100 btn--primary min-h-45px">{{translate('show data')}}</button>
                        </div>
                    </div>
                </form>
            </div>


            <div class="card-body p-20px">
                <!-- Header -->
                <div class="order-top">
                    <div class="card--header">
                        <form action="{{url()->current()}}" method="GET">
                            <div class="input-group">
                                <input id="datatableSearch_" type="search" name="search"
                                        class="form-control"
                                        placeholder="{{translate('Search by ID, order or payment status')}}" aria-label="Search"
                                        value="{{$search}}" required autocomplete="off">
                                <div class="input-group-append">
                                    <button type="submit" class="input-group-text">
                                        {{translate('Search')}}
                                    </button>
                                </div>
                            </div>
                        </form>


                        <!-- Unfold -->
                        <div class="hs-unfold mr-2">
                            <a class="js-hs-unfold-invoker btn btn-sm btn-outline-primary-2 dropdown-toggle min-height-40" href="javascript:;"
                                data-hs-unfold-options='{
                                        "target": "#usersExportDropdown",
                                        "type": "css-animation"
                                    }'>
                                <i class="tio-download-to mr-1"></i> {{ translate('export') }}
                            </a>

                            <div id="usersExportDropdown"
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                <span class="dropdown-header">{{ translate('download') }}
                                    {{ translate('options') }}</span>
                                <a id="export-excel" class="dropdown-item" href="{{route('admin.pos.orders.export', ['branch_id'=>Request::get('branch_id'), 'start_date'=>Request::get('start_date'), 'end_date'=>Request::get('end_date'), 'search'=>Request::get('search')])}}">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                        alt="Image Description">
                                    {{ translate('excel') }}
                                </a>
                            </div>
                        </div>
                        <!-- End Unfold -->
                    </div>
                </div>
                <!-- Table -->
                <div class="table-responsive datatable-custom">
                    <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                        <tr>
                            <th class="">
                                {{translate('#')}}
                            </th>
                            <th>{{translate('order id')}}</th>
                            <th>{{translate('order date')}}</th>
                            <th>{{translate('customer info')}}</th>
                            <th>{{translate('Branch')}}</th>
                            <th>{{translate('total amount')}}</th>
                            <th class="text-center">{{translate('order')}} {{translate('status')}}</th>
                            <th class="text-center">{{translate('order')}} {{translate('type')}}</th>
                            <th class="text-center">{{translate('actions')}}</th>
                        </tr>
                        </thead>

                        <tbody id="set-rows">
                        @foreach($orders as $key=>$order)
                            <tr class="status-{{$order['order_status']}} class-all">
                                <td class="">
                                    {{$key+$orders->firstItem()}}
                                </td>
                                <td>
                                    <a href="{{route('admin.pos.order-details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                                </td>
                                <td>{{date('d M Y',strtotime($order['created_at']))}}</td>

                                <td>
                                    @if(isset($order->customer))
                                        <div>
                                            <a class="text-body text-capitalize font-medium"
                                               href="{{route('admin.customer.view',[$order['user_id']])}}">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</a>
                                        </div>
                                        <div class="text-sm">
                                            <a href="Tel:{{$order->customer['phone']}}">{{$order->customer['phone']}}</a>
                                        </div>
                                    @elseif($order->user_id != null && !isset($order->customer))
                                        <label
                                            class="text-danger">{{translate('Customer_not_available')}}
                                        </label>
                                    @else
                                        <label
                                            class="text-success">{{translate('Walking Customer')}}
                                        </label>
                                    @endif
                                </td>
                                <td>
                                    <label class="badge badge-soft-primary">{{$order->branch?$order->branch->name:'Branch deleted!'}}</label>
                                </td>
                                <td>
                                    <div class="mw-85">
                                    <div>
                                        <?php
                                            $vat_status = isset($order->details[0]) ? $order->details[0]->vat_status : '';
                                            if ($vat_status == 'included') {
                                                $order_amount = $order['order_amount'] - $order['total_tax_amount'];
                                            } else {
                                                $order_amount = $order['order_amount'];
                                            }
                                        ?>
                                        {{ Helpers::set_symbol($order_amount) }}
                                    </div>
                                        @if($order->payment_status=='paid')
                                            <span class="text-success">
                                                {{translate('paid')}}
                                            </span>
                                        @else
                                            <span class="text-danger">
                                                {{translate('unpaid')}}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-capitalize text-center">
                                    @if($order['order_status']=='pending')
                                        <span class="badge badge-soft-info">
                                            {{translate('pending')}}
                                        </span>
                                    @elseif($order['order_status']=='confirmed')
                                        <span class="badge badge-soft-info">
                                        {{translate('confirmed')}}
                                        </span>
                                    @elseif($order['order_status']=='processing')
                                        <span class="badge badge-soft-warning">
                                        {{translate('packaging')}}
                                        </span>
                                    @elseif($order['order_status']=='picked_up')
                                        <span class="badge badge-soft-warning">
                                        {{translate('out_for_delivery')}}
                                        </span>
                                    @elseif($order['order_status']=='delivered')
                                        <span class="badge badge-soft-success">
                                        {{translate('delivered')}}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger">
                                        {{ translate(str_replace('_',' ',$order['order_status'])) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-capitalize text-center">
                                    @if($order['order_type']=='take_away')
                                        <span class="badge badge-soft-info">
                                            {{translate('take_away')}}
                                        </span>
                                    @elseif($order['order_type']=='pos')
                                        <span class="badge badge-soft-info">
                                        {{translate('POS')}}
                                    </span>
                                    @else
                                        <span class="badge badge-soft-success">
                                        {{translate('delivery')}}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="action-btn btn--primary btn-outline-primary"
                                                href="{{route('admin.pos.order-details',['id'=>$order['id']])}}"><i
                                                        class="tio-visible-outlined"></i></a>
                                       <button class="action-btn btn-outline-primary-2" target="_blank" type="button"
                                                onclick="print_invoice('{{$order->id}}')"><i
                                                class="tio-print"></i>
                                       </button>
                                      <button class="action-btn btn-outline-info-2" target="_blank" type="button"
                                                onclick="print_invoicea4('{{$order->id}}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                                          <path d="M448 192V77.25c0-8.49-3.37-16.62-9.37-22.63L393.37 9.37c-6-6-14.14-9.37-22.63-9.37H96C78.33 0 64 14.33 64 32v160c-35.35 0-64 28.65-64 64v112c0 8.84 7.16 16 16 16h48v96c0 17.67 14.33 32 32 32h320c17.67 0 32-14.33 32-32v-96h48c8.84 0 16-7.16 16-16V256c0-35.35-28.65-64-64-64zm-64 256H128v-96h256v96zm0-224H128V64h192v48c0 8.84 7.16 16 16 16h48v96zm48 72c-13.25 0-24-10.75-24-24 0-13.26 10.75-24 24-24s24 10.74 24 24c0 13.25-10.75 24-24 24z"/></svg>
                                      </button>
                                      <!--
                                        <a class="action-btn btn-outline-primary-2" target="_blank" href="{{route('admin.orders.generate-invoice',[$order['id']])}}">
                                            <i class="tio-print"></i>
                                        </a>
                                      -->
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- End Table -->
                @if(count($orders) == 0)
                <div class="text-center p-4">
                    <img class="w-120px mb-3" src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description">
                    <p class="mb-0">{{translate('No_data_to_show')}}</p>
                </div>
                @endif
                <div class="card-footer px-0">
                    {!! $orders->links() !!}
                </div>
                <!-- End Header -->
            </div>


        </div>
        <!-- End Card -->
    </div>

    <div class="modal fade" id="print-invoice" tabindex="-1">
{{--        <div class="modal-dialog" style=" width: 1000px;" >--}}
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">  {{translate('print')}}{{translate('invoice')}}</h5>

{{--                    <button type="button" class="close" aria-label="Close">--}}
{{--                    <span aria-hidden="true">&times;</span>--}}
{{--                      <a href="{{url()->previous()}}" class="btn btn-danger non-printable">--}}
{{--                        <span aria-hidden="true">&times;</span>--}}
{{--                      </a>--}}
{{--                    </button>--}}

                </div>
                <div class="modal-body">
                    <center>
                        <input type="button" class="btn btn-primary non-printable" onclick="printDiv('printableArea')"
                            value="{{translate('Proceed, If thermal printer is ready.')}}"/>
                        <a href="{{url()->previous()}}" class="btn btn-danger non-printable">Back</a>
                    </center>
                    <hr class="non-printable">
                    <div class="row m-auto" id="printableArea">

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>

    <script>
        function print_invoice(order_id) {
            $.get({
                url: '{{url('/')}}/admin/pos/invoice/'+order_id,
                dataType: 'json',
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    // console.log("success...")
                    $('#print-invoice').modal('show');
                    $('#printableArea').empty().html(data.view);
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        }

        function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            location.reload();
        }
    </script>

    <script>
      function print_invoicea4 (order_id) {
        $.get({
          url: '{{url('/')}}/admin/pos/invoicea4/'+order_id,
          dataType: 'json',
          beforeSend: function () {
            $('#loading').show();
          },
          success: function (data) {
            // console.log("success...")
            $('#print-invoice').modal('show');
            $('#printableArea').empty().html(data.view);
          },
          complete: function () {
            $('#loading').hide();
          },
        });
      }

      function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        location.reload();
      }
    </script>


    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF FLATPICKR
            // =======================================================
            $('.js-flatpickr').each(function () {
                $.HSCore.components.HSFlatpickr.init($(this));
            });
        });

    </script>


@endpush
