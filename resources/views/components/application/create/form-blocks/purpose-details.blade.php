<div class="application-form__card form-card closed"
     data-form
     data-name="purposeDetails"
     data-card
>
   <div class="form-card__header" data-card-header>
      <i class="form-card__icon-valid fas fa-check-circle"></i>
      <i class="form-card__icon-invalid fas fa-exclamation-circle"></i>
      <div class="form-card__title">Сведения о цели обращения</div>
   </div>
   <div class="form-card__body column-form" data-card-body>

    {{--  <div class="form-card__field form-field"
           data-field
           data-type="multipleInput"
           data-name="testMultipleInput"
           data-required="true"
--}}{{--           @isset ($pattern)--}}{{--
--}}{{--           data-pattern="{{ $pattern }}"--}}{{--
--}}{{--           @endisset--}}{{--
           data-max-length="1000"

      >
         <div class="form-field__title">Кадастровый номер</div>
         <div class="form-field__content">
            <div class="form-field__body">
               <div class="form-field__select">
                  <input class="form-field__input"
                         data-field-result
                         name="testMultipleInput"
                         placeholder="Введите значение"
                         value=""
                  >
                  <i class="form-field__icon-filled fas fa-check"></i>
               </div>
            </div>
            <div class="form-field__error" data-field-error></div>
         </div>
      </div>
--}}
 {{--     <x-forms.inputs.input
          title="Паспорт"
          name="testPassport"
          required="true"
          pattern="name"
      />

      <x-forms.inputs.input
          title="Номер"
          name="testNumber"
          required="true"
          pattern="/^\d+$/"
          maxLength="5"
      />
--}}

      <x-miscs.single
          title="Форма экспертизы"
          required="true"
          alias="expertiseForm"
          subAliases="expertisePurpose"
      />

      <div class="form-card__display-block"
           data-display-block
           data-name="stateExpertisePurpose"
      >

      </div>

      <div class="form-card__display-block"
           data-display-block
           data-name="nonStateExpertisePurpose"
      >

      </div>

      <x-miscs.single-sub
          title="Цель обращения"
          required="true"
          alias="expertisePurpose"
          subAliases="expertiseSubject"
          errorMessage="Выберите форму экспертизы"
      />
      <x-miscs.multiple-sub
          title="Предмет экспертизы"
          required="true"
          alias="expertiseSubject"
          errorMessage="Выберите цель обращения"
      />

      <x-forms.inputs.text-area
          title="Дополнительная информация"
          name="additionalInfo"
          required="false"
          maxLength="1000"
      />

   </div>
</div>
