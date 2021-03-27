<div class="form-card__field form-field"
     data-field
     data-type="radio"
     data-name="{{ $alias }}"
     data-required="{{ $required }}"
     data-multiple="{{ $multiple }}"
>
   <div class="form-field__title">{{ $title }}</div>
   <div class="form-field__content">
      <div class="form-field__radio radio" data-radio-body>
         @foreach ($items as $item)
            <div class="radio__item" data-radio-item data-id="{{ $item->id }}">
               <i class="radio__icon far fa-square" data-radio-icon></i>
               <span class="radio__text" data-radio-text>{{ $item->label }}</span>
            </div>
         @endforeach
      </div>
      <div class="form-field__error" data-field-error>Поле обязательно для выбора</div>
   </div>
   <input class="form-field__result"
          data-field-result
          type="hidden"
          name="financingType"
          value=""
   >
</div>
