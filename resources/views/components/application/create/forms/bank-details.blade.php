<div class="form-card__subtitle">
   <div class="form-card__label">Банковские реквизиты</div>
</div>
<x-forms.inputs.dadata.bik
    title="БИК"
    name="bik"
    required="true"
/>
<x-forms.inputs.input
    title="Отделение банка"
    name="bankBranch"
    required="true"
/>
<x-forms.inputs.checkingAccount
    title="Расчетный счет"
    name="checkingAccount"
    required="true"
/>
<x-forms.inputs.correspondentAccount
    title="Корреспондентский/Казначейский счет"
    name="correspondentAccount"
    required="true"
/>
<x-forms.inputs.text-area
    title="Лицевой счет"
    name="businessAccount"
    required="false"
    maxLength="1000"
/>
