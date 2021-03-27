<div class="form-card__field form-field inline"
     data-field
     data-type="radio"
     data-name="{{ $name }}"
     data-required="{{ $required }}"
     data-multiple="false"
>
   <div class="form-field__title"></div>
   <div class="form-field__content">
      <div class="radio" data-radio-body>
         <div class="radio__item" data-radio-item data-id="1">
            <i class="radio__icon far fa-square" data-radio-icon></i>
            <span class="radio__text" data-radio-text>{{ $title }}</span>
         </div>
      </div>
      <div class="form-field__error" data-field-error>Поле обязательно для выбора</div>
   </div>
   <input class="form-field__result"
          data-field-result
          type="hidden"
          name="{{ $name }}"
          value=""
   >
</div>
