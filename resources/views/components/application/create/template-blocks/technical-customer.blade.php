<div class="application-form__card form-card closed"
     data-card
     data-template-block
     data-type="technicalCustomer"
     data-label="Технический заказчик"
     data-template-card
     data-name="technicalCustomer"
>

   <div class="form-card__header"
        data-card-header
   >
      <i class="form-card__icon-valid fas fa-check-circle"></i>
      <i class="form-card__icon-invalid fas fa-exclamation-circle"></i>
      <div class="form-card__title">Сведения о техническом заказчике</div>
   </div>

   <div class="form-card__body column-form"
        data-card-body
   >

      <div class="template-block">
         <div class="template-block__add form-button" data-template-add>
            <div class="form-button__name">Добавить технического заказчика</div>
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

            {{--Шаблон технического заказчика--}}
            <div class="template-part"
                 data-display-block
                 data-template-part
                 data-part-main-field="legalSubject"
            >
               <div class="template-part__body" data-part-body>

                  @include('components.application.create.forms.executor-equality')

                  <div class="form-card__field form-field"
                       data-field
                       data-type="radio"
                       data-name="legalSubject"
                       data-required="false"
                       data-multiple="false"
                  >
                     <div class="form-field__title">Вид технического заказчика</div>
                     <div class="form-field__content">
                        <div class="form-field__radio radio" data-radio-body>
                           <div class="radio__item"
                                data-radio-item
                                data-id="1"
                           >
                              <i class="radio__icon far fa-square" data-radio-icon></i>
                              <span class="radio__text" data-radio-text>Юридическое лицо</span>
                           </div>
                        </div>
                        <div class="form-field__error" data-field-error>Поле обязательно для выбора</div>
                     </div>
                     <input class="form-field__result"
                            data-field-result
                            type="hidden"
                            name="legalSubject"
                            value=""
                     >
                  </div>

                  {{--Шаблон "Юридическое лицо"--}}
                  <div class="template-part__display-block"
                       data-display-block
                       data-name="organization"
                       data-form-part
                       data-form-part-key="1"
                  >

                     @include('components.application.create.forms.full-organization-details')

                     <div class="form-card__subtitle">
                        <div class="form-card__label">Документы, подтверждающие полномочия</div>
                     </div>

                     <x-forms.file
                         title="Документы, подтверждающие полномочия"
                         snakeMappings="1_1_1"
                         multiple="true"
                         required="true"
                     />


                  </div>
                  {{--//Шаблон "Юридическое лицо"//--}}

               </div>
               <div class="template-part__actions">
                  <div class="form-button save" data-part-save>
                     <div class="form-button__name">Сохранить технического заказчика</div>
                     <i class="form-button__icon fas fa-check"></i>
                  </div>
                  <div class="form-button remove" data-part-cancel>
                     <div class="form-button__name">Отмена</div>
                     <i class="form-button__icon fas fa-times"></i>
                  </div>
               </div>
            </div>
            {{--//Шаблон технического заказчика//--}}

         </div>
      </div>

   </div>
</div>
