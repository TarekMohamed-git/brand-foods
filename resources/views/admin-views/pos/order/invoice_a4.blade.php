<!DOCTYPE html>
<html lang="en">

  <meta charset="utf-8">
{{--  <title>Example 1</title>--}}
  <link rel="stylesheet" href="{{asset('/public/assets/admin/css/incoidea4/style.css')}}" media="all" />


<body>
  <div class="bodya4">

<header class="clearfix">

  <div id="logo">
    <img src="{{asset('/public/assets/admin/img/food.png')}}" class="initial-38-2" alt="">
  </div>
  <h1>Brand Foods </h1>
  <div id="company" class="clearfix">
    <div>{{ $order->branch->name }}</div>
    <div>{{ $order->branch->address }}</div>
    <div> {{ translate('Phone') }} : {{\App\Model\BusinessSetting::where(['key'=>'phone'])->first()->value}}</div>
    @if ($order->branch->gst_status)
      <h5 class="initial-38-4 initial-38-3 fz-12px">
        {{ translate('Gst No') }} : {{ $order->branch->gst_code }}
      </h5>
    @endif
    <div>

    <h5>{{ translate('Order ID') }} :
      <span class="font-light"> {{$order['id']}}</span>
    </h5>

  </div>
  </div>
  <div id="project">
    <h5>
      <span class="font-light">
      {{date('d M Y h:i a',strtotime($order['created_at']))}}
      </span>
    </h5>
  </div>
</header>
<main>
  <table>
    <thead>
    <tr>
      <th class="service">name</th>
      <th> {{ translate('Unit Price') }} </th>
      <th>{{ translate('Qty') }}</th>
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
              <th class="unit">
               {{ Helpers::set_symbol($detail['price']) }}
              </th>
              <th class="qty">
                {{ $detail['quantity']}}
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
      <td colspan="3">{{ translate('Subtotal') }}:</td>
      <td class="total">{{ Helpers::set_symbol($sub_total+$updated_total_tax) }}</dd></td>
    </tr>
    <tr>
      <td colspan="3">{{translate('Tax / VAT')}} {{ $vat_status == 'included' ? translate('(included)') : '' }}:</td>
      <td class="total">{{ Helpers::set_symbol($total_tax) }}</td>
    </tr>

    <tr>
      <td colspan="3" class="grand total">{{ translate('Total') }}:</td>
      <td class="grand total">{{ Helpers::set_symbol($sub_total+$del_c+$updated_total_tax-$order['coupon_discount_amount']-$order['extra_discount']) }}</td>
    </tr>


    </tbody>
  </table>

  <div class="px-3">
    <dl class="row text-right justify-content-center">
      <dt class="col-6">{{ translate('Items Price') }}:</dt>
      <dd class="col-6">{{ Helpers::set_symbol($sub_total) }}</dd>
      <dt class="col-6">{{translate('Tax / VAT')}} {{ $vat_status == 'included' ? translate('(included)') : '' }}:</dt>
      <dd class="col-6">{{ Helpers::set_symbol($total_tax) }}</dd>

      <dt class="col-6">{{ translate('Subtotal') }}:</dt>
      <dd class="col-6">
        {{ Helpers::set_symbol($sub_total+$updated_total_tax) }}</dd>
      <dt class="col-6">{{ translate('Coupon Discount') }}:</dt>
      <dd class="col-6">
        - {{ Helpers::set_symbol($order['coupon_discount_amount']) }}</dd>
      @if($order['order_type'] == 'pos')
        <dt class="col-sm-6">{{translate('extra Discount')}}:</dt>
        <dd class="col-sm-6">
          - {{ Helpers::set_symbol($order['extra_discount']) }}</dd>
      @endif
      <dt class="col-6">{{ translate('Delivery Fee') }}:</dt>
      <dd class="col-6">
        @if($order['order_type']=='take_away')
          @php($del_c=0)
        @else
          @php($del_c=$order['delivery_charge'])
        @endif
        {{ Helpers::set_symbol($del_c) }}
        <hr>
      </dd>

      <dt class="col-6 font-20px">{{ translate('Total') }}:</dt>
      <dd class="col-6 font-20px">{{ Helpers::set_symbol($sub_total+$del_c+$updated_total_tax-$order['coupon_discount_amount']-$order['extra_discount']) }}</dd>
    </dl>
{{--    <span class="initial-38-5">---------------------------------------------------------------------------------</span>--}}
    <h5 class="text-center pt-1">
      <span class="d-block">"""{{ translate('THANK YOU') }}"""</span>
    </h5>
{{--    <span class="initial-38-5">---------------------------------------------------------------------------------</span>--}}
    <span class="d-block text-center">{{ $footer_text = \App\Model\BusinessSetting::where(['key' => 'footer_text'])->first()->value }}</span>
  </div>
  <div id="notices">
    <div>NOTICE:</div>
    <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
  </div>
</main>

<footer>
  <div class="footera4">
  Invoice was created on a computer and is valid without the signature and seal.
  </div>
</footer>
  </div>
</body>
</html>
