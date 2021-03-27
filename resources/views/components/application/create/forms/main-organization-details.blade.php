<x-forms.inputs.dadata.orgInn
    title="ИНН"
    name="orgInn"
    required="true"
/>
<x-forms.inputs.text-area
    title="Полное наименование"
    name="fullName"
    required="true"
    maxLength="1000"
/>
<x-forms.inputs.input
    title="Краткое наименование"
    name="shortName"
    required="true"
/>
<x-forms.inputs.ogrn
    title="ОГРН"
    name="ogrn"
    required="true"
/>
<x-forms.inputs.kpp
    title="КПП"
    name="kpp"
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
   <div class="form-card__label">Адрес юридического лица</div>
</div>

@include('components.application.create.forms.post-address')
