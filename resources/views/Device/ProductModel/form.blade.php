<div class="md-card">
    <div class="md-card-content large-padding">
        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-2">
                {!! $formFields['ModelNo']['completeField']  !!}
            </div>
            <div class="uk-width-medium-1-2">
                {!! $formFields['ModelName']['completeField']  !!}
            </div>
        </div>
        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-2">
                {!! $formFields['ModelSpec']['completeField']  !!}
            </div>
            <div class="uk-width-medium-1-2">
                {!! $formFields['ModelInfo']['completeField']  !!}
            </div>
        </div>

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
            