<div class="form-card__field form-field"
     data-field
     data-type="input"
     data-name="{{ $name }}"
     data-required="{{ $required }}"
     @isset ($pattern)
     data-pattern="{{ $pattern }}"
    @endisset
    data-max-length="{{ $maxLength }}"

>
   <div class="form-field__title">{{ $title }}</div>
   <div class="form-field__content">
      <div class="form-field__body">
         <div class="form-field__select">
            <input class="form-field__input"
                   data-field-result
                   name="{{ $name }}"
                   placeholder="Введите значение"
                   value=""
            >
            <i class="form-field__icon-filled fas fa-check"></i>
         </div>
      </div>
      <div class="form-field__bottom">
         <div class="form-field__error" data-field-error></div>
      </div>
   </div>
</div>
