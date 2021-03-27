<div class="application-form__card form-card closed"
     data-form
     data-name="ecologicalExpertise"
     data-card
>
   <div class="form-card__header" data-card-header>
      <i class="form-card__icon-valid fas fa-check-circle"></i>
      <i class="form-card__icon-invalid fas fa-exclamation-circle"></i>
      <div class="form-card__title">Сведения об экологической экспертизе</div>
   </div>
   <div class="form-card__body column-form" data-card-body>

      <x-forms.inputs.input
          title="Номер положительного заключения государственной экологической экспертизы"
          name="ecologicalConclusionNumber"
          required="false"
      />
      <x-forms.date
          title="Дата положительного заключения государственной экологической экспертизы"
          name="ecologicalConclusionNumberDate"
          required="false"
          interval="-1"
      />
      <x-forms.file
          title="Файл экологической экспертизы"
          snakeMappings="1_1_1"
          required="false"
          multiple="false"
      />

   </div>
</div>
