<div id="signOverlay" class="overlay sign-overlay"></div>
<div id="signModal" class="modal sign-modal" data-modal>
   <i class="modal-close fas fa-times" data-close-button></i>

   <div class="sign-modal__top">
      <div id="signPluginInfo" class="sign-modal__header" data-displayed="false">
         <div class="sign-modal__plugin-row">
            <span class="sign-modal__plugin-label">Версия плагина: </span>
            <span id="signPluginVersion" class="sign-modal__text"></span>
         </div>
         <div class="sign-modal__plugin-row">
            <span class="sign-modal__plugin-label">Версия криптопровайдера: </span>
            <span id="signCspVersion" class="sign-modal__text"></span>
         </div>
      </div>

      <div class="sign-modal__file-body">
         <div class="sign-modal__file-info">
            <div id="signFile" class="sign-modal__file"></div>
         </div>

         <div class="sign-modal__buttons">
            <div id="signDelete" class="form-button" data-displayed="false">
               <div class="form-button__name">Удалить подпись</div>
               <i class="form-button__icon fas fa-eraser"></i>
            </div>
            <div id="signUpload" class="form-button" data-displayed="false">
               <div class="form-button__name">Загрузить открепленную подпись</div>
               <i class="form-button__icon fas fa-file-upload"></i>
            </div>
            <div id="signCreate" class="form-button" data-displayed="false">
               <div class="form-button__name">Создать открепленную подпись</div>
               <i class="form-button__icon fas fa-pen-alt"></i>
            </div>
            <input id="signExternal" type="file" name="downloadFiles[]" hidden/>
         </div>
      </div>

      <div id="signValidate" class="sign-modal__validate" data-displayed="false"></div>
   </div>
   <div class="sign-modal__bottom">
      <div id="signCerts" class="sign-modal__certs" data-displayed="false">

         <div id="signCertList" class="sign-modal__cert-list">
            <div class="sign-modal__title">Выберите сертификат:</div>
         </div>

         <div id="signCertInfo" class="sign-modal__cert-info" data-displayed="false">
            <div class="sign-modal__cert-row">
               <span class="sign-modal__label">Данные о выбранном сертификате:</span>
            </div>
            <div class="sign-modal__cert-row">
               <span class="sign-modal__label">Владелец:</span>
               <span id="signSubjectName" class="sign-modal__text"></span>
            </div>
            <div class="sign-modal__cert-row">
               <span class="sign-modal__label">Издатель:</span>
               <span id="signIssuerName" class="sign-modal__text"></span>
            </div>
            <div class="sign-modal__cert-row">
               <span class="sign-modal__label">Дата выдачи:</span>
               <span id="signValidFromDate" class="sign-modal__text"></span>
            </div>
            <div class="sign-modal__cert-row">
               <span class="sign-modal__label">Срок действия:</span>
               <span id="signValidToDate" class="sign-modal__text"></span>
            </div>
            <div class="sign-modal__cert-row">
               <span class="sign-modal__label">Статус:</span>
               <span id="signCertMessage" class="sign-modal__text"></span>
            </div>
         </div>

      </div>

      <div id="signActions" class="sign-modal__actions modal-actions" data-displayed="false">
         <div id="signButton" class="modal-actions__button">Подписать</div>
         <div id="signCancel" class="modal-actions__button">Отмена</div>
      </div>
   </div>
</div>
