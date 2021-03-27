import { addDataAttrMutationHandler, mQS, safeDataAttrGetter } from '../../lib/main';
import { FieldStates, Field } from '../fields/Field';
import { FormBlock } from './FormBlock';

/**
 * Представляет собой часть анкеты заявления
 */
export class ApplicationFormBlock extends FormBlock {

   /**
    * Наименование формы
    */
   private readonly name;

   public constructor(element: HTMLElement) {

      super(element);

      this.name = safeDataAttrGetter('name', this.element);
      this.observeDependencies();
   }

   /**
    * Инициализирует и возвращает поле
    *
    * @param field - элемент поля
    */
   protected initField(field: HTMLElement): Field {
      const formField: Field = this.createField(field);
      this.fields.push(formField);

      formField.addValueChangeAction(this.handleFieldChange.bind(this));
      formField.addRequiredChangeAction(this.handleFieldChange.bind(this));

      return formField;
   }

   /**
    * Обрабатывает изменение дочернего поля
    */
   public handleFieldChange(): void {

      if (this.isValid()) {
         this.element.dataset.state = FieldStates.Valid;
      } else {
         this.element.dataset.state = FieldStates.InProcess;
      }

   }

   /**
    * Следит за изменением отображения внутренних блоков
    */
   protected observeDependencies(): void {
      const displayBlocks: NodeListOf<HTMLElement> = this.element.querySelectorAll('[data-display-block]');
      displayBlocks.forEach(block => {
         addDataAttrMutationHandler(block, 'displayed', this.handleFieldChange.bind(this));
      });
   }

   /**
    * Получает наименование формы
    */
   public getName(): string {
      return this.name;
   }
}
