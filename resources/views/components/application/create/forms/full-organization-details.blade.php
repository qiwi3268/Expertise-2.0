<div class="form-card__subtitle">
   <div class="form-card__label">Сведения о юридическом лице</div>
</div>

@include('components.application.create.forms.main-organization-details')

<div class="form-card__subtitle">
   <div class="form-card__label">Руководитель</div>
</div>

<x-forms.inputs.name
    title="Фамилия"
    name="directorLastName"
    required="true"
/>
<x-forms.inputs.name
    title="Имя"
    name="directorFirstName"
    required="true"
/>
<x-forms.inputs.name
    title="Отчество"
    name="directorMiddleName"
    required="false"
/>
<x-forms.inputs.input
    title="Должность"
    name="directorPost"
    required="true"
/>
