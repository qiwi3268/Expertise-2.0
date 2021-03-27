<div class="form-card__field form-field"
     data-field
     data-type="input"
     data-name="{{ $name }}"
     data-required="{{ $required }}"
     data-max-length="{{ $maxLength }}"
>
   <div class="form-field__title">{{ $title }}</div>
   <div class="form-field__content">
      <div class="form-field__body">
         <textarea class="form-field__select form-field__text-area"
                   data-field-result
                   name="{{ $name }}"
         ></textarea>
      </div>
      <div class="form-field__error" data-field-error></div>
   </div>
</div>
