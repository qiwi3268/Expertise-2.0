<div class="application-form__card form-card closed"
     data-form
     data-name="industrialSafetyExpertise"
     data-card
>
   <div class="form-card__header" data-card-header>
      <i class="form-card__icon-valid fas fa-check-circle"></i>
      <i class="form-card__icon-invalid fas fa-exclamation-circle"></i>
      <div class="form-card__title">Сведения об экспертизе промышленной безопасности</div>
   </div>
   <div class="form-card__body column-form" data-card-body>

      <x-forms.inputs.input
          title="Номер положительного заключения"
          name="industrialSafetyConclusionNumber"
          required="false"
      />
      <x-forms.date
          title="Дата положительного заключения"
          name="industrialSafetyConclusionNumberDate"
          required="false"
          interval="-1"
      />
      <x-forms.file
          title="Файл экспертизы промышленной безопасности"
          snakeMappings="1_1_1"
          required="false"
          multiple="false"
      />

   </div>
</div>
