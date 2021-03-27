<div class="documentation"
     data-field
     data-type="documentationFile"
     data-name="{{ $snakeMappings }}"
     data-multiple="true"
     data-min-color="{{ $minColor }}"
     data-max-file-size="{{ $maxFileSize }}"
     data-allowable-extensions="{{ $allowableExtensions }}"
     data-forbidden-symbols="{{ $forbiddenSymbols }}"
>
   @foreach ($structure as $node)

      <div class="documentation__node"
           data-structure-node-id="{{ $node->id }}"
      >
         <div class="documentation__header"
              data-title="{{ $node->is_header ? 'true' : 'false' }}"
         >
            <div class="documentation__name" style="padding-left: {{ $node->depth * 25 + 15 }}px">{{ $node->name }}</div>
            @if (!$node->is_header)
               <i class="documentation__icon fas fa-plus" data-modal-select="file"></i>
            @endif
         </div>
         <div class="documentation__files files"
              data-files-container
              data-depth="{{ $node->depth }}"
         ></div>
      </div>

   @endforeach
   <input class="documentation__result"
          data-field-result
          type="hidden"
          name="{{ $snakeMappings }}"
          value=""
   >
</div>
