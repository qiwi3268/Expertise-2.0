<div class="application-form__card form-card closed"
     data-form
     data-name="objectDetails"
     data-card
>
   <div class="form-card__header" data-card-header>
      <i class="form-card__icon-valid fas fa-check-circle"></i>
      <i class="form-card__icon-invalid fas fa-exclamation-circle"></i>
      <div class="form-card__title">Сведения об объекте</div>
   </div>
   <div class="form-card__body column-form" data-card-body>
      <x-forms.inputs.text-area
          title="Наименование объекта"
          name="objectName"
          required="true"
          maxLength="1000"
      />
      <x-miscs.single
          title="Вид объекта"
          required="true"
          alias="typeOfObject"
      />
      <x-miscs.single
          title="Классификатор ОКС - Группа"
          required="true"
          alias="oksGroup"
          subAliases="oksTypeOfObject"
      />
      <x-miscs.single-sub
          title="Классификатор ОКС - Тип объекта"
          required="true"
          alias="oksTypeOfObject"
          errorMessage="Заполните группу классификатора ОКС"
      />
      <div class="form-card__display-block"
           data-display-block
           data-name="gpzu"
      >
         <x-forms.inputs.input
             title="Номер градостроительного плана земельного участка"
             name="gpzuNumber"
             required="false"
         />
         <x-forms.date
             title="Дата градостроительного плана земельного участка"
             name="gpzuDate"
             required="false"
             interval="-1"
         />
      </div>
      <div class="form-card__display-block"
           data-display-block
           data-name="planningDocumentationApproval"
      >
         <x-forms.inputs.input
             title="Номер утверждения документации по планировке территории"
             name="planningDocumentationApprovalNumber"
             required="false"
         />
         <x-forms.date
             title="Дата утверждения документации по планировке территории"
             name="planningDocumentationApprovalDate"
             required="false"
             interval="-1"
         />
      </div>
      <x-miscs.single
          title="Вид работ"
          required="true"
          alias="typeOfWork"
      />
      <x-forms.inputs.decimal
          title="Сведения о сметной или предполагаемой (предельной) стоимости объекта капитального строительства, содержащиеся в решении по объекту или письме. тыс. руб."
          name="estimateCost"
          required="true"
      />

      <div class="form-card__display-block"
           data-display-block
           data-name="budgetInvestmentFile"
      >
         <x-forms.file
             title="Решение о подготовке и реализации бюджетных инвестиций"
             snakeMappings="1_1_1"
             required="false"
             multiple="true"
         />
      </div>
      <div class="form-card__display-block"
           data-display-block
           data-name="informationLetterFile"
      >
         <x-forms.file
             title="Информационное письмо"
             snakeMappings="1_1_1"
             required="true"
             multiple="true"
         />
      </div>

      <div class="form-card__display-block"
           data-display-block
           data-name="cadastralNumber"
      >
         <x-forms.inputs.input
             title="Кадастровый номер земельного участка"
             name="cadastralNumber"
             required="false"
         />
      </div>

      <x-forms.toggle
          title="Является объектом культурного наследия"
          name="isCulturalObject"
          required="true"
          defaultValue="-1"
      />
      <div class="form-card__display-block"
           data-display-block
           data-name="culturalObjectDetails"
      >
         <x-miscs.single
             title="Категория историко-культурного значения"
             required="true"
             alias="culturalObjectType"
         />
         <x-forms.inputs.input
             title="Номер в реестре"
             name="culturalObjectNumber"
             required="true"
         />
      </div>
      <x-forms.toggle
          title="Объект является национальным проектом"
          name="isNationalProject"
          required="true"
          defaultValue="-1"
      />
      <div class="form-card__display-block"
           data-display-block
           data-name="nationalProjectDetails"
      >
         <x-miscs.single
             title="Название национального проекта"
             required="true"
             alias="nationalProjectName"
             subAliases="federalProjectName"
         />
         <x-miscs.single-sub
             title="Название федерального проекта"
             required="true"
             alias="federalProjectName"
             errorMessage="Выберите название национального проекта"
         />
         <x-miscs.single
             title="Отрасль национального проекта"
             required="true"
             alias="nationalProjectSector"
             subAliases="nationalProjectSubsector"
         />
         <x-miscs.single-sub
             title="Подотрасль национального проекта"
             required="true"
             alias="nationalProjectSubsector"
             errorMessage="Выберите отрасль национального проекта"
             subAliases="nationalProjectGroup"
         />
         <x-miscs.single-sub
             title="Группа национального проекта"
             required="true"
             alias="nationalProjectGroup"
             errorMessage="Выберите подотрасль национального проекта"
         />
         <x-forms.date
             title="Планируемая дата окончания строительно-монтажных работ"
             name="buildingFinishDate"
             required="false"
             interval="0"
         />
      </div>
      <x-miscs.single
          title="Куратор"
          required="true"
          alias="curator"
      />

   </div>
</div>
