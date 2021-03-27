<div class="application-form__card form-card closed"
     data-form
     data-name="objectAddress"
     data-card
>
   <div class="form-card__header" data-card-header>
      <i class="form-card__icon-valid fas fa-check-circle"></i>
      <i class="form-card__icon-invalid fas fa-exclamation-circle"></i>
      <div class="form-card__title">Адрес объекта</div>
   </div>
   <div class="form-card__body column-form" data-card-body>

      <x-forms.inputs.postcode
          title="Почтовый индекс"
          name="postcode"
          required="false"
      />
      <x-miscs.single
          title="Субъект Российской Федерации"
          required="true"
          alias="regionCode"
      />
      <x-miscs.single
          title="Наименование района"
          required="true"
          alias="municipalDistrict"
      />
      <x-forms.inputs.input
          title="Город"
          name="city"
          required="false"
      />
      <x-forms.inputs.input
          title="Населенный пункт"
          name="locality"
          required="false"
      />
      <x-forms.inputs.input
          title="Улица"
          name="street"
          required="false"
      />
      <x-forms.inputs.input
          title="Номер здания/сооружения"
          name="building"
          required="false"
      />
      <x-forms.inputs.input
          title="Номер помещения"
          name="room"
          required="false"
      />
      <x-forms.inputs.text-area
          title="Дополнительные адресные данные"
          name="addressAdditionalInfo"
          required="false"
          maxLength="1000"
      />

   </div>
</div>
