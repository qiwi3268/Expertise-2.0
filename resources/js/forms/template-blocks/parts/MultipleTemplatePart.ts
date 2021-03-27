import { TemplatePart } from './TemplatePart';
import { TemplateBlock } from '../blocks/TemplateBlock';
import { addFieldMutationHandler, mQS } from '../../../lib/main';
import { Form, FormJSON } from '../../blocks/FormBlock';
import { PartForm } from '../part-forms/PartForm';
import { MultipleTemplateBlock } from '../blocks/MultipleTemplateBlock';

/**
 * Типы отображения части
 */
export enum DisplayMode {
   Row = 'row',
   Form = 'form',
}

/**
 * Представляет собой шаблонную часть блока с множеством частей
 */
export class MultipleTemplatePart extends TemplatePart<MultipleTemplateBlock> {

   /**
    * Заголовок части
    */
   protected header: HTMLElement;

   /**
    * Метка отображаемая в заголовке, которая описывает часть
    */
   protected label: string;

   /**
    * Элемент описания части
    */
   protected labelElement: HTMLElement;

   public constructor(templateBlock: TemplateBlock, partForm: PartForm) {
      super(templateBlock as MultipleTemplateBlock, partForm);
      this.header = mQS('[data-part-header]', this.element);
   }

   /**
    * Устанавливает значение в заголовке части
    *
    * @param value - значение для отображения
    */
   public setPartLabel(value: string): void {
      this.label = value;
      this.header.textContent = this.label;

      if (this.labelElement) {
         this.labelElement.textContent = this.label;
      }
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
         this.initRowView();
      }

      this.savedForm = this.partForm.toJSON();
      this.templateBlock.setIsChanged(true);
      this.changeDisplayMode(DisplayMode.Row);
   }

   /**
    * Инициализирует отображение части в виде строки
    */
   protected initRowView(): void {

      const rowView = this.getRowView();
      this.labelElement = mQS('[data-part-label]', rowView);
      this.labelElement.textContent = this.label;
      this.labelElement.addEventListener('click', () => this.changeDisplayMode(DisplayMode.Form));

      const deleteButton = mQS('[data-part-delete]', rowView);
      deleteButton.addEventListener('click', () => this.delete());
   }

   /**
    * Меняет тип отображения части
    *
    * @param displayMode - тип отображения
    */
   public changeDisplayMode(displayMode: DisplayMode): void {
      const partFormView: HTMLElement = this.getFormView();
      const partRowView: HTMLElement = this.getRowView();
      partFormView.dataset.hidden = (partFormView.dataset.partView !== displayMode).toString();
      partRowView.dataset.hidden = (partRowView.dataset.partView !== displayMode).toString();
   }

   /**
    * Получает элемент строкового отображения части
    */
   protected getRowView(): HTMLElement {
      return mQS(`[data-part-view="${DisplayMode.Row}"]`, this.element);
   }

   /**
    * Получает элемент отображения части в виде формы
    */
   protected getFormView(): HTMLElement {
      return mQS(`[data-part-view="${DisplayMode.Form}"]`, this.element);
   }

   /**
    * Удаляет часть
    */
   protected delete(): void {
      this.templateBlock.deletePart(this);
      this.element.remove();
   }

   /**
    * Валидирует часть
    */
   protected complete(): boolean {
      return this.partForm.complete();
   }

   /**
    * Определяет, является ли часть валидной
    */
   public isValid(): boolean {
      return this.partForm.isValid();
   }

   /**
    * Получает часть в формате JSON
    */
   public toJSON(): FormJSON {
      return this.savedForm;
   }

}
