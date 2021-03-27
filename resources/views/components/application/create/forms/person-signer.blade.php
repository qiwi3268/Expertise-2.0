<div class="form-card__subtitle">
   <div class="form-card__label">Лицо, подписывающее заявление</div>
</div>

<x-forms.inputs.name
    title="Фамилия"
    name="signerLastName"
    required="true"
/>
<x-forms.inputs.name
    title="Имя"
    name="signerFirstName"
    required="true"
/>
<x-forms.inputs.name
    title="Отчество"
    name="signerMiddleName"
    required="false"
/>
@include('components.application.create.forms.credentials')
