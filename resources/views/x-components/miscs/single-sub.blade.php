<div class="form-card__field form-field"
     data-field
     data-type="misc"
     data-misc-type="formDependentMisc"
     data-multiple="false"
     data-name="{{ $alias }}"
     data-required="{{ $required }}"
     data-error-message="{{ $errorMessage }}"
     @isset ($subAliases)
         data-sub-misc-aliases="{{ $subAliases }}"
    @endisset
>
   @include('x-components.miscs.single-select')
   @include('x-components.miscs.single-sub-body')
</div>
