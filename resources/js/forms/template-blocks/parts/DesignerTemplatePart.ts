import { DisplayMode, MultipleTemplatePart } from './MultipleTemplatePart';
import { ErrorModal } from '../../../modals/ErrorModal';
import { TemplateBlock } from '../blocks/TemplateBlock';
import { mQS } from '../../../lib/main';

/**
 * Представляет собой шаблонную часть исполнителя в анкете заявления
 */
export class DesignerTemplatePart extends MultipleTemplatePart {

   /**
    * Внутренний шаблонный блок с выписками СРО
    */
   protected extracts: TemplateBlock;

   /**
    * Обрабатывает форму части
    */
   protected handleForm(): void {

      if (!this.complete()) {
         ErrorModal.open(
            'Ошибка при сохранении проектировщика',
            'Не заполнены обязательные поля'
         );
      } else if (this.isNeedExtracts() && !this.extracts.isValid()) {
         ErrorModal.open(
            'Ошибка при сохранении проектировщика',
            'Не добавлены "Выписки из реестра членов саморегулируемой организации"'
         );
      } else {
         this.save();
         this.extracts.setIsChanged(false);
      }

   }

   /**
    * Определяет, обязательны ли в форме выписки СРО
    */
   protected isNeedExtracts (): boolean {
      return this.partForm.getForm().getFieldByName('isRequiredExtract').getValue() === '1';
   }

   /**
    * Получает селектор этой части, чтобы игнорировать
    * внутренние элементы из блока выписок СРО
    */
   protected getPartSelector(): string {
      return `[data-parent-part="${this.templateBlock.getType()}"]`;
   }

   /**
    * Получает элемент строкового отображения части
    */
   protected getRowView(): HTMLElement {
      return mQS(`[data-part-view="${DisplayMode.Row}"]${this.getPartSelector()}`, this.element);
   }

   /**
    * Получает элемент отображения части в виде формы
    */
   protected getFormView(): HTMLElement {
      return mQS(`[data-part-view="${DisplayMode.Form}"]${this.getPartSelector()}`, this.element);
   }

   /**
    * Получает шаблонный блок выписок СРО
    */
   public getExtracts(): TemplateBlock {
      return this.extracts;
   }

   /**
    * Устанавливает шаблонный блок выписок СРО
    *
    * @param extracts - блок выписок СРО
    */
   public setExtracts(extracts: TemplateBlock): void {
      this.extracts = extracts;
   }
}
