<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>付款</title>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport"
          content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, width=device-width">
    <link rel="stylesheet" href="{{ asset('/member/css/font-awesome.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('/member/css/style.css') }}"/>
</head>
<body class="pay" style="background-color: #F8F8F8;font-size:  0.4375rem">
<main class="content">
    <div class="express">
        <div class="container">
            @if($address)
                <div class="address item on">
                    <div class="address-panel">
                        <div class="address-detail" data-address_id="{{$address->id}}">
                            <div class="customer-info">
                                <span class="name">收&nbsp;货&nbsp;人&nbsp;：{{$address->name}}</span>
                            </div>
                            <div class="customer-info" style="margin-top: 0.1rem;">
                                <span class="tel">手&nbsp;机&nbsp;号&nbsp;：{{$address->phone}}</span>
                            </div>
                            <div class="address-detail-info">
                                <p>收货地址：{{$address->province.$address->city.$address->district.$address->address}}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="empty-address-tip item on">
                    添加收货地址
                </div>
            @endif
        </div>
    </div>
    <form action="/member/order/store" method="post" id="pay-form">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        @if($address)
            <input type="hidden" name="address_phone" value="{{$address->phone}}">
            <input type="hidden" name="address_name" value="{{$address->name}}">
            <input type="hidden" name="address_province" value="{{$address->province}}">
            <input type="hidden" name="address_city" value="{{$address->city}}">
            <input type="hidden" name="address_district" value="{{$address->district}}">
            <input type="hidden" name="address_detail" value="{{$address->address}}">
        @endif
        <div class="goods-list">
            <div class="header">
                <span class="shop">商品列表</span>
            </div>
            <div class="goods">
                @foreach($products as $product)
                    <input type="hidden"
                           name="product[{{sizeof($product->specification) ?$product->id.'-'.$product->specification->id :$product->id}}]"
                           value="{{$product->quantity}}"/>
                    <div class="cart-item">
                        <div class="cart-info">
                            <a href="/member/detail?id={{$product->id}}" class="preview">
                                <img src="{{$product->logo}}" alt="">
                            </a>

                            <div class="detail">
                                <a href="/member/detail?id={{$product->id}}" class="goods-link">
                                    <h3 class="goods-title">{{$product->name}}</h3>
                                </a>

                                <p class="goods-weight">{{sizeof($product->specification) ? $product->specification->specification_name : $product->default_spec}}</p>
                            </div>
                            <div class="count">
                                <p class="price">
                                    <span class="unit">&yen;</span>
                                    <span class="value">{{sizeof($product->specification) ?$product->specification->specification_price :$product->price}}</span>
                                </p>

                                <div class="num">
                                    <p class="value">{{$product->quantity}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="block-item price">
            <p>商品合计<span class="num rmb">&yen;{{$productFee}} &nbsp;&nbsp;&nbsp;<span
                            style="color: #00b7ee">[{{$productFee * 10}}糖豆]</span></span></p>

            <p>账号余额<span class="num rmb"
                         style="color: #f60;font-weight: bold">{{$beans}}</span></p>
        </div>
        <div class="confirm">
            <input type="hidden" name="address_id" class="selected_address"
                   value="">
            @if($beans > $productFee * 100)
                <button type="button" class="next" id="pay-weixin">确认支付</button>
            @else
                <button type="button" class="next" disabled
                        style="background-color: #eee;border: 0.03125rem solid #eee;">账号余额不足
                </button>
            @endif
        </div>
    </form>
</main>
<div class="mask-layer">
</div>
<div class="notify">
    <p class="notify-inner"></p>
</div>

<script type="text/javascript" src="/member/js/libs/jquery.min.js"></script>
<script type="text/javascript" src="/member/js/libs/flexible.js"></script>
<script type="text/javascript" src="/member/js/components.js"></script>

<script type="text/javascript">
    var $maskLayer = $('.mask-layer');
    function showMaskLayer(show) {
        return show ? $maskLayer.show() : $maskLayer.hide();
    }
    var $notify = $('.notify');
    function showNotify(text, timeout) {
        showMaskLayer(true);
        //$notify.fadeIn();
        $notify.find('.notify-inner').html(text);
        $notify.fadeIn();
        setTimeout(function () {
            $notify.fadeOut();
            showMaskLayer(false);
        }, timeout);
    }

    $('#pay-weixin').on('click', function () {
        // 地址验证
        var id = $('.address-detail').attr('data-address_id');
        if (!id) {
            showNotify('地址不能为空！', 3000);
            return false;
        }

        var r = confirm("确定用糖豆支付？");
        if (r) {
            var payButton = $('#pay-weixin');
            payButton.attr('disabled', "true");
            payButton.html("支付中...");

            $.post('/member/order/store',
                    $('#pay-form').serialize(),
                    function (data) {
                        if (data.success) {
                            console.log(JSON.stringify(data));
                            window.location.href = "/member/pay-success";
                        } else {
                            alert('服务器异常!');
                        }
                    }, "json"
            );
        } else {
            return false;
        }

    })
</script>
<script type="text/javascript" src="/member/js/main.js"></script>
</body>
</html>
