import { safeMapGetter, safeMapSetter } from '../../lib/main';
import { Form, FormBlock, FormJSON } from './FormBlock';

/**
 * Представляет собой сложную форму
 */
export class CompositeFormBlock extends FormBlock implements Form {

   /**
    * Дочерние фомы
    */
   protected forms: Map<string, Form> = new Map<string, Form>();

   /**
    * Инициализирует дочерние поля исключая поля в дочерних формах
    */
   public initInnerFields(): Form {
      Array.from(this.element.querySelectorAll<HTMLElement>('[data-field]'))
         .filter(field => !field.closest('[data-form-part]'))
         .forEach(field => this.initField(field));

      return this;
   }

   /**
    * Добавляет к форме дочернюю форму
    *
    * @param key - ключ формы
    * @param form - дочерняя форма
    */
   public addForm(key: string, form: Form): void {
      safeMapSetter(this.forms, key, form);
   }

   /**
    * Валидирует форму
    */
   public complete(): boolean {
      const isValid = super.complete();
      const invalidForms = Array.from(this.forms.values()).filter(form => !form.complete());
      return isValid && invalidForms.length === 0;
   }

   /**
    * Определяет, является ли форма валидной
    */
   public isValid(): boolean {
      const isValid = super.isValid();
      const invalidForms = Array.from(this.forms.values()).filter(form => !form.isValid());
      return isValid && invalidForms.length === 0;
   }

   /**
    * Преобразует объект формы в объект в формате JSON
    */
   public toJSON(): FormJSON {
      const formObject = super.toJSON();
      this.forms.forEach((form: Form, key: string) => formObject[key] = form.toJSON());
      return formObject;
   }

   /**
    * Получает дочернюю форму по ключу
    *
    * @param key - ключ для получения
    */
   public getFormByKey(key: string): Form {
      return safeMapGetter(this.forms, key);
   }

}
