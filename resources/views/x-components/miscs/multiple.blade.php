<div class="form-card__field form-field"
     data-field
{{--     data-type="multipleMisc"--}}
     data-type="misc"
     data-misc-type="formMisc"
     data-multiple="true"
     data-name="{{ $alias }}"
     data-required="{{ $required }}"
>
   @include('x-components.miscs.multiple-select')
   @include('x-components.miscs.multiple-body')
</div>
