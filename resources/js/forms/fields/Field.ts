import {
   addDataAttrMutationHandler,
   addFieldMutationHandler,
   isDisplayedElement,
   mQS,
   safeDataAttrGetter
} from '../../lib/main';
import { FieldTypes } from '../blocks/FormBlock';

/**
 * Состояния поля
 */
export enum FieldStates {
   Valid = 'valid',
   Invalid = 'invalid',
   InProcess = 'inProcess'
}

/**
 * Представляет собой поле формы
 */
export abstract class Field {

   /**
    * Элемент поля
    */
   protected readonly element: HTMLElement;

   /**
    * Инпут со значением поля
    */
   protected resultInput: HTMLInputElement;

   /**
    * Блок с сообщение об ошибке
    */
   protected errorElement: HTMLElement;

   /**
    * Тип поля
    */
   protected type: FieldTypes;

   /**
    * Валидно ли поле
    */
   protected valid: boolean;

   /**
    * Наименование поля
    */
   protected readonly name: string;

   public constructor(element: HTMLElement) {

      this.element = element;
      this.name = safeDataAttrGetter('name', this.element);

      this.resultInput = mQS('[data-field-result]', this.element);
      this.errorElement = mQS('[data-field-error]', this.element);

      this.type = safeDataAttrGetter('type', this.element) as FieldTypes;

      this.initValidation();
   }

   /**
    * Инициализирует валидацию поля
    */
   protected initValidation(): void {
      addFieldMutationHandler(this.element, this.complete.bind(this));

      addDataAttrMutationHandler(this.element, 'required', () => {
         if (!this.valid && !this.isRequired()) {
            this.element.removeAttribute('data-state');
         }
      });
   }

   /**
    * Определяет, является ли поле валидным
    */
   public validate(): boolean {

      if (!this.hasValue()) {
         this.valid = !this.isRequired();
      } else {
         this.valid = true;
      }

      return this.valid;
   }

   /**
    * Валидирует поле и устанавливает состояние
    */
   public complete(): boolean {

      this.validate();

      if (this.valid) {
         this.hasValue() ? this.setValidState() : this.setDefaultState();
      } else {
         this.setInvalidState();
      }

      return this.valid;
   }

   /**
    * Добавляет действие при изменении значения,
    * определенное родительским полем
    *
    * @param action - колбэк изменения значения
    */
   public addValueChangeAction(action: Function): void {
      addFieldMutationHandler(this.element, action);
   }

   /**
    * Добавляет действие при изменении обязательности,
    * определенное родительским полем
    *
    * @param action - колбэк изменения обязательности
    */
   public addRequiredChangeAction(action: Function): void {
      addDataAttrMutationHandler(this.element, 'required', action);
   }

   /**
    * Определяет, является ли поле обязательным
    */
   protected isRequired(): boolean {
      return this.element.dataset.required === 'true';
   }

   /**
    * Получает элемент поля
    */
   public getElement(): HTMLElement {
      return this.element;
   }

   /**
    * Получает значение поля
    */
   public getValue(): string {
      return this.resultInput.value;
   }

   /**
    * Определяет, содержит ли поле значение
    */
   public hasValue(): boolean {
      return this.getValue() !== '';
   }

   /**
    * Устанавливает значение поля
    *
    * @param value - новое значение
    */
   public setValue(value: string): void {
      this.resultInput.value = value;
   }

   /**
    * Получает наименование поля
    */
   public getName(): string {
      return this.name;
   }

   /**
    * Получает тип поля
    */
   public getType(): FieldTypes {
      return this.type;
   }

   /**
    * Определяет, существует ли поле на странице
    */
   public isDisplayed(): boolean {
      return isDisplayedElement(this.element);
   }

   /**
    * Устанавливает состояние по умолчанию
    */
   protected setDefaultState(): void {
      this.element.removeAttribute('data-state');
   }

   /**
    * Устанавливает валидное состояние
    */
   protected setValidState(): void {
      this.element.dataset.state = FieldStates.Valid;
   }

   /**
    * Устанавливает невалидное состояние
    */
   protected setInvalidState(): void {
      this.element.dataset.state = FieldStates.Invalid;
   }

}
