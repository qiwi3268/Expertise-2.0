<div class="application-form__card form-card closed"
     data-card
     data-template-block
     data-type="agreementCustomer"
     data-label="Заказчик по договору"
     data-template-card
     data-name="agreementCustomer"
>

   <div class="form-card__header"
        data-card-header
   >
      <i class="form-card__icon-valid fas fa-check-circle"></i>
      <i class="form-card__icon-invalid fas fa-exclamation-circle"></i>
      <div class="form-card__title">Заказчик по договору</div>
   </div>

   <div class="form-card__body column-form"
        data-card-body
   >

      <div class="template-block">
         <div class="template-block__add form-button" data-template-add>
            <div class="form-button__name">Добавить заказчика</div>
            <i class="form-button__icon fas fa-plus"></i>
         </div>

         <div class="template-block__body"
              data-template-body
         ></div>

         <div class="template-block__templates"
              data-templates-container
              data-display-block
              data-template="true"
              data-displayed="false"
         >

            {{--Шаблон заказчика по договору--}}
            <div class="template-part"
                 data-display-block
                 data-template-part
                 data-part-main-field="legalSubject"
            >
               <div class="template-part__body" data-part-body>

                  @include('components.application.create.forms.executor-equality')

                  <x-miscs.single
                      title="Вид заказчика"
                      alias="legalSubject"
                      required="true"
                  />
                  {{--Шаблон "Юридическое лицо"--}}
                  <div class="template-part__display-block"
                       data-display-block
                       data-name="organization"
                       data-form-part
                       data-form-part-key="1"
                  >
                     @include('components.application.create.forms.full-organization-details')

                     @include('components.application.create.forms.bank-details')

                     @include('components.application.create.forms.organization-signer')

                     <x-forms.file
                         title="Документы, подтверждающие полномочия"
                         snakeMappings="1_1_1"
                         multiple="true"
                         required="true"
                     />

                     @include('components.application.create.forms.organization-legal-basis-details')

                  </div>
                  {{--//Шаблон "Юридическое лицо"//--}}

                  {{--Шаблон "Физическое лицо"--}}
                  <div class="template-part__display-block"
                       data-display-block
                       data-name="person"
                       data-form-part
                       data-form-part-key="2"
                  >
                     @include('components.application.create.forms.person-details')
                     @include('components.application.create.forms.bank-details')
                     @include('components.application.create.forms.entrepreneur-signer')
                     <x-forms.file
                         title="Документы, подтверждающие полномочия"
                         snakeMappings="1_1_1"
                         multiple="true"
                         required="true"
                     />
                     @include('components.application.create.forms.entrepreneur-legal-basis-details')
                  </div>
                  {{--//Шаблон "Физическое лицо"//--}}

                  {{--Шаблон "Индивидуальный предприниматель"--}}
                  <div class="template-part__display-block"
                       data-display-block
                       data-name="entrepreneur"
                       data-form-part
                       data-form-part-key="3"
                  >

                     @include('components.application.create.forms.entrepreneur-details')
                     @include('components.application.create.forms.bank-details')
                     @include('components.application.create.forms.entrepreneur-signer')
                     <x-forms.file
                         title="Документы, подтверждающие полномочия"
                         snakeMappings="1_1_1"
                         multiple="true"
                         required="true"
                     />
                     @include('components.application.create.forms.entrepreneur-legal-basis-details')

                  </div>
                  {{--//Шаблон "Индивидуальный предприниматель"//--}}
               </div>
               <div class="template-part__actions">
                  <div class="form-button save" data-part-save>
                     <div class="form-button__name">Сохранить заказчика по договору</div>
                     <i class="form-button__icon fas fa-check"></i>
                  </div>
                  <div class="form-button remove" data-part-cancel>
                     <div class="form-button__name">Отмена</div>
                     <i class="form-button__icon fas fa-times"></i>
                  </div>
               </div>
            </div>
            {{--//Шаблон заказчика по договору//--}}

         </div>
      </div>

   </div>
</div>
