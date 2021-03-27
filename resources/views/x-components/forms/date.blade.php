<div class="form-card__field form-field"
     data-field
     data-type="date"
     data-name="{{ $name }}"
     data-required="{{ $required }}"
     data-interval="{{ $interval }}"
>
   <span class="form-field__title">{{ $title }}</span>
   <div class="form-field__content">
      <div class="form-field__body">
         <div class="form-field__select" data-modal-select="calendar">
            <span class="form-field__value" data-field-label>Выберите дату</span>
            <i class="form-field__icon fas fa-calendar-alt"></i>
            <i class="form-field__icon-filled fas fa-check"></i>
         </div>
         <i class="form-field__icon-clear fas fa-times" data-field-clear></i>
      </div>
      <div class="form-field__error" data-field-error>Дата обязательна для заполнения</div>
   </div>
   <input class="form-field__result"
          data-field-result
          type="hidden"
          name="{{ $name }}"
          value=""
   >
</div>
