<div class="collapsetitle pb-3" id="requester" data-toggle="collapse" href="#collapseRequester" aria-expanded="false" aria-controls="collapseRequester">
    <i class="fa fa-minus-square-o minus" aria-hidden="true"></i><i class="fa fa-plus-square-o plus" aria-hidden="true"></i>【工事先】
</div>
<div class="collapse show" id="collapseRequester">
    <div class="card requester-body">
        <table>
            <tr>
                <td class="lbl">住所</td>
                <td>
                    <div class="input-group input-group-sm longtext">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
                        </div>
                        <input type="text" class="form-control" name="ReqAdress" value="{{old('ReqAdress',@$WaterWork->ReqAdress) }}" maxlength="255" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    </div>
                </td>
            </tr>
            <tr>
                <td class="lbl">建物名</td>
                <td>
                    <div class="input-group input-group-sm longtext">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-building" aria-hidden="true"></i></span>
                        </div>
                        <input type="text" class="form-control" name="ReqBuilding" value="{{old('ReqBuilding',@$WaterWork->ReqBuilding) }}" maxlength="255" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    </div>
                </td>
            </tr>

            <tr>
                <td class="lbl">氏名</td>
                <td>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-id-card-o" aria-hidden="true"></i></span>
                        </div>
                        <input type="text" class="form-control ReqName" id="ReqName" name="ReqName" value="{{old('ReqName',@$WaterWork->ReqName) }}" maxlength="64" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    </div>
                </td>
            </tr>
            <tr>
                <td class="lbl">氏名カナ</td>
                <td>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-id-card-o" aria-hidden="true"></i></span>
                        </div>
                        <input type="text" class="form-control ReqNameKana" id="ReqNameKana" name="ReqNameKana" value="{{old('ReqNameKana',@$WaterWork->ReqNameKana) }}" maxlength="128"  aria-describedby="inputGroup-sizing-sm">
                    </div>
                </td>
            </tr>
            <tr>
                <td class="lbl">電話(1)</td>
                <td>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-phone" aria-hidden="true"></i></span>
                        </div>
                        <input type="text" class="form-control" name="ReqTEL" value="{{old('ReqTEL',@$WaterWork->ReqTEL) }}"  maxlength="64" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    </div>
                </td>
            </tr>
            <tr>
                <td class="lbl">電話(2)</td>
                <td>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-phone" aria-hidden="true"></i></span>
                        </div>
                        <input type="text" class="form-control" name="ReqContactTEL" value="{{old('ReqContactTEL',@$WaterWork->ReqContactTEL) }}" maxlength="64" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    </div>
                </td>
            </tr>
            <tr>
                <td class="lbl">お客様番号（栓番）</td>
                <td>
                    <div class="input-group input-group-sm">
                        <!-- <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-phone" aria-hidden="true"></i></span>
                        </div> -->
                        <input type="text" class="form-control" name="ReqWaterNo" value="{{old('ReqWaterNo',@$WaterWork->ReqWaterNo) }}" maxlength="10" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    </div>
                </td>
            </tr>
            <!-- <tr>
                <td class="lbl">栓番</td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" name="PlugNum" value="{{old('PlugNum',@$WaterWork->PlugNum) }}" maxlength="64" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    </div>
                </td>
            </tr> -->
            <tr>
                <td class="lbl">口径</td>
                <td>
                    <div class="input-group input-group-sm">
                        <!-- <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-circle-o" aria-hidden="true"></i></span>
                        </div> -->
                        <input type="text" class="form-control" name="PipeSize" value="{{old('PipeSize',@$WaterWork->PipeSize) }}" maxlength="64" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>