<div class="form-card__subtitle">
   <div class="form-card__label">Сведения об индивидуальном предпринимателе</div>
</div>

<x-forms.inputs.dadata.persInn
    title="ИНН"
    name="persInn"
    required="true"
/>
<x-forms.inputs.name
    title="Фамилия"
    name="lastName"
    required="true"
/>
<x-forms.inputs.name
    title="Имя"
    name="firstName"
    required="true"
/>
<x-forms.inputs.name
    title="Отчество"
    name="middleName"
    required="false"
/>
<x-forms.inputs.passport
    title="Серия и номер паспорта"
    name="passport"
    required="true"
/>
<x-forms.inputs.text-area
    title="Кем выдан"
    name="passportIssuer"
    required="true"
    maxLength="1000"
/>
<x-forms.date
    title="Когда"
    name="passportIssueDate"
    required="true"
    interval="-1"
/>
<x-forms.inputs.snils
    title="СНИЛС"
    name="snils"
    required="true"
/>
<x-forms.inputs.ogrnip
    title="ОГРНИП"
    name="ogrnip"
    required="true"
/>
<x-forms.inputs.email
    title="Адрес электронной почты"
    name="email"
    required="true"
/>
<x-forms.inputs.phone
    title="Телефон"
    name="phone"
    required="true"
/>

<div class="form-card__subtitle">
   <div class="form-card__label">Адрес индивидуального предпринимателя</div>
</div>

@include('components.application.create.forms.post-address')
