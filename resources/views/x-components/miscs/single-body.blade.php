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
         @foreach ($items as $item)
            <div class="misc__item"
                 data-misc-item
                 data-id="{{ $item->id }}"
            >{{ $item->label }}</div>
         @endforeach
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
