<div class="form-card__field form-field"
     data-field
     data-type="misc"
     data-misc-type="formDependentMisc"
     data-multiple="true"
     data-name="{{ $alias }}"
     data-required="{{ $required }}"
     data-error-message="{{ $errorMessage }}"
>
   @include('x-components.miscs.multiple-select')
   @include('x-components.miscs.multiple-sub-body')
</div>
