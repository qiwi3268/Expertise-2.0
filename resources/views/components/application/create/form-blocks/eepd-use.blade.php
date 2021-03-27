<div class="application-form__card form-card closed"
     data-form
     data-name="eepdUse"
     data-card
>
   <div class="form-card__header" data-card-header>
      <i class="form-card__icon-valid fas fa-check-circle"></i>
      <i class="form-card__icon-invalid fas fa-exclamation-circle"></i>
      <div class="form-card__title">Сведения об использовании экономически эффективной проектной документации повторного использования</div>
   </div>
   <div class="form-card__body column-form" data-card-body>

      <x-forms.toggle
          title="Используется проектная документация повторного использования"
          name="isEepdUse"
          required="true"
      />
      <div class="form-card__display-block"
           data-display-block
           data-name="eepdUseInfo"
      >
         <x-forms.inputs.input
             title="Номер заключения экспертизы в отношении использованной экономически-эффективной проектной организации"
             name="eepdNumber"
             required="true"
         />
         <x-forms.date
             title="Дата утверждение заключения экспертизы в отношении использованной экономически-эффективной проектной организации"
             name="eepdDate"
             required="true"
             interval="-1"
         />
         <x-forms.file
             title="Решение Минстроя России о включении документации в реестр экономически-эффективной документации повторного использования"
             snakeMappings="1_1_1"
             required="false"
             multiple="false"
         />
         <x-forms.inputs.text-area
             title="Дополнительные сведения об использовании экономически-эффективной документации повторного использования"
             name="eepdNote"
             required="false"
             maxLength="1000"
         />
      </div>
      <div class="form-card__display-block"
           data-display-block
           data-name="eepdNonUse"
      >
         <x-miscs.single
             title="Причина неиспользования проектной документации повторного использования, в том числе экономически эффективной"
             required="true"
             alias="eepdNonUseReason"
         />
         <x-forms.file
             title="Загрузка файла (+ шаблон)"
             name="eepdNonUseFile"
             snakeMappings="1_1_1"
             required="true"
             multiple="false"
         />
      </div>

   </div>
</div>
