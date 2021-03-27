<div class="form-card__field form-field"
     data-field
     data-type="file"
     data-name="{{ $snakeMappings }}"
     data-required="{{ $required }}"
     data-multiple="{{ $multiple }}"
     data-min-color="{{ $minColor }}"
     data-max-file-size="{{ $maxFileSize }}"
     data-allowable-extensions="{{ $allowableExtensions }}"
     data-forbidden-symbols="{{ $forbiddenSymbols }}"
>
   <div class="form-field__title">{{ $title }}</div>
   <div class="form-field__content">
      <div class="form-field__file-block">
         <div class="form-field__select file-select" data-modal-select="file">
            @if ($multiple == 'true')
               <div class="form-field__value">Загрузите один или несколько файлов</div>
            @else
               <div class="form-field__value">Загрузите файл</div>
            @endif
            <i class="form-field__icon fas fa-file"></i>
            <i class="form-field__icon-filled fas fa-check"></i>
         </div>
         <div class="form-field__files files" data-files-container></div>
      </div>
      @if ($multiple == 'true')
         <div class="form-field__error" data-field-error>Файлы обязательны для загрузки</div>
      @else
         <div class="form-field__error" data-field-error>Файл обязателен для загрузки</div>
      @endif
   </div>
   <input class="form-field__result"
          data-field-result
          type="hidden"
          name="{{ $snakeMappings }}"
          value=""
   >
</div>
