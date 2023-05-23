<div class="collapsetitle pb-3" id="photo" data-toggle="collapse" href="#collapsePhoto" aria-expanded="false" aria-controls="collapsePhoto">
    <i class="fa fa-minus-square-o minus" aria-hidden="true"></i><i class="fa fa-plus-square-o plus" aria-hidden="true"></i>【写真】
</div>
<div class="collapse show" id="collapsePhoto">
    <div class="card photo-body">
        <table class="table" style="width: 850px;" id="photoTable">
            <thead>
                <tr>
                    <th class="text-center w-12em">ファイル</th>
                    <th class="text-center">概要</th>
                    <th class="text-center w-8em">削除</th>
                </tr>
            </thead>
            <tbody>
                @foreach($WorkImages as $img)
                <tr>
                    <td><img class="showimg" alt="" src="{{ $img->FilePath }}" /></td>
                    <td>{{ $img->Note }}</td>
                    <td><i class="fa fa-trash btnphotoDelete" aria-hidden="true" imgid="{{ $img->ImgID }}"></i></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <button type="button" name="btn" value="save" class="materialselected btn btn-primary" style="margin-left: 63%;" id="btnphotoAdd">新規追加</button>
    </div>
</div>
<!-- Modal Image -->
<div class="modal fade" id="ImageModal" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-error bg-primary p-2 text-center">
                <h5 class="modal-title text-white pl-2">新規写真追加画面</h5>
            </div>
            <div class="modal-body">
                <div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text w-5em"><i class="fa fa-address-book" aria-hidden="true"></i> 概要</span>
                        </div>
                        <input type="text" class="form-control txtSummaryPopup">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text w-5em"><i class="fa fa-file-image-o" aria-hidden="true"></i> 写真</span>
                        </div>
                        <div class="custom-file">
                            <input type="file" name="img[1]" class="custom-file-input fileimage" accept="image/*">
                            <label class="custom-file-label browse">選択されていません</label>
                        </div>
                    </div>
                    <div class="text-center" id="preview">

                    </div>
                </div>
            </div>
            <div class="modal-footer text-center">
                <button type="button" class="btn btn-primary btnOk w-6em">決定</button>
                <button type="button" class="btn btn-danger close-modal w-6em" data-dismiss="modal">閉じる</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Show Image -->
<div class="modal fade" id="ShowImageModal" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-error bg-primary p-2 text-center">
                <h5 class="modal-title text-white pl-2">写真画面</h5>
            </div>
            <div class="modal-body text-center">
            </div>
            <div class="modal-footer text-center">
                <button type="button" class="btn btn-danger close-modal w-6em" data-dismiss="modal">閉じる</button>
            </div>
        </div>
    </div>
</div>