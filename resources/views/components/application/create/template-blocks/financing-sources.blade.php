<div class="application-form__card form-card closed"
     data-card
     data-template-card
     data-name="financingSources"
>
   <div class="form-card__header" data-card-header>
      <i class="form-card__icon-valid fas fa-check-circle"></i>
      <i class="form-card__icon-invalid fas fa-exclamation-circle"></i>
      <div class="form-card__title">Сведения об источниках финансирования</div>
   </div>

   <div class="form-card__body column-form" data-card-body>
      <div class="template-block"
           data-template-block
           data-type="financingSources"
      >

         <div class="template-block__add form-button" data-template-add>
            <div class="form-button__name">Добавить источник финансирования</div>
            <i class="form-button__icon fas fa-plus"></i>
         </div>

         <div class="template-block__body" data-template-body></div>

         <div class="template-block__templates"
              data-templates-container
              data-display-block
              data-template="true"
              data-displayed="false"
         >

            {{--Шаблон источника финансирования--}}
            <div class="template-part"
                 data-display-block
                 data-template-part
                 data-part-main-field="financingSource"
            >
               <div class="template-part__form"
                    data-display-block
                    data-part-view="form"
               >
                  <div class="template-part__header" data-part-header>...</div>
                  <div class="template-part__body" data-part-body>

                     <x-miscs.radio-single
                         title="Тип источника финансирования"
                         alias="financingSource"
                         required="true"
                         multiple="false"
                     />

                     {{--Шаблон "Бюджетные средства"--}}
                     <div class="template-part__display-block"
                          data-display-block
                          data-name="budget"
                          data-form-part
                          data-form-part-key="1"
                     >
                        <x-miscs.single
                            title="Уровень бюджета"
                            required="true"
                            alias="budgetLevel"
                        />
                        <x-forms.inputs.percent
                            title="Размер финансирования (в процентном отношении к полной стоимости проекта)"
                            name="percent"
                            required="true"
                        />
                     </div>
                     {{--//Шаблон "Бюджетные средства"//--}}
                     {{--Шаблон "Средства юридических лиц, перечисленных в части 2 статьи 48.2 Градостроительного кодекса Российской Федерации"--}}
                     <div class="template-part__display-block"
                          data-display-block
                          data-name="organization"
                          data-form-part
                          data-form-part-key="2"
                     >
                        <x-forms.inputs.percent
                            title="Размер финансирования (в процентном отношении к полной стоимости проекта)"
                            name="percent"
                            required="true"
                        />
                        <div class="form-card__subtitle">
                           <div class="form-card__label">Сведения о юридическом лице - источнике финансирование</div>
                        </div>
                        @include('components.application.create.forms.main-organization-details')

                     </div>
                     {{--//Шаблон "Средства юридических лиц, перечисленных в части 2 статьи 48.2 Градостроительного кодекса Российской Федерации"//--}}
                     {{--Шаблон "Средства, не входящие в перечень, указанный в части 2 статьи 8.3 Градостроительного кодекса Российской Федерации"--}}
                     <div class="template-part__display-block"
                          data-display-block
                          data-name="other"
                          data-form-part
                          data-form-part-key="3"
                     >
                        <x-forms.inputs.percent
                            title="Размер финансирования (в процентном отношении к полной стоимости проекта)"
                            name="percent"
                            required="false"
                        />
                     </div>
                     {{--//Шаблон "Средства, не входящие в перечень, указанный в части 2 статьи 8.3 Градостроительного кодекса Российской Федерации"//--}}
                  </div>
                  <div class="template-part__actions">
                     <div class="form-button save" data-part-save>
                        <div class="form-button__name">Сохранить источник финансирования</div>
                        <i class="form-button__icon fas fa-check"></i>
                     </div>
                     <div class="form-button remove" data-part-cancel>
                        <div class="form-button__name">Отмена</div>
                        <i class="form-button__icon fas fa-times"></i>
                     </div>
                  </div>
               </div>
               <div class="template-part__row"
                    data-display-block
                    data-part-view="row"
                    data-hidden="true"
               >
                  <span class="template-part__label form-button" data-part-label></span>
                  <i class="template-part__delete fas fa-times" data-part-delete></i>
               </div>
            </div>
            {{--//Шаблон источника финансирования//--}}

         </div>
      </div>
   </div>
</div>



