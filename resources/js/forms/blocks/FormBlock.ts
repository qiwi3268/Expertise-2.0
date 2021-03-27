import { Field } from '../fields/Field';
import { isDisplayedElement, safeMapGetter } from '../../lib/main';
import { LogicError } from '../../lib/LogicError';
import { InputField } from '../fields/InputField';
import { MiscField } from '../fields/MiscField';
import { FileField } from '../fields/FileField';
import { DateField } from '../fields/DateField';
import { RadioField } from '../fields/RadioField';
import { DadataInputField } from '../fields/DadataInputField';

/**
 * Типы полей
 */
export enum FieldTypes {
   Input = 'input',
   Misc = 'misc',
   Radio = 'radio',
   Date = 'date',
   File = 'file',
   MultipleInput = 'multipleInput',
}

/**
 * Описывает объект формы
 */
export interface Form {

   /**
    * Инициализирует дочерние поля
    */
   initInnerFields(): Form;

   /**
    * Добавляет к форме поле
    *
    * @param field - элемент поля
    */
   addField(field: HTMLElement): Field;

   /**
    * Добавляет к форме дочернюю форму
    *
    * @param key - ключ формы
    * @param form - дочерняя форма
    */
   addForm(key: string, form: Form): void;

   /**
    * Определяет, содержит ли форма поле с указанным наименованием
    *
    * @param name - наименование поля
    */
   hasField(name: string): boolean;

   /**
    * Получает поле формы по наименование
    *
    * @param name - наименование поля
    */
   getFieldByName(name: string): Field;

   /**
    * Получает дочернюю форму по ключу
    *
    * @param key - ключ для получения
    */
   getFormByKey(key: string): Form;

   /**
    * Валидирует форму
    */
   complete(): boolean;

   /**
    * Определяет, является ли форма валидной
    */
   isValid(): boolean;

   /**
    * Преобразует объект формы в объект в формате JSON
    */
   toJSON(): FormJSON;
}

/**
 * Объект формы в формате JSON
 */
export type FormJSON = {
   [fieldName: string]: string | boolean | FormJSON | FormJSON[];
}

/**
 * Объект строковой формы
 */
export type StringForm = {
   [fieldName: string]: string | null;
}

/**
 * Представляет собой простую форму
 */
export class FormBlock implements Form {

   /**
    * Элемент формы
    */
   protected element: HTMLElement;

   /**
    * Поля формы
    */
   protected fields: Field[] = [];

   /**
    * Элементы полей формы
    */
   protected fieldElements: HTMLElement[] = [];

   public constructor(element: HTMLElement) {
      this.element = element;
   }

   /**
    * Инициализирует дочерние поля
    */
   public initInnerFields(): Form {
      this.element.querySelectorAll<HTMLElement>('[data-field]')
         .forEach(field => this.initField(field));

      return this;
   }

   /**
    * Инициализирует и возвращает поле
    *
    * @param field - элемент поля
    */
   protected initField(field: HTMLElement): Field {
      const formField: Field = this.createField(field);
      this.fields.push(formField);
      return formField;
   }

   /**
    * Создает объект поля
    *
    * @param fieldElement - элемент
    */
   public createField(fieldElement: HTMLElement): Field {
      let formField: Field;

      switch (fieldElement.dataset.type) {
         case FieldTypes.Input:
            formField = fieldElement.hasAttribute('data-dadata')
               ? new DadataInputField(fieldElement, this)
               : new InputField(fieldElement);
            break;
         case FieldTypes.Misc:
            formField = new MiscField(fieldElement);
            break;
         case FieldTypes.File:
            formField = new FileField(fieldElement);
            break;
         case FieldTypes.Date:
            formField = new DateField(fieldElement);
            break;
         case FieldTypes.Radio:
            formField = new RadioField(fieldElement);
            break;
         /*     case FieldTypes.MultipleInput:
                 formField = new MultipleInputField(fieldElement, this.createInputPart.bind(this));
                 break;*/
         default:
            new LogicError('Не определен тип поля');
      }

      return formField!;
   }

   /**
    * Возвращает заполненные поля
    */
   public getFilledFields(): Field[] {
      return this.fields.filter((field: Field) => field.hasValue());
   }

   /**
    * Возвращает все поля
    */
   public getFields(): Field[] {
      return this.fields;
   }

   /**
    * Определяет, существует ли форма на странице
    */
   public isDisplayed(): boolean {
      return isDisplayedElement(this.element);
   }

   /**
    * Добавляет к форме поле
    *
    * @param field - элемент поля
    */
   public addField(field: HTMLElement): Field {
      return this.initField(field);
   }

   /**
    * Валидирует форму
    */
   public complete(): boolean {
      const invalidFields: Field[] = this.fields
         .filter(field => isDisplayedElement(field.getElement()))
         .filter(field => !field.complete())

      return invalidFields.length === 0;
   }

   /**
    * Определяет, является ли форма валидной
    */
   public isValid(): boolean {
      return this.fields
         .filter(field => isDisplayedElement(field.getElement()))
         .every(field => field.validate());
   }

   /**
    * Преобразует объект формы в объект в формате JSON
    */
   public toJSON(): FormJSON {
      const formObject: FormJSON = {};
      this.fields
         .filter(field => field.getType() !== FieldTypes.File)
         .forEach(field => formObject[field.getName()] = field.isDisplayed() ? field.getValue() : '');
      return formObject;
   }

   /**
    * Определяет, содержит ли форма поле с указанным наименованием
    *
    * @param name - наименование поля
    */
   public hasField(name: string): boolean {
      return this.fields.find(field => field.getName() === name) !== undefined;
   }

   /**
    * Получает поле формы по наименование
    *
    * @param name - наименование поля
    */
   public getFieldByName(name: string): Field {
      const field: Field | undefined = this.fields.find(field => field.getName() === name);
      if (field === undefined) {
         new LogicError(`Не найдено в форме поле с именем: ${name}`);
      }
      return field!;
   }

   /**
    * Заглушка для реализации паттерна Composite
    */
   public addForm(name: string, form: Form): void {
      throw new LogicError('Невозможно добавление формы в простую форму');
   }

   /**
    * Заглушка для реализации паттерна Composite
    */
   public getFormByKey(key: string): Form {
      throw new LogicError('Невозможно получение формы по ключу в простой форме');
   }
}




