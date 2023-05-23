@extends('layoutsphone.master', ['title' => '工事管理/資材管理システム'])
@section('header')
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@stop
@section('content')
<style>
    .login-margin input {
        padding: inherit;
        height: inherit;
    }

    .login-margin button {
        width: 100%;
        padding: 7px;
    }

    .login-margin .input-group-text i {
        width: 1em;
    }
</style>
<section>
    <form method="POST" action="/loginphone">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
        <div class="login-margin mt-5">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user" aria-hidden="true"></i></span>
                </div>
                <input type="text" value="{{ old('UserID') }}" class="form-control" name="UserID" placeholder="ユーザーID" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon2"><i class="fa fa-key" aria-hidden="true"></i></span>
                </div>
                <input type="password" name="PassWord" value="" class="form-control" placeholder="パスワード" aria-label="PassWord" aria-describedby="basic-addon2">
            </div>
            <div class="input-group mb-3">
                <button name="BtnLogin" class="btn btn-primary m-0">ログイン</button>
            </div>
        </div>

    </form>
</section> 
@if(($errors->any()))
    <!-- Modal Error Message -->
    <div class="modal fade" id="MessageModal" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header modal-header-error bg-danger">
                    <h5 class="modal-title text-white">エラーメッセージ</h5>
                </div>
                <div class="modal-body">
                    @foreach ($errors->all() as $error)
                    <p>{!! $error !!}</p>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger close-modal" data-dismiss="modal">閉じる</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $("#MessageModal").modal();
        });
    </script>
    @endif
@stop