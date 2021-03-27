<div class="form-card__display-block"
     data-display-block
     data-name="executorEquality"
     data-displayed="false"
>
   <div class="form-card__field form-field"
        data-field
        data-type="misc"
        data-misc-type="executorEqualityMisc"
        data-name="equality"
        data-required="false"
{{--        data-displayed="false"--}}
   >
      <div class="form-field__title" data-misc-title>Совпадает с ролью</div>
      <div class="form-field__content">
         <div class="form-field__body">
            <div class="form-field__select" data-misc-select>
               <div class="form-field__value" data-field-label>Выберите значение</div>
               <i class="form-field__icon fas fa-bars"></i>
               <i class="form-field__icon-filled fas fa-check"></i>
            </div>
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
               <div class="misc__item" data-misc-item data-id="applicant">Заявитель</div>
               <div class="misc__item" data-misc-item data-id="agreementCustomer">Заказчик по договору</div>
               <div class="misc__item" data-misc-item data-id="3">Застройщик</div>
               <div class="misc__item" data-misc-item data-id="4">Технический заказчик</div>
               <div class="misc__item" data-misc-item data-id="5">Плательщик</div>
               <div class="misc__item" data-misc-item data-id="6">Генеральный проектировщик</div>
            </div>
         </div>
      </div>

      <input class="form-field__result"
             data-field-result
             data-misc-result
             name="equality"
             type="hidden"
             value=""
      >
   </div>
</div>
