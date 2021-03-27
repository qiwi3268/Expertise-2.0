<div class="form-card__subtitle">
   <div class="form-card__label">Подписант</div>
</div>
<x-forms.toggle
    title="Руководитель"
    name="isSignerEqualsDirector"
    required="true"
    defaultValue="-1"
/>
<div class="template-part__display-block"
     data-display-block
     data-name="signerDetails"
>
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
</div>
@include('components.application.create.forms.credentials')
