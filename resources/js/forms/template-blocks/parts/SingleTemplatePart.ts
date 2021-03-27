import { TemplatePart } from './TemplatePart';
import { SingleTemplateBlock } from '../blocks/SingleTemplateBlock';
import { Form, FormJSON } from '../../blocks/FormBlock';
import { TemplateBlock } from '../blocks/TemplateBlock';
import { PartForm } from '../part-forms/PartForm';

/**
 * Представляет собой шаблонную часть блока с единственной частью
 */
export class SingleTemplatePart extends TemplatePart<SingleTemplateBlock> {

   public constructor(templateBlock: TemplateBlock, partForm: PartForm) {
      super(templateBlock as SingleTemplateBlock, partForm);
   }

   /**
    * Сохраняет часть
    */
   protected save(): void {

      if (!this.isSaved()) {
         this.templateBlock.savePart(this);
         this.isSavedForm = true;
         this.cancelButton!.remove();
         this.cancelButton = null;
      }

      this.savedForm = this.partForm.toJSON();
      this.templateBlock.setIsChanged(true);
   }

   /**
    * Отменяет создание части
    */
   protected cancel(): void {
      this.element.remove();
      this.templateBlock.showAddPartButton();
   }

   /**
    * Валидирует форму части
    */
   protected complete(): boolean {
      return this.partForm.complete();
   }

   /**
    * Определяет, валидна ли форма части
    */
   public isValid(): boolean {
      return this.partForm.isValid();
   }

   /**
    * Получает часть в формати JSON
    */
   public toJSON(): FormJSON {
      return this.savedForm;
   }


}
