<div class="application-form__card form-card closed"
     data-form
     data-name="historicalCulturalExpertise"
     data-card
>
   <div class="form-card__header" data-card-header>
      <i class="form-card__icon-valid fas fa-check-circle"></i>
      <i class="form-card__icon-invalid fas fa-exclamation-circle"></i>
      <div class="form-card__title">Сведения об историко-культурной экспертизе</div>
   </div>
   <div class="form-card__body column-form" data-card-body>

      <x-forms.inputs.input
          title="Номер положительного заключения"
          name="historicalCulturalConclusionNumber"
          required="false"
      />
      <x-forms.date
          title="Дата положительного заключения"
          name="historicalCulturalConclusionNumberDate"
          required="false"
          interval="-1"
      />
      <x-forms.file
          title="Файл историко-культурной экспертизы"
          snakeMappings="1_1_1"
          required="false"
          multiple="false"
      />

   </div>
</div>
