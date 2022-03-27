<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Tạo mới đơn hàng</title>
    <!-- Bootstrap core CSS -->
    <link href="/assets/bootstrap.min.css" rel="stylesheet"/>
    <!-- Custom styles for this template -->
    <link href="/assets/jumbotron-narrow.css" rel="stylesheet">
    <script src="/assets/jquery-1.11.3.min.js"></script>
</head>

<body>

<div class="container">
    <div class="header clearfix">
        <h3 class="text-muted">Danh sách đơn hàng <a href="{{ route('createMomo') }}" type="button" class="btn btn-primary">Tạo mới</a></h3>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">STT</th>
                <th scope="col">Mã hóa đơn</th>
                <th scope="col">Số tiền</th>
                <th scope="col">Nội dung thanh toán</th>
                <th scope="col">Trạng thái</th>
            </tr>
        </thead>
        <tbody>
        @foreach($order as $key => $item)
            <tr>
                <th scope="row">{{ $key+1 }}</th>
                <td>{{ $item->code }}</td>
                <td>{{ $item->money }}</td>
                <td>{{ $item->content }}</td>
                @if($item->status == \App\Models\PaymentMomo::STATUS['UNPAID'])
                <td>
                    Chờ xử lý
                </td>
                @endif
                @if($item->status == \App\Models\PaymentMomo::STATUS['SUCCESS'])
                <td>
                    Thanh toán thành công
                </td>
                @endif
                @if($item->status == \App\Models\PaymentMomo::STATUS['FAILURE'])
                <td>
                    Thanh toán thất bại
                </td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
    <p>
        &nbsp;
    </p>
    <footer class="footer">
        <p>&copy; MOMO <?php echo date('Y')?></p>
    </footer>
</div>
</body>
</html>
