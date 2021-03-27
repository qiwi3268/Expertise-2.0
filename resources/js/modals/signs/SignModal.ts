import { mGetByID, mQS } from '../../lib/main';
import { GeFile } from '../../lib/GeFile';
import {
   CertificateInfo,
   SignInfo,
   ValidationResult
} from '../../api/response-handlers/files/InternalSignatureValidationResponseHandler';

export abstract class SignModal {
   /**
    * Модальное окно модуля подписей
    */
   protected readonly modal: HTMLElement;

   /**
    * Блок с результатами проверки подписей
    */
   protected validateInfo: HTMLElement;

   /**
    * Фон модального окна
    */
   protected overlay: HTMLElement;

   /**
    * Создает объект модального окна модуля подписания
    */
   protected constructor() {
      this.modal = mGetByID('signModal');
      this.validateInfo = mGetByID('signValidate');

      this.handleOverlay();
      this.handleCloseButton();
   }

   /**
    * Обрабатывает нажатие на фон модального окна
    */
   protected handleOverlay(): void {
      this.overlay = mGetByID('signOverlay')
      this.overlay.addEventListener('click', () => this.closeModal());
   }

   /**
    * Закрывает модальное окно просмотра подписей
    */
   protected abstract closeModal(): void;

   /**
    * Обрабатывает кнопку закрытия модального окна
    */
   private handleCloseButton(): void {
      const closeButton = mQS('[data-close-button]', this.modal);
      closeButton.addEventListener('click', () => this.closeModal());
   }

   /**
    * Открывает модуль подписей
    *
    * @param geFile - файл, для которого открывается модуль подписей
    */
   public abstract open(geFile: GeFile): void;

   /**
    * Добвляет файл в модальное окно модуля просмотра подписей
    *
    * @param geFile - файл, который добавляется в модальное окно
    */
   protected addFileElement(geFile: GeFile): void {

      const signFile = mGetByID('signFile');

      const geFileName = geFile.getName();

      const fileIcon = document.createElement('I');
      fileIcon.classList.add('files__icon', 'fas', GeFile.getFileIconClass(geFileName));

      const fileDescription = document.createElement('DIV');
      fileDescription.classList.add('files__description');

      const fileName: HTMLElement = document.createElement('DIV');
      fileName.classList.add('files__name');
      fileName.textContent = geFileName;

      const geFileSize = geFile.getSizeString();
      const fileSize: HTMLElement = document.createElement('DIV');
      fileSize.classList.add('files__size');
      fileSize.textContent = geFileSize;

      fileDescription.appendChild(fileName);
      fileDescription.appendChild(fileSize);

      signFile.appendChild(fileIcon);
      signFile.appendChild(fileDescription);

   }

   /**
    * Добавляет результаты проверки подписей в модальное окно
    * просмотра подписей
    *
    * @param validationResults - результаты проверки подписей файла
    */
   public fillSignsInfo(validationResults: ValidationResult) {
      this.validateInfo.dataset.displayed = 'true';
      this.validateInfo.textContent = '';

      validationResults.signers.forEach(result => {
         this.validateInfo.appendChild(SignModal.createSignInfo(result))
      });
   }

   /**
    * Создает блок в с информацией о подписи
    *
    * @param result - объект, содержащий информацию о подписи
    * @returns блок, в который добавлена информация о подписи
    */
   private static createSignInfo(result: SignInfo): HTMLElement {
      const sign: HTMLElement = document.createElement('DIV');
      sign.classList.add('sign-modal__sign');

      const certRow: HTMLElement = SignModal.createInfoRow('Сертификат:', result.certificateMessage);
      certRow.setAttribute('data-state', String(result.certificateResult));

      const signRow: HTMLElement = SignModal.createInfoRow('Подпись:', result.signatureMessage);
      signRow.setAttribute('data-state', String(result.signatureResult));

      const nameRow: HTMLElement = SignModal.createInfoRow('Подписант:', result.fio);

      const certInfoBlock: HTMLElement = SignModal.createCertInfoBlock(result.certificate);

      sign.appendChild(certRow);
      sign.appendChild(signRow);
      sign.appendChild(nameRow);
      sign.appendChild(certInfoBlock);

      return sign;
   }

   /**
    * Создает поле блока с информацией о подписи
    *
    * @param label - имя поля блока с информацией о подписи
    * @param text - содержимое поля блока с информацией о подписи
    * @returns элемент строки блока с информацией о подписи
    */
   private static createInfoRow(label: string, text: string): HTMLElement {
      const row = document.createElement('DIV');
      row.classList.add('sign-modal__sign-row');

      const labelElem = document.createElement('SPAN');
      labelElem.classList.add('sign-modal__label');
      labelElem.textContent = label;

      const textElem = document.createElement('SPAN');
      textElem.classList.add('sign-modal__text');
      textElem.textContent = text;

      row.appendChild(labelElem);
      row.appendChild(textElem);

      return row;
   }

   /**
    * Создает элемент с информацией о сертификате подписанта
    *
    * @param certificate - информация о сертификате
    */
   private static createCertInfoBlock(certificate: CertificateInfo): HTMLElement {
      const certInfo: HTMLElement = document.createElement('DIV');

      const serialRow: HTMLElement = SignModal.createInfoRow('Серийный номер:', certificate.serial);
      const issuerRow: HTMLElement = SignModal.createInfoRow('Издатель:', certificate.issuer);

      const dateIntervalRow: HTMLElement = SignModal.createInfoRow('Срок действия:', certificate.validRange);

      certInfo.appendChild(serialRow);
      certInfo.appendChild(issuerRow);
      certInfo.appendChild(dateIntervalRow);

      return certInfo;
   }

}
