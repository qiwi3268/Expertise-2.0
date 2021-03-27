<div class="application-form__card form-card closed"
     data-card
     data-template-card
     data-name="designers"
>
   <div class="form-card__header" data-card-header>
      <i class="form-card__icon-valid fas fa-check-circle"></i>
      <i class="form-card__icon-invalid fas fa-exclamation-circle"></i>
      <div class="form-card__title">Сведения о лицах, подготовивших проектную документацию</div>
   </div>

   <div class="form-card__body column-form" data-card-body>
      <div class="template-block"
           data-template-block
           data-type="designers"
      >

         <div class="template-block__add form-button" data-template-add>
            <div class="form-button__name">Добавить проектировщика</div>
            <i class="form-button__icon fas fa-plus"></i>
         </div>

         <div class="template-block__body" data-template-body></div>

         <div class="template-block__templates"
              data-templates-container
              data-display-block
              data-template="true"
              data-displayed="false"
         >

            {{--Шаблон проектировщика--}}
            <div class="template-part"
                 data-display-block
                 data-template-part
                 data-part-main-field="legalSubject"
            >
               <div class="template-part__form"
                    data-display-block
                    data-part-view="form"
                    data-parent-part="designers"
               >
                  <div class="template-part__header" data-part-header>...</div>
                  <div class="template-part__body" data-part-body>

                     {{--   tmp-misc --}}
                     <div class="form-card__field form-field"
                          data-field
                          data-type="misc"
                          data-misc-type="formMisc"
                          data-multiple="false"
                          data-name="legalSubject"
                          data-required="true"
                     >
                        <div class="form-field__title" data-misc-title>Вид проектировщика</div>
                        <div class="form-field__content">
                           <div class="form-field__body">
                              <div class="form-field__select" data-misc-select>
                                 <div class="form-field__value" data-field-label>Выберите значение</div>
                                 <i class="form-field__icon fas fa-bars"></i>
                                 <i class="form-field__icon-filled fas fa-check"></i>
                              </div>
                              <i class="form-field__icon-clear fas fa-times" data-field-clear></i>
                           </div>
                           <div class="form-field__error" data-field-error>Поле обязательно для заполнения</div>
                        </div>
                        <div class="misc"
                             data-misc-modal
                        >
                           <i class="modal-close fas fa-times" data-misc-close></i>
                           <div class="misc__wrapper">
                              <div class="misc__search search">
                                 <div class="search__body">
                                    <i class="search__icon fas fa-search"></i>
                                    <input class="search__input"
                                           type="text"
                                           placeholder="Поиск в справочнике"
                                           data-misc-search
                                    >
                                 </div>
                              </div>
                              <div class="misc__body" data-misc-container>
                                 <div class="misc__item"
                                      data-misc-item
                                      data-id="1"
                                 >Юридическое лицо</div>
                                 <div class="misc__item"
                                      data-misc-item
                                      data-id="3"
                                 >Индивидуальный предприниматель</div>
                              </div>
                           </div>
                        </div>

                        <input class="form-field__result"
                               data-field-result
                               data-misc-result
                               name="legalSubject"
                               type="hidden"
                               value=""
                        >
                     </div>
                     {{--   tmp-misc --}}

                     {{--Шаблон "Юридическое лицо"--}}
                     <div class="template-part__display-block"
                          data-display-block
                          data-name="organization"
                          data-form-part
                          data-form-part-key="1"
                     >
                        @include('components.application.create.forms.full-organization-details')
                     </div>
                     {{--//Шаблон "Юридическое лицо"//--}}


                     {{--Шаблон "Индивидуальный предприниматель"--}}
                     <div class="template-part__display-block"
                          data-display-block
                          data-name="entrepreneur"
                          data-form-part
                          data-form-part-key="3"
                     >
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
                          data-parent-part="designers"
                     >
                        <div class="form-button__name">Сохранить проектировщика</div>
                        <i class="form-button__icon fas fa-check"></i>
                     </div>
                     <div class="form-button remove"
                          data-part-cancel
                          data-parent-part="designers"
                     >
                        <div class="form-button__name">Отмена</div>
                        <i class="form-button__icon fas fa-times"></i>
                     </div>
                  </div>
               </div>
               <div class="template-part__row"
                    data-display-block
                    data-part-view="row"
                    data-parent-part="designers"
                    data-hidden="true"
               >
                  <span class="template-part__label form-button" data-part-label></span>
                  <i class="template-part__delete fas fa-times" data-part-delete></i>
               </div>
            </div>
            {{--//Шаблон проектировщика//--}}

            {{--Шаблон выписок--}}
            <div class="template-part__display-block"
                 data-display-block
                 data-name="registerExtracts"
            >
                  @include('components.application.create.template-blocks.register-extract')
            </div>
            {{--//Шаблон выписок//--}}

         </div>


      </div>
   </div>
</div>


