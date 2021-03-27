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
      <div class="misc__body" data-misc-container></div>
      <div class="misc__actions modal-actions">
         <div class="modal-actions__button" data-misc-submit>Выбрать</div>
         <div class="modal-actions__button" data-misc-unselect>Снять выбор</div>
      </div>
   </div>
</div>

<input class="form-field__result"
       data-field-result
       data-misc-result
       name="{{ $alias }}"
       type="hidden"
       value=""
>
