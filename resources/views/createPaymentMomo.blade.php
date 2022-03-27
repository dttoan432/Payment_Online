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
        <h3 class="text-muted">THANH TOÁN MOMO</h3>
    </div>
    <h3>Tạo mới đơn hàng</h3>
    <div class="table-responsive">
        <form action="{{ route('storeMomo') }}" id="create_form" method="post">
            @csrf
            <div class="form-group">
                <label for="order_id">Mã hóa đơn</label>
                <input class="form-control" id="order_id" name="order_id" type="text" value="<?php echo date("YmdHis") ?>"/>
            </div>
            <div class="form-group">
                <label for="amount">Số tiền</label>
                <input class="form-control" id="amount"
                       name="amount" type="number" value="10000"/>
            </div>
            <div class="form-group">
                <label for="order_desc">Nội dung thanh toán</label>
                <textarea class="form-control" cols="20" id="order_desc" name="order_info" rows="2">Thanh toán Momo</textarea>
            </div>
            <div class="form-group">
                <label for="language">Ngôn ngữ</label>
                <select name="language" id="language" class="form-control">
                    <option value="vi">Tiếng Việt</option>
                    <option value="en">English</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" id="btnPopup">Thanh toán</button>
        </form>
    </div>
    <p>
        &nbsp;
    </p>
    <footer class="footer">
        <p>&copy; MOMO <?php echo date('Y')?></p>
    </footer>
</div>
</body>
</html>
