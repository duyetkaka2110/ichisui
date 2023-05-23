<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>

<head>
    <title><?php if (isset($title)) echo $title;
            else echo  "工事管理/資材管理システム" ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ URL::asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/loginphone.css') }}">
    <!-- <script src="{{ URL::asset('js/index.js') }}"></script> -->
    <script src="{{ URL::asset('js/phone.js') }}"></script>
    @yield('header')
</head>

<body>

    <main>
        <div class="margin-body">
            <header>
                <h3 class="title-fixed m-0">工事管理/資材管理システム</h3>
            </header>
            @yield('content')
            <div class="clearleft"></div>
        </div>
    </main>
    <!-- Confirm Modal  -->
    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" data-backdrop="static" aria-hidden="true" id="ConfirmModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">メッセージ</h4>
                </div>
                <div class="modal-body">
                    <p></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary confirm close-modal" data-dismiss="modal">閉じる</button>
                </div>
            </div>
        </div>
    </div>
    @if (session('msg'))
    <script>
        $(document).ready(function() {
            
            $("#ConfirmModal .modal-body p").html(getMsgByID("{{session('msg')}}"));
            $("#ConfirmModal").modal();
        });
    </script>
    @endif
    <!-- Modal Error Message -->
    <div class="modal fade" id="MessageModal" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header modal-header-error">
                    <h4 class="modal-title">エラーメッセージ</h4>
                </div>
                <div class="modal-body">
                    <p><?php echo session('error') ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger close-modal" data-dismiss="modal">閉じる</button>
                </div>
            </div>
        </div>
    </div>
    @if (session('error'))
    <script>
        $(document).ready(function() {
            $("#MessageModal").modal();
        });
    </script>
    @endif
    <div class="loading">
        <div class="spinner-border text-light" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</body>

</html>