<div class="sub-header">
   <div class="sub-header__title">Создание заявления</div>
   <div class="sub-header__actions">
      <div id="saveApplication" class="sub-header__action action-button">
         <div class="action-button__name">Сохранить заявление</div>
      </div>
   </div>
</div>
<div class="application-form">
   @include('components.application.create.sidebar')

   <div class="application-form__cards">

      @include('components.application.create.form-blocks.purpose-details')
      @include('components.application.create.form-blocks.object-details')
      @include('components.application.create.form-blocks.object-address')

      @include('components.application.create.form-blocks.natural-conditions')
      @include('components.application.create.form-blocks.ecological-expertise')
      @include('components.application.create.form-blocks.industrial-safety')
      @include('components.application.create.form-blocks.historical-cultural-expertise')

      @include('components.application.create.form-blocks.eepd-use')
      @include('components.application.create.template-blocks.financing-sources')

      @include('components.application.create.form-blocks.teps')

      @include('components.application.create.template-blocks.applicant')
      @include('components.application.create.template-blocks.agreement-customer')
      @include('components.application.create.template-blocks.developer')
      @include('components.application.create.template-blocks.technical-customer')
      @include('components.application.create.template-blocks.payer')


      @include('components.application.create.template-blocks.designers')
      @include('components.application.create.template-blocks.survey-designers')

      @include('components.application.create.form-blocks.service-terms')
      @include('components.application.create.form-blocks.documentation')

   </div>
</div>

<div id="miscOverlay" class="overlay"></div>
@include('components.modals.file-uploader')
@include('components.modals.error')
@include('components.modals.calendar')
@include('components.modals.sign-create')
@include('components.modals.confirmation')
