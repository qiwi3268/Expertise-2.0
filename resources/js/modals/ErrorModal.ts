import { mGetByID, mQS } from '../lib/main';

/**
 * Представляет собой модальное окно с ошибкой
 * для отображения пользователю
 */
export class ErrorModal {

   /**
    * Инстанс модального окна с ошибкой
    */
   private static instance: ErrorModal;

   /**
    * Модальное окно с ошибкой
    */
   private readonly modal: HTMLElement;

   /**
    * Фон модального окна
    */
   private overlay: HTMLElement;

   /**
    * Блок с заголовком модального окна с ошибкой
    */
   private title: HTMLElement;

   /**
    * Блок с сообщениями ошибок
    */
   private messages: HTMLElement;

   /**
    * Блок с кодом ошибки
    */
   private code: HTMLElement;

   private static getInstance(): ErrorModal {

      if (!this.instance) {
         this.instance = new ErrorModal();
      }

      return this.instance;
   }

   private constructor() {
      this.modal = mGetByID('errorModal');
      this.overlay = mGetByID('errorOverlay');
      this.overlay.addEventListener('click', () => this.close());

      const closeButton = mQS('[data-modal-close]', this.modal);
      closeButton.addEventListener('click', () => this.close());

      this.title = mGetByID('errorTitle');
      this.messages = mGetByID('errorMessages');
      this.code = mGetByID('errorCode');
   }

   /**
    * Закрывает модальное окно с ошибкой
    */
   private close(): void {
      this.modal.setAttribute('data-opened', 'false');
      this.overlay.setAttribute('data-opened', 'false');
   }

   /**
    * Открывает модальное окно с ошибкой
    *
    * @param title - заголовок ошибки
    * @param message - сообщение с ошибкой
    * @param code - код ошибки, если техническая ошибка
    */
   public static open(title: string, message: string | string[], code?: number | string): void {
      const instance: ErrorModal = this.getInstance();
      instance.modal.setAttribute('data-opened', 'true');
      instance.overlay.setAttribute('data-opened', 'true');

      instance.title.textContent = title;
      instance.messages.textContent = '';

      const messages: string[] = Array.isArray(message) ? message : [message];
      messages.forEach(message => instance.appendMessageRow(message));

      if (code) {
         instance.code.textContent = 'Техническая ошибка. Обратитесь к администратору с кодом ошибки: ' + code;
         instance.code.style.display = 'block';
      } else {
         instance.code.style.display = 'none';
      }

   }

   /**
    * Создает элемент части сообщения
    *
    * @param messagePart - часть сообщения
    */
   private appendMessageRow(messagePart: string): void {
      const messageRow: HTMLElement = document.createElement('DIV');
      messageRow.classList.add('error-modal__message');
      messageRow.textContent = messagePart;

      this.messages.appendChild(messageRow);
   }
}
