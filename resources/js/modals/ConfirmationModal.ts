import { mGetByID } from '../lib/main';

/**
 * Представляет собой модальное окно
 * для подтверждения какого-либо действия
 */
export class ConfirmationModal {

   /**
    * Инстанс модального окна подтверждения
    */
   private static instance: ConfirmationModal;

   /**
    * Элемент модального окна
    */
   private readonly modal: HTMLElement;

   /**
    * Фон модального окна
    */
   private overlay: HTMLElement;

   /**
    * Сообщение с вопросом о подтверждении действия
    */
   private text: HTMLElement;

   /**
    * Кнопка для подтверждения действия
    */
   private confirmButton: HTMLElement;

   /**
    * Кнопка для отмены действия
    */
   private cancelButton: HTMLElement;

   /**
    * Колбэк выполнения действия
    */
   private confirm: EventListenerOrEventListenerObject

   /**
    * Колбэк отмены действия
    */
   private cancel: EventListenerOrEventListenerObject;

   public static getInstance(): ConfirmationModal {

      if (!this.instance) {
         this.instance = new ConfirmationModal();
      }

      return this.instance;
   }

   private constructor() {
      this.modal = mGetByID('confirmationModal');
      this.text = mGetByID('confirmationText');
      this.confirmButton = mGetByID('confirmationOk');
      this.cancelButton = mGetByID('confirmationCancel');

      this.overlay = mGetByID('confirmationOverlay');
   }

   /**
    * Закрывает модальное окно подтверждения
    */
   private closeModal(): void {
      this.modal.setAttribute('data-opened', 'false');
      this.overlay.setAttribute('data-opened', 'false');

      this.cancelButton.removeEventListener('click', this.cancel);
      this.confirmButton.removeEventListener('click', this.confirm);

      this.cancel = () => {
      };

      this.confirm = () => {
      };
   }

   /**
    * Открывает модальное окно подтверждения
    *
    * @param message - сообщение с вопросом о подтверждении действия
    * @param confirmAction - колбэк выполнения действия
    * @param cancelAction - колбэк отмены действия
    */
   public open(message: string | string[], confirmAction: Function, cancelAction: Function = this.emptyAction): void {

      this.modal.setAttribute('data-opened', 'true');
      this.overlay.setAttribute('data-opened', 'true');

      this.text.textContent = '';

      const messages: string[] = Array.isArray(message) ? message : [message];
      messages.forEach((messagePart: string) => this.text.appendChild(this.createMessagePart(messagePart)));

      this.confirm = () => {
         confirmAction();
         this.closeModal();
      }

      this.cancel = () => {
         cancelAction();
         this.closeModal();
      }

      this.confirmButton.addEventListener('click', this.confirm, {once: true});
      this.cancelButton.addEventListener('click', this.cancel, {once: true});
   }

   /**
    * Создает элемент части сообщения
    *
    * @param partStr - часть сообщения
    */
   private createMessagePart(partStr: string): HTMLElement {
      const messagePart: HTMLElement = document.createElement('DIV');
      messagePart.classList.add('confirmation-modal__row');
      messagePart.textContent = partStr;
      return messagePart;
   }

   /**
    * Пустое действие для отмены по умолчанию
    */
   private emptyAction(): void {
   }

}

