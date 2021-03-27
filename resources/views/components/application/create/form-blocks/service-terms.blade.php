<div class="application-form__card form-card closed"
     data-form
     data-card
     data-name="serviceTerms"
>
   <div class="form-card__header" data-card-header>
      <i class="form-card__icon-valid fas fa-check-circle"></i>
      <i class="form-card__icon-invalid fas fa-exclamation-circle"></i>
      <div class="form-card__title">Согласие с условиями предоставления услуги</div>
   </div>

   <div class="form-card__body column-form" data-card-body>

      <x-forms.yes-radio-block
          title="Обязуюсь сообщать обо всех изменениях, связанных с представленными в настоящем заявлении документами и сведениями"
          name="isReportChanges"
          required="true"
      />
      <x-forms.yes-radio-block
          title="Я ознакомлен с правилами предоставления услуги и как заявитель несу ответственность за полноту и достоверность предоставленных сведений"
          name="isKnowsRules"
          required="true"
      />
      <x-forms.yes-radio-block
          title="Подтверждаю, что переданная мной проектная документация и(или) результаты инженерных изысканий не содержат сведений, доступ к которым ограничен в соответствии с законодательством Российской Федерации"
          name="isNotRestrictedInformation"
          required="true"
      />
      <x-forms.yes-radio-block
          title="Гарантирую оплату вне зависимости от результатов экспертизы в соответствии с условиями договора"
          name="isGuaranteedPayment"
          required="true"
      />
      <x-forms.yes-radio-block
          title="Согласен на обработку персональных данных"
          name="isAgreedProcessingPersonalData"
          required="true"
      />
      <x-forms.yes-radio-block
          title='Согласен на передачу информации Оператору ГИС "ЕГРЗ" в соответствии с законодательством'
          name="isAgreedSendInformationToEgrz"
          required="true"
      />

      <div class="form-card__subtitle">
         <div class="form-card__label">Условия ведения договорных отношений</div>
      </div>
      <x-forms.yes-radio-block
          title="Получить финансовые документы только в электронном виде"
          name="isElectronicFinancialDocuments"
          required="true"
      />
      <x-miscs.single
          title="Вид договора"
          alias="contractType"
          required="true"
      />
      <x-miscs.radio-single
          title="Реквизиты договора"
          alias="contractPreparationType"
          required="true"
          multiple="false"
      />
      <x-forms.inputs.ikz
          title="Укажите ИКЗ"
          name="ikz"
          required="false"
      />
      <div class="form-card__subtitle">
         <div class="form-card__label">Сокращенный срок оказания услуги по выбору заявителя</div>
      </div>
      <x-forms.yes-radio-block
          title="Провести услугу в 30 рабочих дней"
          name="isReducedTerm"
          required="false"
      />

      <div class="form-card__subtitle">
         <div class="form-card__label">Уведомления по e-mail</div>
      </div>
      <x-forms.inputs.input
          title="Адрес электронной почты"
          name="email"
          required="true"
      />
      <x-forms.yes-radio-block
          title="Согласен получать информационные сообщения по работе учреждения на электронный адрес"
          name="isAgreedReceiveEmail"
          required="true"
      />

   </div>
</div>
