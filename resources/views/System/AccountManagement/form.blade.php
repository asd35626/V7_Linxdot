<div class="md-card">
    <div class="md-card-content large-padding">
        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-2">
                {!! $formFields['MemberNo']['completeField']  !!}
            </div>
            <div class="uk-width-medium-1-2">
                {{-- @if($Action == 'EDIT')                
                    <div class="md-btn md-btn-primary" onclick="reSendPassword('{{ $targetId }}')">重新產生登入密碼</div>
                @elseif($Action == 'NEW')
                    {!! $formFields['UserPassword']['completeField']  !!}    
                @endif --}}
                {!! $formFields['UserPassword']['completeField']  !!}
            </div>
        </div>
        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-2">
                {!! $formFields['RealName']['completeField']  !!}
            </div>
            {{-- <div class="uk-width-medium-1-2">
                {!! $formFields['Member']['completeField']  !!}
            </div> --}}
        </div>
        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-4">
                {!! $formFields['UserType']['completeField'] !!}
            </div>
            <div class="uk-width-medium-1-4">
                {!! $formFields['DegreeId']['completeField'] !!}
            </div>
            {{-- <div class="uk-width-medium-1-4" id="FromUID">
                {!! $formFields['FromUserID']['completeField'] !!}
            </div>
            <div class="uk-width-medium-1-4" id="FromUID2">
                {!! $formFields['FromUser']['completeField'] !!}
            </div> --}}
        </div>
        <div class="uk-grid " data-uk-grid-margin>
            <div class="uk-width-medium-1-4">
                 {!! $formFields['IfValid']['completeField']  !!}
            </div>
            <div class="uk-width-medium-1-4">
                {!! $formFields['CreateBy']['completeField']  !!}
            </div>
            <div class="uk-width-medium-1-4">
               {!! $formFields['CreateDate']['completeField']  !!}
            </div>
            <div class="uk-width-medium-1-4">
                {!! $formFields['LastLogin']['completeField']  !!}
            </div>
		</div>
        <div class="uk-grid">
            <div class="uk-width-1-1">
                @if($Action == 'EDIT' || $Action == 'NEW')
                    <button type="submit" class="md-btn md-btn-primary" onclick="this.disabled='true'; this.form.submit();">送出</button>
                @endif
                <button type="button" class="md-btn md-btn-warning" onclick="resetForm();">返回</button>
            </div>
        </div>
    </div>
</div>            