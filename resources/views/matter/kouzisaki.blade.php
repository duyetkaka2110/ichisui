<div class="collapsetitle pb-3" id="construction" data-toggle="collapse" href="#collapseConstruction" aria-expanded="false" aria-controls="collapseConstruction">
    <i class="fa fa-minus-square-o minus" aria-hidden="true"></i><i class="fa fa-plus-square-o plus" aria-hidden="true"></i>【依頼者】
</div>
<div class="collapse show" id="collapseConstruction">
    <div class="card construction-body">
        <table>
            <!-- 工事先table -->
            <tr>
                <td colspan="2">
                    <?php
                    if ($WaterWork) {
                        if ($WaterWork->ReqAdress == $WaterWork->ConstrAdress && $WaterWork->ReqName == $WaterWork->ConstrName && $WaterWork->ReqBuilding == $WaterWork->ConstrBuilding && $WaterWork->ReqTEL == $WaterWork->ConstrTEL) {
                            $ConstructionCopyChecked = "checked";
                            $ConstructionDisabled = "disabled";
                        } else {
                            $ConstructionCopyChecked = "";
                            $ConstructionDisabled = "";
                        }
                    } else {
                        $ConstructionCopyChecked = "checked";
                        $ConstructionDisabled = "disabled";
                    }
                    ?>
                    <label>
                        <input type="checkbox" {{$ConstructionCopyChecked}} id="ConstructionCopyCheck" name="ConstructionCopyCheck" value="1">工事先に同じ</input>
                    </label>
                </td>
            </tr>

            <tr>
                <td class="lbl">住所</td>
                <td>
                    <div class="input-group input-group-sm longtext">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
                        </div>
                        <input type="text" {{$ConstructionDisabled}} class="form-control" name="ConstrAdress" maxlength="255" value="{{old('ConstrAdress',@$WaterWork->ConstrAdress) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    </div>
                </td>
            </tr>
            <tr>
                <td class="lbl">氏名</td>
                <td>
                    <div class="input-group input-group-sm longtext">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-id-card-o" aria-hidden="true"></i></span>
                        </div>
                        <input type="text" {{$ConstructionDisabled}} class="form-control" name="ConstrName" maxlength="64" value="{{old('ConstrName',@$WaterWork->ConstrName) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
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
                        <input type="text" {{$ConstructionDisabled}} class="form-control" name="ConstrBuilding" maxlength="255" value="{{old('ConstrBuilding',@$WaterWork->ConstrBuilding) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    </div>
                </td>
            </tr>
            <tr>
                <td class="lbl">電話番号</td>
                <td>
                    <div class="input-group input-group-sm float-left">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-phone" aria-hidden="true"></i></span>
                        </div>
                        <input type="text" {{$ConstructionDisabled}} class="form-control" name="ConstrTEL" maxlength="64" value="{{old('ConstrTEL',@$WaterWork->ConstrTEL) }}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    </div>
                    
                    <div class="custom-control custom-switch float-left mt-1 ml-4">
                        <?php
                        $checked = "";
                        if ($WaterWork) {
                            if ($WaterWork->WTelFlg == 1 || !$WaterWork->WWName) {
                                $checked = "checked";
                            }
                        } 
                        ?>
                        <input type="checkbox" class="custom-control-input" id="customSwitch1" {{$checked}} name="WTelFlg" value="1" {{old('WTelFlg',@$WaterWork->WTelFlg)}}>
                        <label class="custom-control-label" for="customSwitch1">電話連絡要</label>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>