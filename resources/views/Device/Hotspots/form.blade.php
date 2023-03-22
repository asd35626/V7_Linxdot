<div class="md-card">
    <div class="md-card-content large-padding">
        <h3 class="heading_a" style="padding-left: 0px;">Device Information</h3>
        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-3">
                <!-- Online Status -->
                {!! $formFields['DeviceSN']['completeField']  !!}
            </div>
            <div class="uk-width-medium-1-3">
                {!! $formFields['MacAddress']['completeField']  !!}
            </div>
            @if($Action == 'EDIT')
                <div class="uk-width-medium-1-3">
                    {!! $formFields['WifiMacAddress']['completeField']  !!}
                </div>
            @endif
        </div>
        @if($Action == 'EDIT')
            <div class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-medium-1-3">
                    {!! $formFields['ModelName']['completeField']  !!}
                </div>
                <div class="uk-width-medium-2-3">
                    {!! $formFields['DeviceID']['completeField']  !!}
                </div>
            </div>
            <div class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-medium-1-3">
                    {!! $formFields['Firmware']['completeField']  !!}
                </div>
                <div class="uk-width-medium-1-3">
                    {!! $formFields['OwnerID']['completeField']  !!}
                </div>
            </div>

        @endif
            
        <h3 class="heading_a" style="padding-left: 0px;">Helium Information</h3>
        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-3">
                {!! $formFields['AnimalName']['completeField']  !!}
            </div>
            @if($Action == 'NEW')
                <div class="uk-width-medium-1-3">
                    {!! $formFields['IsRegisteredDewi']['completeField']  !!}
                </div>
            @endif
            @if($Action == 'EDIT')
                <div class="uk-width-medium-2-3">
                    {!! $formFields['OnBoardingKey']['completeField']  !!}
                </div>
            @endif
        </div>
        @if($Action == 'EDIT')
            <div class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-medium-1-6">
                    {!! $formFields['DewiStatus']['completeField']  !!}
                </div>
                <div class="uk-width-medium-1-3">
                    {!! $formFields['IsRegisteredDewi']['completeField']  !!}
                </div>
                <div class="uk-width-medium-1-6">
                    @if($IsRegisteredDewi != 1)
                        <button type="button" onclick="RegisteredDewi('{!!$MacAddress!!}')" class="md-btn md-btn-primary">Dewi</button>
                    @endif
                </div>
                <div class="uk-width-medium-1-3">
                    {!! $formFields['LastRegisterDewiDate']['completeField']  !!}
                </div>
            </div>
            <div class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-medium-1-3">
                </div>
                @if($IsRegisteredDewi != 1)
                    <div class="uk-width-medium-2-3">
                        {!! $formFields['LastRegisterDewiMemo']['completeField']  !!}
                    </div>
                @endif
            </div>
            <div class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-medium-1-3">
                    {!! $formFields['MinerVersion']['completeField']  !!}
                </div>
                <div class="uk-width-medium-1-3">
                    {!! $formFields['map_lat']['completeField']  !!}
                </div>
                <div class="uk-width-medium-1-3">
                    {!! $formFields['map_lng']['completeField']  !!}
                </div>
            </div>
        @endif

        <h3 class="heading_a" style="padding-left: 0px;">Process Info</h3>
        @if($Action == 'EDIT')
            <div class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-medium-1-3">
                    {!! $formFields['FactoryDispatchDate']['completeField']  !!}
                </div>
                <div class="uk-width-medium-1-3">
                    {!! $formFields['IssueDate']['completeField']  !!}
                </div>
                <div class="uk-width-medium-1-3">
                    {!! $formFields['ShippedDate']['completeField']  !!}
                </div>
            </div>
        @endif
        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-3">
                {!! $formFields['PalletId']['completeField']  !!}
            </div>
            <div class="uk-width-medium-1-3">
                {!! $formFields['CartonId']['completeField']  !!}
            </div>
            <div class="uk-width-medium-1-3">
                {!! $formFields['IsVerify']['completeField']  !!}
            </div>
        </div>
        @if($Action == 'EDIT')
            <div class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-medium-1-3">
                    {!! $formFields['CustomInfo']['completeField']  !!}
                </div>
                <div class="uk-width-medium-1-3">
                    {!! $formFields['TrackingNo']['completeField']  !!}
                </div>
            </div>
            <div class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-medium-1-3">
                    {!! $formFields['OfficalNickName']['completeField']  !!}
                </div>
                <div class="uk-width-medium-1-3">
                    {!! $formFields['NickName']['completeField']  !!}
                </div>
            </div>
        @endif

        <br>
        <div class="uk-grid " data-uk-grid-margin>
            <div class="uk-width-medium-1-3">
                {!! $formFields['IfValid']['completeField']  !!}
            </div>
            <div class="uk-width-medium-1-3">
                {!! $formFields['CreateBy']['completeField']  !!}
            </div>
            <div class="uk-width-medium-1-3">
                {!! $formFields['CreateDate']['completeField']  !!}
            </div>
        </div>

        <div class="uk-grid">
            <div class="uk-width-1-1">
                @if($Action == 'EDIT' || $Action == 'NEW')
                    <button type="submit" class="md-btn md-btn-primary" onclick="this.disabled='true'; this.form.submit();">OK</button>
                @endif
                <button type="button" class="md-btn md-btn-warning" onclick="resetForm();">BACK</button>
            </div>
        </div>
    </div>
</div>
            