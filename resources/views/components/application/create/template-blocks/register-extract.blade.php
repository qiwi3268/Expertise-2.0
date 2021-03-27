<div class="template-block"
     data-template-block
     data-type="registerExtracts"
>
   <div class="template-block__add form-button" data-template-add>
      <div class="form-button__name">Добавить выписку</div>
      <i class="form-button__icon fas fa-plus"></i>
   </div>

   <div class="template-block__body" data-template-body></div>

   <div class="template-block__templates"
        data-templates-container
        data-display-block
        data-template="true"
        data-displayed="false"
   >
      {{--Шаблон выписки из реестра членов саморегулируемой организации--}}
      <div class="template-part"
           data-display-block
           data-template-part
           data-part-main-field="extractNumber"
      >
         <div class="template-part__form"
              data-display-block
              data-part-view="form"
         >
            <div class="template-part__header" data-part-header>...</div>
            <div class="template-part__body" data-part-body>
               <x-forms.inputs.input
                   title="Название саморегулируемой организации"
                   name="sroName"
                   required="true"
               />
               <x-forms.inputs.input
                   title="Регистрационный номер в государственном реестре саморегулируемых организаций"
                   name="registerNumber"
                   required="true"
               />
               <x-forms.inputs.input
                   title="Номер выписки из реестра членов СРО"
                   name="extractNumber"
                   required="true"
               />
               <x-forms.date
                   title="Дата выписки"
                   name="extractDate"
                   required="true"
                   interval="-1"
               />
               <x-forms.file
                   title="Выписка из реестра членов СРО"
                   snakeMappings="1_1_1"
                   multiple="true"
                   required="true"
               />
            </div>
            <div class="template-part__actions">
               <div class="form-button save" data-part-save>
                  <div class="form-button__name">Сохранить выписку</div>
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
      {{--Шаблон выписки из реестра членов саморегулируемой организации--}}
   </div>

</div>
