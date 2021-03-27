import { mQS } from '../../../lib/main';
import { ErrorModal } from '../../../modals/ErrorModal';
import { TemplateBlock } from '../blocks/TemplateBlock';
import { Form, FormJSON } from '../../blocks/FormBlock';
import { PartForm } from '../part-forms/PartForm';

/**
 * Представляет собой шаблонную часть блока с шаблонами
 */
export abstract class TemplatePart<T extends TemplateBlock = TemplateBlock> {

   /**
    * Родительский шаблонный блок
    */
   protected templateBlock: T;

   /**
    * Элемент части
    */
   protected element: HTMLElement;

   /**
    * Кнопка сохранения части
    */
   protected saveButton: HTMLElement;

   /**
    * Кнопка отмены создания части
    */
   protected cancelButton: HTMLElement | null;

   /**
    * Элемент, в котором находится форма части
    */
   protected body: HTMLElement;

   /**
    * Флаг, указывающий, сохранена ли часть
    */
   protected isSavedForm: boolean;

   /**
    * Форма части
    */
   protected partForm: PartForm;

   /**
    * Форма части в формате JSON
    */
   protected savedForm: FormJSON;

   protected constructor(templateBlock: T, partForm: PartForm) {
      this.templateBlock = templateBlock;
      this.element = templateBlock.createBlock(this.templateBlock.getBody(), '[data-template-part]');
      this.body = mQS('[data-part-body]', this.element);

      this.handleCancelButton();
      this.handleSaveButton();

      this.partForm = partForm;
      this.partForm.init(this);
   }

   /**
    * Обрабатывает кнопку отмены создания части
    */
   protected handleCancelButton(): void {
      this.cancelButton = mQS('[data-part-cancel]', this.element) as HTMLElement;
      this.cancelButton.addEventListener('click', () => this.cancel());
   }

   /**
    * Отменяет создание части
    */
   protected cancel(): void {
      this.element.remove();
   }

   /**
    * Обрабатывает кнопку отмены сохранения
    */
   protected handleSaveButton(): void {
      this.saveButton = mQS('[data-part-save]', this.element);
      this.saveButton.addEventListener('click', () => this.handleForm());
   }

   /**
    * Обрабатывает форму части
    */
   protected handleForm(): void {

      if (this.complete()) {
         this.save();
      } else {
         ErrorModal.open(
            'Ошибка при сохранении блока',
            'Не заполнены обязательные поля'
         );
      }

   }

   /**
    * Сохраняет часть
    */
   protected abstract save(): void;

   /**
    * Получает часть в формате JSON
    */
   public abstract toJSON(): FormJSON;

   /**
    * Определяет, сохранена ли часть
    */
   public isSaved(): boolean {
      return this.isSavedForm;
   }

   /**
    * Определяет, валидна ли форма части
    */
   public abstract isValid(): boolean;

   /**
    * Валидирует форму части
    */
   protected abstract complete(): boolean;

   /**
    * Получает элемент части
    */
   public getElement(): HTMLElement {
      return this.element;
   }

   /**
    * Получает родительский шаблонный блок
    */
   public getParentBlock(): T {
      return this.templateBlock;
   }

   /**
    * Получает элемент с формой части
    */
   public getBody(): HTMLElement {
      return this.body;
   }

}
