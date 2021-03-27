<div class="application-form__card form-card closed"
     data-form
     data-name="teps"
     data-card
>
   <div class="form-card__header" data-card-header>
      <i class="form-card__icon-valid fas fa-check-circle"></i>
      <i class="form-card__icon-invalid fas fa-exclamation-circle"></i>
      <div class="form-card__title">Технико-экономические показатели</div>
   </div>

   <div class="form-card__body grid-form" data-card-body>

      <div class="form-card__display-block"
           data-display-block
           data-name="tepsEmptyObjectType"
      >
         <div class="form-card__message">
            <i class="form-card__icon-message fas fa-exclamation"></i>
            <span class="form-card__message-text">Выберите вид объекта</span>
         </div>
      </div>

      {{--Производственный/непроизводственный объект--}}
      <div class="form-card__display-block"
           data-display-block
           data-name="nonLinearTeps"
      >

         {{--Общие ТЭПы для производственного/непроизводственного объекта--}}
         <div class="form-card__display-block"
              data-display-block
              data-name="nonLinearGeneralTeps"
         >
            <div class="form-card__row">

               <x-forms.inputs.decimal
                   title="Площадь участка (м2)"
                   name="landArea"
                   required="true"
               />
               <x-forms.inputs.decimal
                   title="Площадь застройки (м2)"
                   name="buildingArea"
                   required="true"
               />
               <x-forms.inputs.decimal
                   title="Строительный объем (м3)"
                   name="nonProductionBuildingSize"
                   required="true"
               />

               <div class="form-card__column">
                  <x-forms.inputs.decimal
                      title="В том числе надземной части (м3)"
                      name="buildingOvergroundSize"
                      required="true"
                  />
                  <x-forms.inputs.decimal
                      title="В том числе подземной части (м3)"
                      name="buildingUndergroundSize"
                      required="true"
                  />
               </div>

               <x-forms.inputs.decimal
                   title="Общая площадь (м2)"
                   name="nonProductionTotalArea"
                   required="true"
               />

               <div class="form-card__column">
                  <x-forms.inputs.integer
                      title="Количество этажей (шт.)"
                      name="numberOfFloors"
                      required="true"
                  />
                  <x-forms.inputs.integer
                      title="В том числе подземных (шт.)"
                      name="numberOfUndergroundFloors"
                      required="true"
                  />
               </div>

               <x-forms.inputs.decimal
                   title="Высота (м)"
                   name="objectHeight"
                   required="true"
               />
               <x-forms.inputs.input
                   title="Класс энергоэффективности здания"
                   name="energyEfficiencyClass"
                   required="true"
               />

            </div>
         </div>
         {{--//Общие ТЭПы для производственного/непроизводственного объекта//--}}

         {{--Объект непроизводственного назначения--}}
         <div class="form-card__display-block"
              data-display-block
              data-name="nonProductionTeps"
         >

            <div class="form-card__display-block"
                 data-display-block
                 data-name="tepsEmptyOksGroup"
            >
               <div class="form-card__message">
                  <i class="form-card__icon-message fas fa-exclamation"></i>
                  <span class="form-card__message-text">Для отображения всех показателей выберите группу классификатора ОКС</span>
               </div>
            </div>


            {{--Нежилой объект--}}
            <div class="form-card__display-block"
                 data-display-block
                 data-name="nonResidentialTeps"
            >
               <div class="form-card__subtitle">
                  <div class="form-card__label">ТЭПы нежилого объекта</div>
               </div>
               <div class="form-card__row">
                  <x-forms.inputs.integer
                      title="Количество зданий, сооружений (шт.)"
                      name="nonResidentialNumberOfBuildings"
                      required="true"
                  />
                  <x-forms.inputs.integer
                      title="Количество мест (шт.)"
                      name="numberOfSeats"
                      required="true"
                  />
                  <x-forms.inputs.integer
                      title="Вместимость (чел.)"
                      name="capacity"
                      required="true"
                  />
               </div>
            </div>
            {{--//Нежилой объект//--}}

            {{--Объект жилищного фонда--}}
            <div class="form-card__display-block"
                 data-display-block
                 data-name="residentialTeps"
            >
               <div class="form-card__subtitle">
                  <div class="form-card__label">ТЭПы объекта жилищного фонда</div>
               </div>
               <div class="form-card__row">
                  <x-forms.inputs.decimal
                      title="Площадь нежилых помещений (м2)"
                      name="nonResidentialRoomsArea"
                      required="true"
                  />
                  <x-forms.inputs.decimal
                      title="Площадь встроенно-пристроенных помещений (м2)"
                      name="extensionRoomsArea"
                      required="true"
                  />
                  <x-forms.inputs.decimal
                      title="Общая площадь жилых помещений (за исключением балконов, лоджий, веранд и террас) (м2)"
                      name="residentialAreaExcludeExtensions"
                      required="true"
                  />
                  <x-forms.inputs.decimal
                      title="Общая площадь жилых помещений (с учётом балконов, лоджий, веранд и террас) (м2)"
                      name="residentialAreaIncludeExtensions"
                      required="true"
                  />
                  <x-forms.inputs.decimal
                      title="Общая площадь нежилых помещений, в том числе площадь общего имущество в многоквартирном доме (м2)"
                      name="nonResidentialFullArea"
                      required="true"
                  />

                  <div class="form-card__column">
                     <x-forms.inputs.integer
                         title="Количество 1-комнатных квартир (шт.)"
                         name="numberOf1RoomApartments"
                         required="true"
                     />
                     <x-forms.inputs.integer
                         title="Количество 2-комнатных квартир (шт.)"
                         name="numberOf2RoomApartments"
                         required="true"
                     />
                  </div>
                  <div class="form-card__column">
                     <x-forms.inputs.integer
                         title="Количество 3-комнатных квартир (шт.)"
                         name="numberOf3RoomApartments"
                         required="true"
                     />
                     <x-forms.inputs.integer
                         title="Количество 4-комнатных квартир (шт.)"
                         name="numberOf4RoomApartments"
                         required="true"
                     />
                  </div>
                  <x-forms.inputs.integer
                      title="Количество более чем 4-комнатных квартир (шт.)"
                      name="numberOfMore4RoomApartments"
                      required="true"
                  />
                  <div class="form-card__column">
                     <x-forms.inputs.decimal
                         title="Общая площадь 1-комнатных квартир (м2)"
                         name="areaOf1RoomApartments"
                         required="true"
                     />
                     <x-forms.inputs.decimal
                         title="Общая площадь 2-комнатных квартир (м2)"
                         name="areaOf2RoomApartments"
                         required="true"
                     />
                  </div>
                  <div class="form-card__column">
                     <x-forms.inputs.decimal
                         title="Общая площадь 3-комнатных квартир (м2)"
                         name="areaOf3RoomApartments"
                         required="true"
                     />
                     <x-forms.inputs.decimal
                         title="Общая площадь 4-комнатных квартир (м2)"
                         name="areaOf4RoomApartments"
                         required="true"
                     />
                  </div>
                  <x-forms.inputs.decimal
                      title="Общая площадь более чем 4-комнатных квартир (м2)"
                      name="areaOfMore4RoomApartments"
                      required="true"
                  />
               </div>
            </div>
            {{--//Объект жилищного фонда//--}}
         </div>
         {{--//Объект непроизводственного назначения//--}}

         {{--Объект производственного назначения--}}
         <div class="form-card__display-block"
              data-display-block
              data-name="productionTeps"
         >
            <div class="form-card__subtitle">
               <div class="form-card__label">ТЭПы объекта производственного назначения</div>
            </div>
            <div class="form-card__row">
               <div class="form-card__tep">
                  <x-forms.inputs.input
                      title="Тип объекта"
                      name="productionObjectType"
                      required="true"
                  />
                  <x-forms.inputs.input
                      title="Единица измерения"
                      name="linearTypeMeasure"
                      required="true"
                  />
               </div>

               <div class="form-card__tep">
                  <x-forms.inputs.input
                      title="Мощность"
                      name="productionObjectPower"
                      required="true"
                  />
                  <x-forms.inputs.input
                      title="Единица измерения"
                      name="linearTypeMeasure"
                      required="true"
                  />
               </div>

               <div class="form-card__tep">
                  <x-forms.inputs.input
                      title="Производительность"
                      name="performance"
                      required="true"
                  />
                  <x-forms.inputs.input
                      title="Единица измерения"
                      name="linearTypeMeasure"
                      required="true"
                  />
               </div>

               <x-forms.inputs.integer
                   title="Количество зданий, сооружений (шт.)"
                   name="productionNumberOfBuildings"
                   required="true"
               />
            </div>

         </div>
         {{--//Объект производственного назначения//--}}

      </div>
      {{--//Производственный/непроизводственный объект//--}}

      {{--Линейный объект--}}
      <div class="form-card__display-block"
           data-display-block
           data-name="linearTeps"
      >
         <div class="form-card__row">
            <x-forms.inputs.input
                title="Категория (класс)"
                name="objectCategory"
                required="true"
            />
            <x-forms.inputs.decimal
                title="Протяженность (м)"
                name="objectLength"
                required="true"
            />
            <div class="form-card__tep">
               <x-forms.inputs.input
                   title="Мощность (пропускная способность, грузооборот, интенсивность движения)"
                   name="linearObjectPower"
                   required="false"
               />
               <x-forms.inputs.input
                   title="Единица измерения"
                   name="powerMeasure"
                   required="false"
               />
            </div>
            <x-forms.inputs.decimal
                title="Диаметр трубопроводов (мм)"
                name="pipelinesDiameter"
                required="false"
            />
            <x-forms.inputs.decimal
                title="Количество трубопроводов (м)"
                name="numberOfPipelines"
                required="false"
            />
            <x-forms.inputs.input
                title="Характеристика материала труб"
                name="pipeMaterialCharacteristic"
                required="false"
            />
            <x-forms.inputs.input
                title="Тип (КЛ, ВЛ, КВЛ)"
                name="linearType"
                required="false"
            />
            <x-forms.inputs.decimal
                title="Уровень напряжения линий электропередачи (кВ)"
                name="voltage"
                required="false"
            />
         </div>

      </div>
      {{--//Линейный объект//--}}


   </div>
</div>
