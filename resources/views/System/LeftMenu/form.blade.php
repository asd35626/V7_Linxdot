
<div class="md-card">
    <div class="md-card-content large-padding">
        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-2">
                {!! $formFields['ParentFunctionId']['completeField']  !!}
            </div>
        </div>
        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-2">
                {!! $formFields['FunctionName']['completeField']  !!}
            </div>
            <div class="uk-width-medium-1-2">
                {!! $formFields['FunctionCode']['completeField']  !!}
            </div>
        </div>
        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-4">
                {!! $formFields['MenuOrder']['completeField']  !!}
            </div>
            <div class="uk-width-medium-1-2"></div>
        </div>
        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-2-2">
                {!! $formFields['FunctionDesc']['completeField'] !!}
            </div>
        </div>
        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-2-2">
                {!! $formFields['FunctionURL']['completeField'] !!}
            </div>
        </div>
        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-3">
                 {!! $formFields['IfValid']['completeField']  !!}
            </div>
            <div class="uk-width-medium-1-3">
                <div class="parsley-row">
                    <label for="CreateBy">資料建立者
                        <span class="req">*</span>
                    </label>
                    {!! Form::text('CreateBy', null, array('id' => 'CreateBy', 'placeholder' =>'系統自動產生', 'class' => 'md-input label-fixed')) !!}
                </div>
            </div>
            <div class="uk-width-medium-1-3">
                <div class="parsley-row">
                    <label for="UserTypeName">資料建立時間
                        <span class="req">*</span>
                    </label>
                    {!! Form::text('CreateDate', null, array('id' => 'CreateDate', 'placeholder' =>'系統自動產生', 'class' => 'md-input label-fixed')) !!}
                </div>
            </div>
        </div>
        <div class="uk-grid">
            <div class="uk-width-1-1">
                <button type="submit" class="md-btn md-btn-primary" onclick="this.disabled='true'; this.form.submit();">送出</button>
                <button type="button" class="md-btn md-btn-warning" onclick="resetForm();">返回</button>
            </div>
        </div>
    </div>
</div>