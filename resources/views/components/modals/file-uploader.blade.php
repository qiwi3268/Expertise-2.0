<div id="fileOverlay" class="overlay"></div>
<div id="fileModal" class="modal file-modal" data-modal>
   <i class="modal-close fas fa-times" data-modal-close></i>
   <div class="file-modal__header">
      <div id="fileUploaderTitle" class="file-modal__title">Выберите или перетащите файлы</div>
      <div id="fileUploaderProgressBar" class="file-modal__progress-bar"></div>
   </div>
   <div id="filesDropArea" class="file-modal__drop-area" data-multiple>
      <div id="fileUploaderBody" class="file-modal__body"></div>
   </div>
   <div class="file-modal__actions modal-actions">
      <div id="fileUploaderUpload" class="modal-actions__button">Выбрать</div>
      <div id="fileUploaderSubmit" class="modal-actions__button">Загрузить</div>
      <div id="fileUploaderDelete" class="modal-actions__button">Удалить файлы</div>
   </div>
   <input id="fileUploaderInput" type="file" name="downloadFiles[]" hidden/>
</div>
