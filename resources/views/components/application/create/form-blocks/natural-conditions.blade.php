<div class="application-form__card form-card closed"
     data-display-block
     data-form
     data-name="naturalConditions"
     data-card
>
   <div class="form-card__header" data-card-header>
      <i class="form-card__icon-valid fas fa-check-circle"></i>
      <i class="form-card__icon-invalid fas fa-exclamation-circle"></i>
      <div class="form-card__title">Сведения о природных условиях</div>
   </div>
   <div class="form-card__body column-form" data-card-body>

      <x-miscs.multiple
          title="Ветровой район"
          required="true"
          alias="windRegion"
      />
      <x-miscs.multiple
          title="Снеговой район"
          required="true"
          alias="snowRegion"
      />
      <x-miscs.multiple
          title="Интенсивность сейсмических воздействий, баллы (шкала MSK-64 в соответствии с ОСР-2015 по СП-14.13330.2014)"
          required="true"
          alias="seismicIntensity"
      />
      <x-miscs.multiple
          title="Климатический район и подрайон"
          required="true"
          alias="climaticRegionAndSubRegion"
      />
      <x-miscs.multiple
          title="Категория сложности инженерно-геологических условий"
          required="true"
          alias="engineeringGeologicalCondition"
      />

   </div>
</div>

