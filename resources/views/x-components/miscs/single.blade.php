<div class="form-card__field form-field"
     data-field
     data-type="misc"
     data-misc-type="formMisc"
     data-multiple="false"
     data-name="{{ $alias }}"
     data-required="{{ $required }}"
     @isset ($subAliases)
        data-sub-misc-aliases="{{ $subAliases }}"
     @endisset
>
   @include('x-components.miscs.single-select')
   @include('x-components.miscs.single-body')
</div>
