<div class="template-part__display-block"
     data-display-block
     data-name="legalBasisDetails"
>
   <div class="form-card__subtitle">
      <div class="form-card__label">Сведения о доверенности</div>
   </div>
   <x-forms.date
       title="Срок действия"
       name="legalBasisDuration"
       required="true"
       interval="1"
   />
   <x-forms.toggle
       title="Возможность передоверия"
       name="canEntrustLegalBasis"
       required="true"
   />
   <div class="form-card__subtitle">
      <div class="form-card__label">Лицо, выдавшее доверенность</div>
   </div>
   <x-forms.toggle
       title="Руководитель"
       name="isLegalBasisIssuerEqualsDirector"
       required="true"
       defaultValue="-1"
   />
   <div class="template-part__display-block"
        data-display-block
        data-name="legalBasisIssuerDetails"
   >
      <x-forms.inputs.name
          title="Фамилия"
          name="legalBasisIssuerLastName"
          required="true"
      />
      <x-forms.inputs.name
          title="Имя"
          name="legalBasisIssuerFirstName"
          required="true"
      />
      <x-forms.inputs.name
          title="Отчество"
          name="legalBasisIssuerMiddleName"
          required="false"
      />
   </div>
</div>
