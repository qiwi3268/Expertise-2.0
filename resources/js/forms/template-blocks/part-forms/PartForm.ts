import { Form, FormBlock, FormJSON } from '../../blocks/FormBlock';
import { Field } from '../../fields/Field';
import { safeDataAttrGetter } from '../../../lib/main';
import { TemplatePart } from '../parts/TemplatePart';

/**
 * Представляет собой форму части шаблонного блока
 */
export abstract class PartForm<T extends TemplatePart = TemplatePart> {

   /**
    * Объект формы
    */
   protected form: Form;

   /**
    * Главное поле части
    */
   protected mainField: Field;

   /**
    * Элемент родительской части
    */
   protected partElement: HTMLElement;

   /**
    * Родительская части
    */
   protected parentPart: T;

   /**
    * Инициализирует форму
    *
    * @param parentPart - родительская часть
    */
   public init(parentPart: T): void {
      this.parentPart = parentPart;
      this.partElement = this.parentPart.getElement();

      this.initFormElements();
   }

   /**
    * Инициализирует элементы формы
    */
   protected initFormElements(): void {
      this.form = new FormBlock(this.partElement);
      this.form.initInnerFields();

      const mainFieldName: string = safeDataAttrGetter('partMainField', this.partElement);
      this.mainField = this.form.getFieldByName(mainFieldName);
   }

   /**
    * Валидирует форму
    */
   public complete(): boolean {
      return this.form.complete();
   }

   /**
    * Определяет, является ли форма валидной
    */
   public isValid(): boolean {
      return this.form.isValid();
   }

   /**
    * Получает форму в формате JSON
    */
   public toJSON(): FormJSON {
      return this.form.toJSON();
   }

   /**
    * Получает объект формы
    */
   public getForm(): Form {
      return this.form;
   }

}
