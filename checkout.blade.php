@if($allsettings->maintenance_mode == 0)
<!DOCTYPE HTML>
<html lang="en">
<head>
<title>{{ $allsettings->site_title }} - {{ Helper::translation(2899,$translate,'') }}</title>
@include('meta')
@include('style')
</head>
<body>
@include('header')
<div class="page-title-overlap pt-4" style="background-image: url('{{ url('/') }}/public/storage/settings/{{ $allsettings->site_banner }}');">
      <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
        <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb flex-lg-nowrap justify-content-center justify-content-lg-star">
              <li class="breadcrumb-item"><a class="text-nowrap" href="{{ URL::to('/') }}"><i class="dwg-home"></i>{{ Helper::translation(2862,$translate,'') }}</a></li>
              <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ Helper::translation(2899,$translate,'') }}</li>
              </li>
             </ol>
          </nav>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
          <h1 class="h3 mb-0 text-white">{{ Helper::translation(2899,$translate,'') }}</h1>
        </div>
      </div>
    </div>
<div class="container mb-5 pb-3">
      <div class="bg-light box-shadow-lg rounded-lg overflow-hidden">
        <div class="row">

          <!-- Content-->
          @if($cart_count != 0)
          <section class="col-lg-8 pt-2 pt-lg-4 pb-4 mb-3">
          <form action="{{ route('checkout') }}" class="needs-validation" id="checkout_form" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="pt-2 px-4 pr-lg-0 pl-xl-5">
             <input type="hidden" name="order_firstname" value="{{ Auth::user()->name }}">
             <input type="hidden" name="order_email" value="{{ Auth::user()->email }}">
             <div class="widget mb-3 d-lg-none">
                <h2 class="widget-title">{{ Helper::translation(2900,$translate,'') }}</h2>
                @php
                $subtotal = 0;
                $order_id = '';
                $item_price = '';
                $item_userid = '';
                $item_user_type = '';
                $commission = 0;
                $vendor_amount = 0;
                $single_price = 0;
                $coupon_code = "";
                $new_price = 0;
                $sale_price_discount_total = 0;
                @endphp
                @foreach($cart['item'] as $cart)
                <div class="media align-items-center pb-2 pt-2 border-bottom">
                <a class="d-block mr-2" href="{{ url('/item') }}/{{ $cart->item_slug }}">
                @if($cart->item_thumbnail!='')
                <img class="rounded-sm" width="64" src="{{ url('/') }}/public/storage/items/{{ $cart->item_thumbnail }}" alt="{{ $cart->item_name }}"/>
                @else
                <img class="rounded-sm" width="64" src="{{ url('/') }}/public/img/no-image.png" alt="{{ $cart->item_name }}"/>
                @endif
                </a>
                  <div class="media-body pl-1">
                    <h6 class="widget-product-title"><a href="{{ url('/item') }}/{{ $cart->item_slug }}">{{ $cart->item_name }}</a></h6>
                      <div class="widget-product-meta">@if($cart->sale_price != 0)<span class="has-text-grey-light font-weight-medium cross-line">{{ $allsettings->site_currency_symbol }}{{ $cart->regular_price }}</span> <span class="text-accent font-weight-medium border-right pr-2 mr-2">{{ $allsettings->site_currency_symbol }}{{ $cart->item_price }}</span>@else <span class="border-right font-weight-medium pr-2 mr-2">{{ $allsettings->site_currency_symbol }}{{ $cart->item_price }}</span> @endif<span class="font-size-xs text-muted">{{ $cart->license }}@if($cart->license == 'regular') ({{ $additional->regular_license }}) @elseif($cart->license == 'extended') ({{ $additional->extended_license }}) @endif</span></div>
                  </div>
                </div>
                @php
                $subtotal += $cart->item_price;
                $order_id .= $cart->ord_id.',';
                $item_price .= $cart->item_price.',';
                $item_userid .= $cart->item_user_id.',';
                $item_user_type .= $cart->exclusive_author;
                $amount_price = $subtotal;
                $single_price += $cart->item_price;
                if($cart->discount_price != 0)
                {
                    $price = $cart->discount_price;
                    $new_price += $cart->discount_price;
                    $coupon_code = $cart->coupon_code;
                }
                else
                {
                   $price = $cart->item_price;
                   $new_price += $cart->item_price;
                   $coupon_code = "";
                }
				if($item_user_type == 1)
                {
                   $commission +=($price * $allsettings->site_exclusive_commission) / 100;
                }
                else
                {
                   $commission +=($price * $allsettings->site_non_exclusive_commission) / 100;
                }
                if($cart->sale_price != 0)
                {
                    $sale_price_discount = $cart->regular_price - $cart->sale_price;
                }
                else
                {
                   $sale_price_discount = 0;
                }
                $sale_price_discount_total += $sale_price_discount;
                @endphp
                @endforeach
                <ul class="list-unstyled font-size-sm py-3">
                @if($allsettings->site_extra_fee != 0)
                  <li class="d-flex justify-content-between align-items-center"><span class="mr-2">{{ Helper::translation(2901,$translate,'') }}</span><span class="text-right">{{ $allsettings->site_currency_symbol }} {{ $allsettings->site_extra_fee }}</span></li>
                  @endif
                  @if($coupon_code != "")
                  @php
                  $coupon_discount = $subtotal - $new_price;
                  $final = $new_price + $allsettings->site_extra_fee;
                  $last_price =  $new_price;
                  $priceamount = $new_price;
                  @endphp
                  <li class="d-flex justify-content-between align-items-center font-size-base"><span class="mr-2">{{ Helper::translation(2895,$translate,'') }}</span><span class="text-right">{{ $coupon_discount }} {{ $allsettings->site_currency }}</span></li>
                  @else
                  @php
                  $final = $subtotal+$allsettings->site_extra_fee;
                  $last_price =  $subtotal;
                  $priceamount = $subtotal;
                  @endphp
                  @endif
                  @if($country_percent != 0)
                  @php
                  $vat_price = ($single_price * $country_percent) / 100;
                  @endphp
                  <li class="d-flex justify-content-between align-items-center font-size-base"><span class="mr-2">VAT (%)</span><span class="text-right">{{ $allsettings->site_currency_symbol }} {{ $vat_price }}</span></li>
                  @else
                  @php
                  $vat_price = 0;
                  @endphp
                  @endif
                  @if($sale_price_discount_total !=0)
                  <li class="d-flex justify-content-between align-items-center text-accent"><span class="mr-2">Total Discounts</span><span class="text-right">{{ $allsettings->site_currency_symbol }} {{ $sale_price_discount_total }}</span></li>
                  @endif
                  <li class="checkout-total-price d-flex justify-content-between align-items-center font-size-base"><span class="mr-2">{{ Helper::translation(2896,$translate,'') }}</span><span class="text-right">{{ $allsettings->site_currency_symbol }} {{ $final+$vat_price }}</span></li>
                </ul>
                @php
                $vendor_amount = $priceamount - $commission;
                @endphp
                 <input type="hidden" name="orderssss_id" value="{{ $sale_price_discount_total }}">
                <input type="hidden" name="order_id" value="{{ rtrim($order_id,',') }}">
                <input type="hidden" name="item_prices" value="{{ base64_encode(rtrim($item_price,',')) }}">
                <input type="hidden" name="item_user_id" value="{{ rtrim($item_userid,',') }}">
                <input type="hidden" name="vat_price" value="{{ base64_encode($vat_price) }}">
                <input type="hidden" name="amount" value="{{ base64_encode($last_price) }}">
                <input type="hidden" name="processing_fee" value="{{ base64_encode($allsettings->site_extra_fee) }}">
                <input type="hidden" name="website_url" value="{{ url('/') }}">
                <input type="hidden" name="admin_amount" value="{{ base64_encode($commission) }}">
                <input type="hidden" name="vendor_amount" value="{{ base64_encode($vendor_amount) }}">
                <input type="hidden" name="token" class="token">
                <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}">
               </div>
                <div class="payment-logos mb-4" style="font-family:'FontAwesome';text-align:center;font-size:30px;">
                    <i class="fab fa-cc-visa" style="color:#1434cb;"></i>
                    <i class="fab fa-cc-mastercard" style="color:#f14f1b;"></i>
                    <i class="fab fa-cc-amex" style="color:#016fd0;"></i>
                    <i class="fab fa-cc-discover" style="color:#ef7d00;"></i>
                </div>
              <div class="accordion mb-2" id="payment-method" role="tablist">
                @php $no = 1; @endphp
                @foreach($get_payment as $payment)
                @php
                if($payment == '2checkout')
                {
                $payment = 'twocheckout';
                }
                else
                {
                $payment = $payment;
                }
                @endphp
                <div class="card">
                  <div class="card-header" role="tab">
                    <h3 class="accordion-heading payment-accordion"><a href="#{{ $payment }}" id="{{ $payment }}" data-toggle="collapse">{{ Helper::translation(4899,$translate,'') }} @if($payment == 'twocheckout') {{ Helper::translation(4902,$translate,'') }} @elseif($payment == 'paypal')<i class="fab fa-cc-paypal" style="color:#01206a;font-size:2rem;vertical-align:middle;padding-left:10px;"></i> @elseif($payment == 'stripe')<i class="fab fa-cc-stripe" style="color:#665bff;font-size:2rem;vertical-align:middle;padding-left:10px;"></i> @elseif($payment == 'razorpay') <svg version="1.1" id="Layer_1" focusable="false" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 576 412.9" style="width:2.5rem;fill:#3395ff;margin-left:10px;" xml:space="preserve"> <g> <path d="M386,188.7c-1.4-4.1-4.1-6.1-8.1-6.1c-4.1,0-7.6,2-10.5,5.9c-3,3.9-5.1,9.5-6.5,16.9c-1.4,7.3-1.4,12.9,0.1,16.8 c1.5,3.9,4.3,5.8,8.3,5.8s7.6-1.9,10.5-5.7c2.9-3.8,5-9.3,6.4-16.5l-0.1,0C387.5,198.4,387.5,192.8,386,188.7z"/> <path d="M141.2,182.8c-4.1,0-7.6,2-10.6,6.1c-3,4.1-5.2,9.7-6.6,16.9s-1.3,12.7,0.2,16.5c1.5,3.8,4.3,5.7,8.4,5.7 c4.1,0,7.6-1.9,10.5-5.8c2.9-3.9,5.1-9.5,6.5-16.8l0,0.1c1.4-7.3,1.4-12.9-0.1-16.9C148,184.8,145.2,182.8,141.2,182.8z"/> <path d="M264.8,182.6c-4.1,0-7.7,1.9-10.6,5.7c-2.9,3.8-5.1,9.5-6.5,17c-2.9,15.1-0.1,22.7,8.4,22.7c4.1,0,7.5-1.9,10.4-5.6 c2.9-3.7,5-9.5,6.5-17.1v0c1.4-7.5,1.5-13.2,0-17C271.6,184.5,268.9,182.6,264.8,182.6z"/> <path d="M528,0H48C21.5,0,0,19.8,0,44.2v324.5c0,24.4,21.5,44.2,48,44.2h480c26.5,0,48-19.8,48-44.2V44.2C576,19.8,554.5,0,528,0z M94.6,210.6l2.9,33.7h-18l-3-36.2c-0.3-2.8-1.1-4.8-2.5-6c-1.4-1.1-3.4-1.7-5.8-1.7h-11l-8.4,43.8H32l20.8-108.6h34.5 c9,0,15.3,2.8,18.9,8.2c3.6,5.5,4.4,13.4,2.4,23.6v0c-1.3,7-3.9,13.5-7.6,18.6c-3.7,5.2-8.3,8.9-13.9,11.1 C91.5,198.7,94,203.1,94.6,210.6z M142.1,244.4l2.2-11.5c-2.4,4-5.5,7.1-8.9,9.3c-3.4,2.2-7.2,3.4-11,3.4c-4.8,0-8.7-1.6-11.8-4.9 c-3-3.2-5-7.9-5.9-14c-0.9-6-0.6-13,0.9-21c1.5-7.9,3.9-15,7.2-21.2c3.3-6.2,7.1-11,11.4-14.4c4.3-3.4,8.9-5.1,13.6-5.1 c3.8,0,7.1,1.2,9.7,3.5c2.6,2.4,4.4,5.5,5.3,9.5l2.1-11l0,0h16.3l-14.8,77.4H142.1z M221,227.1l-3.3,17.2H169l3.1-16.2l36.3-43.9 H181l3.3-17.2h47.2l-3,15.5l-36.9,44.6H221z M289.3,205.2c-1.6,8.1-4.1,15.3-7.6,21.4c-3.5,6.1-7.8,10.8-12.8,14.1 c-5,3.3-10.4,4.9-16.2,4.9c-5.9,0-10.7-1.6-14.5-4.9c-3.8-3.2-6.3-7.9-7.4-14c-1.2-6.1-1-13.2,0.5-21.3c1.6-8.1,4.1-15.3,7.6-21.4 c3.4-6,7.8-10.8,12.8-14c5-3.2,10.5-4.9,16.4-4.9c5.8,0,10.6,1.6,14.3,4.9l0.1-0.1c3.7,3.3,6.2,7.9,7.4,14 C291.1,190,290.9,197.1,289.3,205.2z M321.9,190c-2.9,3.3-4.9,7.9-6.1,13.8l-7.7,40.4h-16.5l14.9-77.3h11.6l4.7,0l-2.2,11.3 c2.1-3.9,4.6-7,7.6-9.3c3.4-2.6,7-3.9,10.7-3.9c2.3,0,4.2,0.5,5.6,1.5l-4.2,21.1l0,0c-2.4-1.7-4.9-2.6-7.6-2.6 C328.4,185.1,324.8,186.8,321.9,190z M402.6,205.7c-1.5,7.9-3.9,14.9-7.1,21c-3.2,6-6.9,10.7-11.3,14c-4.3,3.2-8.9,4.9-13.7,4.9 c-3.9,0-7.1-1.1-9.7-3.3c-2.6-2.2-4.3-5.3-5.2-9.3l-8.5,44.4h-16.5l16.9-88.2l0.1-0.7l4.1-21.4h16.1l-2.8,12.3l-0.1,0.5 c2.3-4.6,5.4-8.2,9.1-10.8c3.7-2.7,7.7-4,11.8-4c4.7,0,8.6,1.7,11.7,5.1l0,0c3,3.4,5,8.2,5.9,14.4 C404.5,190.7,404.1,197.7,402.6,205.7z M444.8,244.3l2.2-11.5c-2.4,4-5.5,7.1-8.9,9.3c-3.5,2.2-7.2,3.4-11,3.4 c-4.8,0-8.7-1.6-11.8-4.9c-3.1-3.3-5-7.9-5.9-14c-0.9-6.1-0.6-13,0.9-21c1.5-7.9,3.9-15,7.2-21.2c3.3-6.2,7.1-11,11.4-14.4 c4.4-3.4,8.9-5.1,13.6-5.1c3.8,0,7.1,1.2,9.7,3.5c2.6,2.3,4.3,5.5,5.3,9.5l2.1-11l0,0h16.2l-14.8,77.3H444.8z M544,167l-20.5,40.7 l-24.4,48.4l-0.2,0.3l-5.4,10.8c-0.2,0.3-0.3,0.5-0.4,0.8l-4.7,9.2h-17.2l19.3-37.3l-8.7-73H499l4.3,48l21-41l0.3-0.6l0.7-1.2 l2.7-5.2h5.2c0.3,0,0.6,0,0.9,0L544,167L544,167L544,167L544,167L544,167z"/> <path d="M81.3,154H65.8l-5.4,28.4h15.5c4.9,0,8.6-1.1,11.3-3.4c2.7-2.3,4.5-5.9,5.4-10.8v0c0.9-4.9,0.5-8.4-1.3-10.8 S86.2,154,81.3,154z"/> <path d="M444,182.7c-4.1,0-7.6,2-10.7,6.1s-5.2,9.7-6.6,16.9c-1.4,7.2-1.3,12.7,0.2,16.5c1.5,3.8,4.3,5.7,8.4,5.7 c4.1,0,7.6-1.9,10.5-5.8c2.9-3.9,5.1-9.5,6.5-16.8l0,0c1.4-7.3,1.4-12.9,0-16.9C450.8,184.6,448,182.7,444,182.7z"/> </g> </svg> @else {{ $payment }} @endif<span class="accordion-indicator"><i data-feather="chevron-up"></i></span></a></h3>
                  </div>
                  <div class="collapse @if($no == 1) show @endif" id="{{ $payment }}" data-parent="#payment-method" role="tabpanel">
                  @if($payment == 'paypal')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" @if($no == 1) checked @endif data-bvalidator="required"> {{ Helper::translation(5937,$translate,'') }}</span> - {{ Helper::translation(4905,$translate,'') }}</p>
                      <button class="btn btn-pay-now" type="submit">{{ Helper::translation(4908,$translate,'') }}</button>
                    </div>
                    @endif
                  @if($payment == 'stripe')
                    <div class="card-body font-size-sm custom-radio custom-control">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio"  value="{{ $payment }}" @if($no == 1) checked @endif data-bvalidator="required"> {{ Helper::translation(5940,$translate,'') }}</span> - {{ Helper::translation(2903,$translate,'') }}</p>
                      <div class="stripebox mb-3" id="ifYes" style="display:none;">
                        <label for="card-element">{{ Helper::translation(2903,$translate,'') }}</label>
                        <div id="card-element" class="field"></div>
                        <div id="card-errors" role="alert"></div>
                        <div class="group">
                            <label>
                                <span>Name</span>
                                <input id="first-name" name="stripe_first_name" class="field"/>
                            </label>
                            <label>
                                <span>Address</span>
                                <input id="address-line1" name="stripe_address_line1" class="field"/>
                            </label>
                            <label>
                                <span>City</span>
                                <input id="address-city" name="stripe_address_city" class="field" />
                            </label>
                            <label>
                                <span>ZIP</span>
                                <input id="address-zip" name="stripe_address_zip" class="field"/>
                            </label>
                          </div>
                     </div>
                      <button id="stripe_payment_submit" class="btn btn-pay-now">{{ Helper::translation(4911,$translate,'') }}</button>
                    </div>
                    @endif
                    @if($payment == 'wallet')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" @if($no == 1) checked @endif data-bvalidator="required"> {{ Helper::translation(5943,$translate,'') }}</span> - ({{ $allsettings->site_currency }} {{ Auth::user()->earnings }})</p>
                      <button class="btn btn-primary" type="submit">{{ Helper::translation(4914,$translate,'') }}</button>
                    </div>
                    @endif
                    @if($payment == 'twocheckout')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" @if($no == 1) checked @endif data-bvalidator="required"> {{ Helper::translation(4902,$translate,'') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ Helper::translation(4917,$translate,'') }}</button>
                    </div>
                    @endif
                    @if($payment == 'paystack')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" @if($no == 1) checked @endif data-bvalidator="required"> {{ Helper::translation(5946,$translate,'') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ Helper::translation(4920,$translate,'') }}</button>
                    </div>
                    @endif
                    @if($payment == 'localbank')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" @if($no == 1) checked @endif data-bvalidator="required"> {{ Helper::translation(5949,$translate,'') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ Helper::translation(4923,$translate,'') }}</button>
                    </div>
                    @endif
                    @if($payment == 'razorpay')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" @if($no == 1) checked @endif data-bvalidator="required"> {{ __('Razorpay') }}</span></p>
                      <button class="btn btn-pay-now" type="submit">Checkout with Razorpay</button>
                    </div>
                    @endif
                    @if($payment == 'payhere')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" @if($no == 1) checked @endif data-bvalidator="required"> {{ __('Payhere') }}</span></p>
                      <button class="btn btn-primary" type="submit">Checkout with Payhere</button>
                    </div>
                    @endif
                    @if($payment == 'payumoney')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" @if($no == 1) checked @endif data-bvalidator="required"> {{ __('Payumoney') }}</span></p>
                      <button class="btn btn-primary" type="submit">Checkout with Payumoney</button>
                    </div>
                    @endif
                    @if($payment == 'iyzico')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" @if($no == 1) checked @endif data-bvalidator="required"> {{ __('Iyzico') }}</span></p>
                      <button class="btn btn-primary" type="submit">Checkout with Iyzico</button>
                    </div>
                    @endif
                    @if($payment == 'flutterwave')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" @if($no == 1) checked @endif data-bvalidator="required"> {{ __('Flutterwave') }}</span></p>
                      <button class="btn btn-primary" type="submit">Checkout with Flutterwave</button>
                    </div>
                    @endif
                    @if($payment == 'coingate')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" @if($no == 1) checked @endif data-bvalidator="required"> {{ __('Coingate') }}</span></p>
                      <button class="btn btn-primary" type="submit">Checkout with Coingate</button>
                    </div>
                    @endif
                      @if($payment == 'ipay')
                          <div class="card-body font-size-sm custom-control custom-radio">
                              <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" @if($no == 1) checked @endif data-bvalidator="required"> {{ __('iPay') }}</span></p>
                              <button class="btn btn-primary" type="submit">Checkout with iPay</button>
                          </div>
                      @endif
                  </div>
                </div>
                @php $no++; @endphp
                @endforeach
              </div>
            </div>
            </form>
          </section>
          <aside class="col-lg-4 d-none d-lg-block">
            <hr class="d-lg-none">
            <div class="cz-sidebar-static h-100 ml-auto border-left">
              <div class="widget mb-3">
                <h2 class="widget-title text-center">{{ Helper::translation(2900,$translate,'') }}</h2>
                @php
                $subtotal = 0;
                $order_id = '';
                $item_price = '';
                $item_userid = '';
                $item_user_type = '';
                $commission = 0;
                $vendor_amount = 0;
                $single_price = 0;
                $coupon_code = "";
                $new_price = 0;
                @endphp
                @foreach($mobile['item'] as $cart)
                <div class="media align-items-center pb-3 mb-3 border-bottom">
                <a class="d-block mr-2" href="{{ url('/item') }}/{{ $cart->item_slug }}">
                @if($cart->item_thumbnail!='')
                <img class="rounded-sm" width="64" src="{{ url('/') }}/public/storage/items/{{ $cart->item_thumbnail }}" alt="{{ $cart->item_name }}"/>
                @else
                <img class="rounded-sm" width="64" src="{{ url('/') }}/public/img/no-image.png" alt="{{ $cart->item_name }}"/>
                @endif
                </a>
                  <div class="media-body pl-1">
                    <h6 class="widget-product-title"><a href="{{ url('/item') }}/{{ $cart->item_slug }}">{{ $cart->item_name }}</a></h6>
                      <div class="widget-product-meta">@if($cart->sale_price != 0)<span class="has-text-grey-light font-weight-medium cross-line">{{ $allsettings->site_currency_symbol }}{{ $cart->regular_price }}</span> <span class="text-accent font-weight-medium border-right pr-2 mr-2">{{ $allsettings->site_currency_symbol }}{{ $cart->item_price }}</span>@else <span class="border-right font-weight-medium pr-2 mr-2">{{ $allsettings->site_currency_symbol }}{{ $cart->item_price }}</span> @endif<span class="font-size-xs text-muted">{{ $cart->license }}@if($cart->license == 'regular') ({{ $additional->regular_license }}) @elseif($cart->license == 'extended') ({{ $additional->extended_license }}) @endif</span></div>
                  </div>
                </div>
                @php
                $subtotal += $cart->item_price;
                $order_id .= $cart->ord_id.',';
                $item_price .= $cart->item_price.',';
                $item_userid .= $cart->item_user_id.',';
                $item_user_type .= $cart->exclusive_author;
                $amount_price = $subtotal;
                $single_price += $cart->item_price;
                if($cart->discount_price != 0)
                {
                    $price = $cart->discount_price;
                    $new_price += $cart->discount_price;
                    $coupon_code = $cart->coupon_code;
                }
                else
                {
                   $price = $cart->item_price;
                   $new_price += $cart->item_price;
                   $coupon_code = "";
                }
				if($item_user_type == 1)
                {
                   $commission +=($price * $allsettings->site_exclusive_commission) / 100;
                }
                else
                {
                   $commission +=($price * $allsettings->site_non_exclusive_commission) / 100;
                }
                @endphp
                @endforeach
                <ul class="list-unstyled font-size-sm pt-3 pb-2 border-bottom">
                @if($allsettings->site_extra_fee != 0)
                  <li class="d-flex justify-content-between align-items-center"><span class="mr-2">{{ Helper::translation(2901,$translate,'') }}</span><span class="text-right">{{ $allsettings->site_currency_symbol }} {{ $allsettings->site_extra_fee }}</span></li>
                  @endif
                  @if($coupon_code != "")
                  @php
                  $coupon_discount = $subtotal - $new_price;
                  $final = $new_price + $allsettings->site_extra_fee;
                  $last_price =  $new_price;
                  $priceamount = $new_price;
                  @endphp
                  <li class="d-flex justify-content-between align-items-center"><span class="mr-2">{{ Helper::translation(2895,$translate,'') }}</span><span class="text-right">{{ $coupon_discount }} {{ $allsettings->site_currency }}</span></li>
                  @else
                  @php
                  $final = $subtotal+$allsettings->site_extra_fee;
                  $last_price =  $subtotal;
                  $priceamount = $subtotal;
                  @endphp
                  @endif
                  @if($country_percent != 0)
                  @php
                  $vat_price = ($single_price * $country_percent) / 100;
                  @endphp
                  <li class="d-flex justify-content-between align-items-center"><span class="mr-2">VAT (%)</span><span class="text-right">{{ $allsettings->site_currency_symbol }} {{ $vat_price }}</span></li>
                  @else
                  @php
                  $vat_price = 0;
                  @endphp
                  @endif
                    @if($sale_price_discount_total !=0)
                        <li class="d-flex justify-content-between align-items-center text-accent"><span class="mr-2">Total Discounts</span><span class="text-right">{{ $allsettings->site_currency_symbol }} {{ $sale_price_discount_total }}</span></li>
                    @endif
                {{-- <h3 class="font-weight-bold text-center my-4">{{ $allsettings->site_currency_symbol }} {{ $final + $vat_price }}</h3>--}}
                    <li class="checkout-total-price d-flex justify-content-between align-items-center font-size"><span class="mr-2">{{ Helper::translation(2896,$translate,'') }}</span><span class="text-right">{{ $allsettings->site_currency_symbol }} {{ $final+$vat_price }}</span></li>
                  </ul>
               </div>
                <div class="payment-logos" style="font-family:'FontAwesome';text-align:center;font-size:30px;">
                    <i class="fab fa-cc-visa" style="color:#1434cb;"></i>
                    <i class="fab fa-cc-mastercard" style="color:#f14f1b;"></i>
                    <i class="fab fa-cc-amex" style="color:#016fd0;"></i>
                    <i class="fab fa-cc-discover" style="color:#ef7d00;"></i>
                </div>
            </div>
          </aside>
          @else
          <section class="col-lg-12 pt-2 pt-lg-4 pb-4 mb-3">
          <div class="pt-2 px-4 pr-lg-0 pl-xl-5">
          <div class="col-lg-12" data-aos="fade-up" data-aos-delay="200">
          <div class="font-size-md">{{ Helper::translation(2898,$translate,'') }}</div>
          </div>
          </div>
          </section>
          @endif
        </div>
      </div>
    </div>
