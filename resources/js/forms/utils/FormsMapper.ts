import { Form, StringForm } from '../blocks/FormBlock';
import { LogicError } from '../../lib/LogicError';
import { Field } from '../fields/Field';

/**
 * Опции маппера форм
 */
export type FormsMapperOptions = {
   ignore?: string[],
   replace?: {
      [sourceFieldName: string]: string
   }
}

/**
 * Вспомогательный класс для маппинга форм
 */
export class FormsMapper {

   /**
    * Копирует значения из одной формы в другую
    *
    * @param targetForm - форма, в которую копируют
    * @param sourceForm - форма, которая копируется
    * @param options - опции маппера формы
    */
   public static copyFromStringForm(targetForm: Form, sourceForm: StringForm, options?: FormsMapperOptions) {
      if (options !== undefined) {
         sourceForm = FormsMapper.transformSourceStringForm(sourceForm, options);
      }

      new Map(Object.entries(sourceForm)).forEach((value: string | null, name: string) => {
         if (value !== null && targetForm.hasField(name)) {
            const field: Field = targetForm.getFieldByName(name);
            field.setValue(value);
         }
      });

   }

   /**
    * Преобразует форму, которая копируется, чтобы она подходила для копирования
    *
    * @param form - копируемая форма
    * @param options - опции маппера формы
    */
   private static transformSourceStringForm(form: StringForm, options: FormsMapperOptions): StringForm {
      let transformedForm: StringForm = form;

      if (options.ignore !== undefined) {

         const ignoredFields: string[] = options.ignore;

         transformedForm = Object.fromEntries(
            Object.entries(form).filter((entry: [string, string | null]) => !ignoredFields.includes(entry[0]))
         );

      }

      if (options.replace !== undefined) {

         Object.entries(options.replace).forEach((entry) => {

            const originalFieldName = entry[0];
            const transformedFieldName = entry[1];

            if (transformedForm.hasOwnProperty(transformedFieldName)) {
               new LogicError(`Заменяемое поле присутствует в форме: ${transformedFieldName}`);
            }

            if (transformedForm.hasOwnProperty(originalFieldName)) {
               const fieldValue: string | null = transformedForm[originalFieldName];
               delete transformedForm[originalFieldName];

               transformedForm[transformedFieldName] = fieldValue;
            }
         })

      }

      return transformedForm;
   }


}
