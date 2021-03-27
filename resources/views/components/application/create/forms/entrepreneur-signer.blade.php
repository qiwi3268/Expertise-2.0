<div class="form-card__subtitle">
   <div class="form-card__label">Подписант</div>
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
<x-forms.inputs.input
    title="Должность"
    name="signerPost"
    required="true"
/>
@include('components.application.create.forms.credentials')