@include('footer')
@include('script')
<!-- stripe code -->
@if(!empty($stripe_publish))
<script src="https://js.stripe.com/v3/"></script>
<script>
    $(function () {
        'use strict';
        $("#ifYes").hide();
        $("input[name='payment_method']").click(function () {

            if ($("#opt1-stripe").is(":checked")) {
                $("#ifYes").show();

                /* stripe code */

                var stripe = Stripe('{{ $stripe_publish }}');

                var elements = stripe.elements();

                var style = {
                    base: {
                        color: '#32325d',
                        lineHeight: '18px',
                        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                        fontSmoothing: 'antialiased',
                        fontSize: '14px',
                        '::placeholder': {
                            color: '#aab7c4'
                        }
                    },
                    invalid: {
                        color: '#fa755a',
                        iconColor: '#fa755a'
                    }
                };


                var card = elements.create('card', {style: style, hidePostalCode: true});


                card.mount('#card-element');


                card.addEventListener('change', function(event) {
                    var displayError = document.getElementById('card-errors');
                    if (event.error) {
                        displayError.textContent = event.error.message;
                    } else {
                        displayError.textContent = '';
                    }
                });


                var form = document.getElementById('stripe_payment_submit');
                form.addEventListener('click', function(event) {
                    /*event.preventDefault();*/
                    if ($("#opt1-stripe").is(":checked")) { event.preventDefault(); }
                    stripe.createToken(card).then(function(result) {

                        if (result.error) {

                            var errorElement = document.getElementById('card-errors');
                            errorElement.textContent = result.error.message;


                        } else {

                            // Send the token to your server.
                            stripeTokenHandler(result.token);

                            document.querySelector('.token').value = result.token.id;

                            document.getElementById('checkout_form').submit();


                        }
                        /*document.querySelector('.token').value = result.token.id;

                            document.getElementById('checkout_form').submit();*/

                    });
                });


                /* stripe code */

                function stripeTokenHandler(token) {
                    // Insert the token ID into the form so it gets submitted to the server
                    var form = document.getElementById('checkout_form');
                    var hiddenInput = document.createElement('input');
                    hiddenInput.setAttribute('type', 'hidden');
                    hiddenInput.setAttribute('name', 'stripeToken');
                    hiddenInput.setAttribute('value', token.id);
                    form.appendChild(hiddenInput);

                    // Submit the form
                    form.submit();
                }



            } else {
                $("#ifYes").hide();
            }
        });
    });


</script>
@endif
<!-- stripe code -->
</body>
</html>
@else
@include('503')
@endif
