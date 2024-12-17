@extends('layouts.admin.app')

@section('title', translate(' Brand Foods invoice'))

@push('css_or_js')
  <meta charset="utf-8">
  {{--  <title>Example 1</title>--}}
  <link rel="stylesheet" href="{{asset('/public/assets/admin/css/incoidea4/style.css')}}" media="all" />
  
@endpush


@section('content')

<div class="content container-fluid initial-38">
    <div class="row justify-content-center">

    <div class="col-md-12">
      <center>
        <input type="button" class="btn btn--primary non-printable text-white" onclick="Export2Word('printableArea')"
               value="{{translate('Proceed, If thermal printer is ready.')}}"/>
        <a href="{{url()->previous()}}" class="btn btn--danger non-printable text-white">{{ translate('Back') }}</a>
      </center>
      <hr class="non-printable">
    </div>
      <div class="initial-38-1" style="max-width: 750px !important;" id="printableArea">
    <div class="bodya4">

      <header class="clearfix">

        <div id="logo">
          <img src="{{asset('/public/assets/admin/img/logo_print.jpg')}}" syley="height: 200px !important; width: 50% !important;" class="initial-38-2" alt="">
        </div>
        <h3>Brand Foods </h3>
        <div id="company" class="clearfix">
          <!--<div>{{ $order->branch->name }}</div>-->
          <!--<div>{{ $order->branch->address }}</div>-->
          <div>

            <table>
  <tr>
    <td>{{ translate('Order ID') }} :<span class="font-light"> {{$order['id']}}</span></td>
    <td> {{$order->customer['f_name'].' '.$order->customer['l_name']}}</td>
    <td> {{$order->customer['phone']}}</td>
    <td> {{$order->delivery_address?$order->delivery_address['address']:''}}</td>
    <td></td>
  </tr>
</table>


          </div>
        </div>
        <div id="project">

        </div>

      </header>

      <main>
        <table>
          <thead>
          <tr>
            <th class="service">name</th>
            <th>{{ translate('Qty') }}</th>
            <th> {{ translate('Unit Price') }} </th>
            <th>{{ translate('total') }}</th>
          </tr>
          </thead>

          <tbody>
          @php($sub_total=0)
          @php($total_tax=0)
          @php($total_dis_on_pro=0)
          @php($updated_total_tax=0)
          @php($vat_status = '')

          @foreach($order->details as $detail)

            @if($detail->product_details !=null)
              @php($product = json_decode($detail->product_details, true))
              <tr>
                <th class="service">
                  {{$product['name']}}
                </th>
                 <th class="qty">
                  {{ $detail['quantity']}}
                </th>
                <th class="unit">
                  {{ Helpers::set_symbol($detail['price']) }}
                </th>
                <th class="total">
                  @php($amount=($detail['price']-$detail['discount_on_product'])*$detail['quantity'])
                  {{ Helpers::set_symbol($amount) }}
                </th>
              </tr>
              @php($sub_total+=$amount)
              @php($total_tax+=$detail['tax_amount']*$detail['quantity'])
              @php($updated_total_tax+= $detail['vat_status'] === 'included' ? 0 : $detail['tax_amount']*$detail['quantity'])
              @php($vat_status = $detail['vat_status'])

            @endif

          @endforeach
          <dd class="col-12">
            <hr>
          </dd>
          {{--    <dt class="col-6">{{ translate('Delivery Fee') }}:</dt>--}}
          <dd class="col-12">
            @if($order['order_type']=='take_away')
              @php($del_c=0)
            @else
              @php($del_c=$order['delivery_charge'])
            @endif
            {{--      {{ Helpers::set_symbol($del_c) }}--}}
          </dd>

          <tr>
            <th colspan="3" class="grand total">{{ translate('Total') }}:</th>
            <th class="grand total">{{ Helpers::set_symbol($sub_total+$del_c+$updated_total_tax-$order['coupon_discount_amount']-$order['extra_discount']) }}</th>
          </tr>


          </tbody>
        </table>

        <div class="px-3">

 
   <table>
  <tr>
    <td>{{ translate('Subtotal') }}: {{ Helpers::set_symbol($sub_total+$updated_total_tax) }}</span></td>
    <td> {{ translate('Coupon Discount') }}: - {{ Helpers::set_symbol($order['coupon_discount_amount']) }}</td>
    <td> {{ translate('Delivery Fee') }}:</td>
    <td>              @if($order['order_type']=='take_away')
                @php($del_c=0)
              @else
                @php($del_c=$order['delivery_charge'])
              @endif
              {{ Helpers::set_symbol($del_c) }}</td>
    <td></td>
    <td>{{ translate('Total') }}:{{ Helpers::set_symbol($sub_total+$del_c+$updated_total_tax-$order['coupon_discount_amount']-$order['extra_discount']) }}</td>
  </tr>
</table>

  <table>
  <tr>
    <td>Brand Foods</span></td>
    <td> {{\App\Model\BusinessSetting::where(['key'=>'phone'])->first()->value}}</td>
    <td> {{date('d M Y h:i a',strtotime($order['created_at']))}}</td>
    <td></td>
  </tr>
</table>

          
            
          <!--<span class="d-block text-center">{{ $footer_text = \App\Model\BusinessSetting::where(['key' => 'footer_text'])->first()->value }}</span>-->
        </div>
        <!--<div id="notices">-->
        <!--  <div>NOTICE:</div>-->
        <!--  <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>-->

        <!--</div>-->
      </main>

    </div>
    </div>

  </div>

</div>


@endsection

@push('script')
  <script>
    function printDiv(divName) {
      var printContents = document.getElementById(divName).innerHTML;
      var originalContents = document.body.innerHTML;
      document.body.innerHTML = printContents;
      window.print();
     // document.body.innerHTML = originalContents;
    }
    
function Export2Word(element, filename = ''){
          var printContents = document.getElementById(element).innerHTML;
      var originalContents = document.body.innerHTML;
      document.body.innerHTML = printContents;
      
    var preHtml = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'><head><meta charset='utf-8'><title>Export HTML To Doc</title></head><body>";
    var postHtml = "</body></html>";
    var html = document.documentElement.innerHTML;

    var blob = new Blob(['\ufeff', html], {
        type: 'application/msword'
    });
    
    // Specify link url
    var url = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(html);
    
    // Specify file name
    filename = filename?filename+'.doc':'document.doc';
    
    // Create download link element
    var downloadLink = document.createElement("a");

    document.body.appendChild(downloadLink);
    
    if(navigator.msSaveOrOpenBlob ){
        navigator.msSaveOrOpenBlob(blob, filename);
    }else{
        // Create a link to the file
        downloadLink.href = url;
        
        // Setting the file name
        downloadLink.download = filename;
        
        //triggering the function
        downloadLink.click();
    }
    
    document.body.removeChild(downloadLink);
}
  </script>
@endpush
