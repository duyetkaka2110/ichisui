@extends("layouts.layout")
@section('title', '工事検索')
@section("css")
<link href="/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<link href="/css/work.css" rel="stylesheet">
@endsection

@section("js")
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment-with-locales.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/ja.js" type="text/javascript"></script>
<script src="/js/bootstrap-datetimepicker.min.js"></script>
<script src="/js/work.js"></script>
@endsection

@section("content")
<main class=" p-3">
    <form action="" method="POST" class="formsubmit">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
        <div class="main-header pt-3">
            <div class="m-title">
                <h4 class="pb-2">工事検索</h4>
            </div>
            <div class="m-header w-100 p-2 mb-3 mt-3 pt-3 form-search position-relative">
                <div class="collapsetitle position-absolute m-title" data-toggle="collapse" href="#collapseSearch" role="button" aria-expanded="false" aria-controls="collapseSearch">
                    <i class="fa fa-plus-square-o plus" aria-hidden="true"></i><i class="fa fa-minus-square-o minus" aria-hidden="true"></i> 検索条件
                </div>
                <div class="w-100 collapse show " id="collapseSearch">
                    <table class="w-100">
                        <tbody>
                            <tr>
                                <td>
                                    <label class="m-0">受付No</label>
                                    <input name="WWID" type="number" min=0 class="w-6em form-control d-inline txtsearch p-0 WWID" value="{{ @$datasearch['WWID'] }}" />
                                </td>
                                <td colspan="2" class="mg-2date">
                                    <label class="m-0">受付日</label>
                                    <div class="input-group input-group-sm mg-date datetimepicker ">
                                        <input type="text" autocomplete="off" class="form-control WWDateTimeFrom datetimepicker txtsearch" name="WWDateTimeFrom" value="{{@$datasearch['WWDateTimeFrom'] }}">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                    ～
                                    <div class="input-group input-group-sm mg-date datetimepicker ">
                                        <input type="text" autocomplete="off" class="form-control datetimepicker WWDateTimeTo txtsearch" name="WWDateTimeTo" value="{{@$datasearch['WWDateTimeTo'] }}">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <label class="m-0">工事区分</label>
                                    <select name="WWReceptType" class="form-control p-0  w-7em d-inline txtsearch p-0 WWReceptType">
                                        <option value=""></option>
                                        @foreach($ListType as $k=>$v)
                                        <option <?php if (isset($datasearch['WWReceptType']) && $datasearch['WWReceptType'] == $v->InternalValue) echo 'selected'; ?> value="{{ @$v->InternalValue }}">{{ $v->DispText }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="w-9em" rowspan="4">
                                    <button type="submit" value="btnSearch" name="btnSearch" class="btn btn-primary w-100 p-0 btn-mater-search mb-3">検索</button>
                                    <button type="button" class="btn btn-primary w-100 p-0  btn-clear mb-3">クリア</button>
                                    <button type="button" name="Export" value="Export" class="btn btn-primary w-100 p-0 btn-Export mb-3">Excel出力</button>
                                    <button type="button" value="btnExcelHokoku" name="btnExcelHokoku" class="btn btn-primary w-100 p-0 btn-excel btnExcelHokoku mb-3">修繕報告書出力</button>
                                    <button type="button" value="btnExcelUchiwake" name="btnExcelUchiwake" class="btn btn-primary w-100 p-0 btn-excel btnExcelUchiwake ">修繕内訳書出力</button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="m-0">扱者</label>
                                    <input name="WWHandlerID" class="w-6em form-control  txtsearch p-0 WWHandlerID" value="{{@$datasearch['WWHandlerID']}}" />
                                </td>
                                <td>
                                    <label class="m-0">工事先氏名</label>
                                    <input name="ReqName" class="w-6em form-control d-inline txtsearch p-0 ReqName" value="{{ @$datasearch['ReqName'] }}" />
                                </td>
                                <td colspan="2">
                                    <label class="m-0">工事先住所</label>
                                    <input name="ReqAdress" class="w-6em form-control d-inline txtsearch p-0 ReqAdress" value="{{ @$datasearch['ReqAdress']  }}" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="m-0">診断番号</label>
                                    <input name="WWReceptNo" class="w-6em form-control d-inline txtsearch p-0 WWReceptNo" value="{{ @$datasearch['WWReceptNo']  }}" />
                                </td>
                                <td>
                                    <label class="m-0">作業担当者氏名</label>
                                    <input name="WorkUserNM" class="w-6em form-control d-inline txtsearch p-0 WorkUserNM" value="{{ @$datasearch['WorkUserNM']  }}" />
                                </td>
                                <td>
                                    <label class="m-0">作業区分</label>
                                    <select name="WorkType" class="form-control p-0  w-7em d-inline txtsearch p-0 WorkType">
                                        <option value=""></option>
                                        @foreach($ListWorkType as $k=>$v)
                                        <option <?php if (isset($datasearch['WorkType']) && $datasearch['WorkType'] == $v->InternalValue) echo 'selected'; ?> value="{{ @$v->InternalValue }}">{{ $v->DispText }}</option>
                                        @endforeach

                                    </select>
                                </td>
                                <td class="mg-2radio">
                                    <label class="m-0">作業状況</label>
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        @foreach($ListWorkStatus as $k=>$v)
                                        <?php
                                        $selected = null;
                                        if (isset($datasearch['WorkStatus']) && $datasearch['WorkStatus'] == $v->InternalValue)  $selected = 'checked';
                                        if (!isset($datasearch['WorkStatus']) && $k == 0)  $selected = 'checked';
                                        ?>
                                        <label class="btn btn-info p-0 WorkStatus WorkStatus{{ $k }} {{  $selected ? 'active' : '' }}">
                                            <input type="radio" {{ $selected }} name="WorkStatus" value="{{ @$v->InternalValue }}" autocomplete="off"> {{ $v->DispText }}
                                        </label>
                                        @endforeach
                                        <?php
                                        $selected = null;
                                        if (isset($datasearch['WorkStatus']) && $datasearch['WorkStatus'] == 'all')  $selected = 'checked';
                                        ?>
                                        <label class="btn btn-info p-0 WorkStatus WorkStatus3 {{  $selected ? 'active' : '' }}">
                                            <input type="radio" name="WorkStatus" value="all" {{  $selected ? 'checked' : '' }} autocomplete="off"> 全て
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="m-0">入金状況</label>
                                    <select name="PaymentStatus" class="form-control p-0  w-7em d-inline txtsearch p-0 PaymentStatus">
                                        <option value=""></option>
                                        @foreach($ListPaymentStatus as $k=>$v)
                                        <option <?php if (isset($datasearch['PaymentStatus']) && $datasearch['PaymentStatus'] == $v->InternalValue) echo 'selected'; ?> value="{{ @$v->InternalValue }}">{{ $v->DispText }}</option>
                                        @endforeach

                                    </select>
                                </td>
                                <td colspan="2" class="mg-2date">
                                    <label class="m-0">作業日</label>
                                    <div class="input-group input-group-sm mg-date datetimepicker ">
                                        <input type="text" autocomplete="off" class="form-control datetimepicker WorkTimeFrom txtsearch" name="WorkTimeFrom" value="{{@$datasearch['WorkTimeFrom'] }}">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                    ～
                                    <div class="input-group input-group-sm mg-date datetimepicker ">
                                        <input type="text" autocomplete="off" class="form-control datetimepicker WorkTimeTo txtsearch" name="WorkTimeTo" value="{{@$datasearch['WorkTimeTo'] }}">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="main-content">
            
        <div class="mg-pagination">
                {{ !$first ? $list->links("vendor.pagination.bootstrap-4-submit") : "" }}
            </div>
            <div>
                <label id="ResultSearchCount">検索結果：<span>{{ !$first ? $list->total() : "0"}}</span>件</label>
            </div>
            <table class="table table-order table-work">
                <thead class="bg-light">
                    <tr>
                        <th scope="col" class="w-5em text-center">No.</th>
                        <th scope="col" class="w-10em text-center">受付</th>
                        <th scope="col" class="w-17em text-center">工事先</th>
                        <th scope="col" class="w-17em text-center">作業先</th>
                        <th scope="col" class="w-15em text-center">作業1</th>
                        <th scope="col" class="w-15em text-center">作業2</th>
                        <th scope="col" class="w-15em text-center">作業3</th>
                        <th scope="col" class="w-7em text-center">請求</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!$first)
                    @if($list->total())
                    @foreach($list as $k=>$l)
                    <?php $trovan = "tr-ovan";
                    if ($k % 2 == 0)  $trovan = ""; ?>
                    <tr class="{{$trovan}}">
                        <td rowspan="5" class="text-center"><a href="/matterinput?idww={{ $l->WWID }}">{{ $l->WWID }}</a></td>
                    </tr>
                    <tr class="{{$trovan}}">
                        <td>{{ $l->WWDateTime }}</td>
                        <td>{{ $l->ReqName }}</td>
                        <td>{{ $l->ConstrName == $l->ReqName ? '':  $l->ConstrName}}</td>
                        <td>{{ $l->WORKFrom1 }}</td>
                        <td>{{ $l->WORKFrom2 }}</td>
                        <td>{{ $l->WORKFrom3 }}</td>
                        <td title="請求日" class="w-7em text-left">{{ $l->ClaimDate }}</td>
                    </tr>
                    <tr class="{{$trovan}}">
                        <td>{{ $l->WWTypeNM }}</td>
                        <td>{{ $l->ReqAdress }}</td>
                        <td>{{ $l->ConstrAdress == $l->ReqAdress ? '' : $l->ConstrAdress }}</td>
                        <td>{{ $l->time1 }}</td>
                        <td>{{ $l->time2 }}</td>
                        <td>{{ $l->time3 }}</td>
                        <td title="入金日" class="w-7em text-left">{{ $l->PaymentDate }}</td>
                    </tr>
                    <tr class="{{$trovan}}">
                        <td>{{ $l->WWReceptTypeNM }}</td>
                        <td>{{ $l->ReqBuilding }}</td>
                        <td>{{ $l->ReqBuilding == $l->ConstrBuilding ? '' : $l->ConstrBuilding }}</td>
                        <td>{{ Helper::getUserNM($l->UserNMs1) }}</td>
                        <td>{{ Helper::getUserNM($l->UserNMs2) }}</td>
                        <td>{{ Helper::getUserNM($l->UserNMs3) }}</td>
                        <td class="w-7em ">{{ $l->PaymentStatusNM }}</td>
                    </tr>
                    <tr class="{{$trovan}}">
                        <td>{{ $l->WWHandlerIDNM }}</td>
                        <td>{{ $l->ReqTEL ? $l->ReqTEL : $l->ReqContactTEL  }}</td>
                        <td>{{ $l->ConstrTEL == $l->ReqTEL ? '' :  $l->ConstrTEL  }}</td>
                        <td>{{ $l->WorkTypeNM1 }}</td>
                        <td>{{ $l->WorkTypeNM2 }}</td>
                        <td>{{ $l->WorkTypeNM3 }}</td>
                        <td class="w-7em ">{{ $l->ClaimTypeNM }}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="9" class="text-center">対象データがありません。</td>
                    </tr>
                    @endif
                    @endif
                </tbody>
            </table>
            <div class="mg-pagination">
                {{ !$first ? $list->links("vendor.pagination.bootstrap-4-submit") : "" }}
            </div>
        </div>
    </form>
</main>
@if($ErrMsg)

<script>
    $(document).ready(function() {
        $("#MessageModal .modal-body").html('<?php echo trim($ErrMsg) ?>');
        $("#MessageModal").modal();
    })
</script>
@endif
@endsection