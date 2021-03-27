<div class="form-card__field form-field"
     data-field
     data-type="radio"
     data-name="{{ $name }}"
     data-required="{{ $required }}"
     data-multiple="false"
>
   <span class="form-field__title">{{ $title }}</span>
   <div class="form-field__content">
      <div class="radio inline" data-radio-body>
         @if ($defaultValue == '1')
            <div class="radio__item"
                 data-radio-item
                 data-id="1"
                 data-selected="true"
            >
               <i class="radio__icon far fa-check-square" data-radio-icon></i>
               <span class="radio__text">Да</span>
            </div>
            <div class="radio__item"
                 data-radio-item
                 data-id="-1"
            >
               <i class="radio__icon far fa-square" data-radio-icon></i>
               <span class="radio__text">Нет</span>
            </div>
         @elseif ($defaultValue == '-1')
            <div class="radio__item"
                 data-radio-item
                 data-id="1"
            >
               <i class="radio__icon far fa-square" data-radio-icon></i>
               <span class="radio__text">Да</span>
            </div>
            <div class="radio__item"
                 data-radio-item
                 data-id="-1"
                 data-selected="true"
            >
               <i class="radio__icon far fa-check-square" data-radio-icon></i>
               <span class="radio__text">Нет</span>
            </div>
         @else
            <div class="radio__item"
                 data-radio-item
                 data-id="1"
            >
               <i class="radio__icon far fa-square" data-radio-icon></i>
               <span class="radio__text">Да</span>
            </div>
            <div class="radio__item"
                 data-radio-item
                 data-id="-1"
            >
               <i class="radio__icon far fa-square" data-radio-icon></i>
               <span class="radio__text">Нет</span>
            </div>
         @endif

      </div>
      <div class="form-field__error" data-field-error>Поле обязательно для выбора</div>
   </div>
   <input class="form-field__result"
          data-field-result
          type="hidden"
          name="{{ $name }}"
          value="{{ $defaultValue }}"
   >
</div>
