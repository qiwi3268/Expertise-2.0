<div class="application-form__card form-card closed"
     data-card
     data-template-card
     data-name="surveyDesigners"
>
   <div class="form-card__header" data-card-header>
      <i class="form-card__icon-valid fas fa-check-circle"></i>
      <i class="form-card__icon-invalid fas fa-exclamation-circle"></i>
      <div class="form-card__title">Сведения о результатах инженерных изысканий</div>
   </div>

   <div class="form-card__body column-form" data-card-body>
      <div class="template-block"
           data-template-block
           data-type="surveyDesigners"
      >

         <div class="template-block__add form-button" data-template-add>
            <div class="form-button__name">Добавить изыскание</div>
            <i class="form-button__icon fas fa-plus"></i>
         </div>

         <div class="template-block__body" data-template-body></div>

         <div class="template-block__templates"
              data-templates-container
              data-display-block
              data-template="true"
              data-displayed="false"
         >
            {{--Шаблон изыскания--}}
            <div class="template-part"
                 data-display-block
                 data-template-part
                 data-part-main-field="legalSubject"
            >
               <div class="template-part__form"
                    data-display-block
                    data-part-view="form"
                    data-parent-part="surveyDesigners"
               >
                  <div class="template-part__header" data-part-header>...</div>
                  <div class="template-part__body" data-part-body>
                     <x-miscs.single
                         title="Вид инженерных изысканий"
                         alias="engineeringSurvey"
                         required="true"
                     />
                     <x-miscs.single
                         title="Вид лица, подготовившего изыскание"
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
                        <x-forms.date
                            title="Дата подготовки технического отчета"
                            name="surveyPreparationDate"
                            required="true"
                            interval="-1"
                        />
                        @include('components.application.create.forms.full-organization-details')
                     </div>
                     {{--//Шаблон "Юридическое лицо"//--}}

                     {{--Шаблон "Физическое лицо"--}}
                     <div class="template-part__display-block"
                          data-display-block
                          data-name="person"
                          data-form-part
                          data-form-part-key="2"
                     >
                        <x-forms.date
                            title="Дата подготовки технического отчета"
                            name="surveyPreparationDate"
                            required="true"
                            interval="-1"
                        />
                        @include('components.application.create.forms.person-details')
                     </div>
                     {{--//Шаблон "Физическое лицо"//--}}


                     {{--Шаблон "Индивидуальный предприниматель"--}}
                     <div class="template-part__display-block"
                          data-display-block
                          data-name="entrepreneur"
                          data-form-part
                          data-form-part-key="3"
                     >
                        <x-forms.date
                            title="Дата подготовки технического отчета"
                            name="surveyPreparationDate"
                            required="true"
                            interval="-1"
                        />
                        @include('components.application.create.forms.entrepreneur-details')
                     </div>
                     {{--//Шаблон "Индивидуальный предприниматель"//--}}

                     <div class="template-part__display-block"
                          data-display-block
                          data-name="extractsTmp"
                     >
                        <div class="form-card__subtitle">
                           <div class="form-card__label">Выписки из реестра членов саморегулируемой организации</div>
                        </div>
                        <x-forms.toggle
                            title="Требуется выписка из реестра членов СРО"
                            name="isRequiredExtract"
                            required="true"
                        />
                        <div class="template-part__display-block"
                             data-display-block
                             data-name="notRequiredSroReason"
                        >
                           <x-forms.file
                               title="Документы, подтверждающие, что для исполнителя работ по подготовке ПД и ИИ не требуется членство в СРО"
                               snakeMappings="1_1_1"
                               multiple="true"
                               required="true"
                           />
                        </div>
                     </div>

                  </div>
                  <div class="template-part__actions">
                     <div class="form-button save"
                          data-part-save
                          data-parent-part="surveyDesigners"
                     >
                        <div class="form-button__name">Сохранить изыскание</div>
                        <i class="form-button__icon fas fa-check"></i>
                     </div>
                     <div class="form-button remove"
                          data-part-cancel
                          data-parent-part="surveyDesigners"
                     >
                        <div class="form-button__name">Отмена</div>
                        <i class="form-button__icon fas fa-times"></i>
                     </div>
                  </div>
               </div>
               <div class="template-part__row"
                    data-display-block
                    data-part-view="row"
                    data-parent-part="surveyDesigners"
                    data-hidden="true"
               >
                  <span class="template-part__label form-button" data-part-label></span>
                  <i class="template-part__delete fas fa-times" data-part-delete></i>
               </div>
            </div>
            {{--//Шаблон изыскания//--}}

            {{--Шаблон выписок--}}
            <div class="template-part__display-block"
                 data-display-block
                 data-name="registerExtracts"
                 data-displayed="false"
            >
               @include('components.application.create.template-blocks.register-extract')
            </div>
            {{--//Шаблон выписок//--}}

         </div>


      </div>
   </div>
</div>


