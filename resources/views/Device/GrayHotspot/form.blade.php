<div class="md-card">
    <div class="md-card-content large-padding">
        <h3 class="heading_a" style="padding-left: 0px;">Hotspot Info.</h3>
        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-3">
                {!! $formFields['DeviceSN']['completeField']  !!}
            </div>
            <div class="uk-width-medium-1-3">
                {!! $formFields['MacAddress']['completeField']  !!}
            </div>
            <div class="uk-width-medium-1-3">
                {!! $formFields['AnimalName']['completeField']  !!}
            </div>
        </div>
        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-1">
                {!! $formFields['OnBoardingKey']['completeField']  !!}
            </div>
        </div>
        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-3">
                {!! $formFields['WifiMac']['completeField']  !!}
            </div>
            <div class="uk-width-medium-1-3">
                {!! $formFields['MgrVersion']['completeField']  !!}
            </div>
        </div>
        <div class="uk-grid " data-uk-grid-margin>
            <div class="uk-width-medium-1-3">
                {!! $formFields['MinerVersion']['completeField']  !!}
            </div>
            <div class="uk-width-medium-1-3">
                {!! $formFields['Firmware']['completeField']  !!}
            </div>
            <div class="uk-width-medium-1-3">
                {!! $formFields['Region']['completeField']  !!}
            </div>
        </div>
        <h3 class="heading_a"style="padding-left: 0px;">Memo</h3>
        <div class="uk-grid " data-uk-grid-margin>
            <div class="uk-width-medium-1-3">
                {!! $formFields['IsFixed']['completeField']  !!}
            </div>
            <div class="uk-width-medium-1-3">
                {!! $formFields['HotspoType']['completeField']  !!}
            </div>
        </div>
        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-1">
                {!! $formFields['GrayMemo']['completeField']  !!}
            </div>
        </div>
        <div class="uk-grid " data-uk-grid-margin>
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
            