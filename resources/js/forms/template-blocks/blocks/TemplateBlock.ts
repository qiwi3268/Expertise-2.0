import { mQS, safeDataAttrGetter } from '../../../lib/main';
import { TemplatePart } from '../parts/TemplatePart';
import { FormJSON } from '../../blocks/FormBlock';
import { FieldStates } from '../../fields/Field';

/**
 * Представляет собой блок страницы с шаблоном
 */
export abstract class TemplateBlock {

   /**
    * Элемент шаблонного блока
    */
   protected element: HTMLElement;

   /**
    * Элемент, в который добавляются шаблонные части
    */
   protected body: HTMLElement;

   /**
    * Элемент, в котором хранятся шаблоны
    */
   protected templatesContainer: HTMLElement;

   /**
    * Кнопка для добавления части
    */
   protected addPartButton: HTMLElement;

   /**
    * Тип блока
    */
   protected type: string;

   /**
    * Колбэк создания части
    */
   protected partCreation: Function;

   /**
    * Флаг, указывающий, был ли изменен блок
    */
   protected isPartsChanged = false;

   protected constructor(element: HTMLElement) {
      this.element = element;
      this.type = safeDataAttrGetter('type', element);
      this.body = mQS('[data-template-body]', this.element);
      this.templatesContainer = mQS('[data-templates-container]', this.element);
      this.handleAddPartButton();
   }

   /**
    * Устанавливает колбэк создания части
    *
    * @param partCreation - колбэк создания части
    */
   public setPartCreation(partCreation: Function): void {
      this.partCreation = partCreation;
   }

   /**
    * Обрабатывает кнопку создания части
    */
   protected handleAddPartButton(): void {
      this.addPartButton = mQS('[data-template-add]', this.element);
      this.addPartButton.addEventListener('click', () => this.createPart());
   }

   /**
    * Создает часть
    */
   protected createPart(): void {
      this.partCreation();
   }

   /**
    * Копирует элемент из контейнера шаблонов и добавляет в элемент
    *
    * @param mainBlock - элемент, в который добавляется шаблон
    * @param selector - селектор, для поиска шаблона
    * @return скопированный элемент
    */
   public createBlock(mainBlock: HTMLElement, selector: string): HTMLElement {
      const template: HTMLElement = mQS(selector, this.templatesContainer);
      const newBlock: HTMLElement = template.cloneNode(true) as HTMLElement;
      mainBlock.appendChild(newBlock);
      return newBlock;
   }

   /**
    * Сохраняет шаблонную часть
    *
    * @param part - сохраняемая часть
    */
   public abstract savePart(part: TemplatePart): void;

   /**
    * Определяет, является ли блок валидным
    */
   public abstract isValid(): boolean;

   /**
    * Определяет сохранен ли блок
    */
   public abstract isSaved(): boolean;

   /**
    * Получает блок в виде формы в формате JSON
    */
   public abstract toJSON(): FormJSON;

   /**
    * Получает тип блока
    */
   public getType(): string {
      return this.type;
   }

   /**
    * Устанавливает флаг сохранения блока
    *
    * @param isChanged - флаг сохранения
    */
   public setIsChanged(isChanged: boolean): void {
      this.isPartsChanged = isChanged;
   }

   /**
    * Определяет, был ли изменен блок
    */
   public isChanged(): boolean {
      return this.isPartsChanged;
   }

   /**
    * Валидирует блок
    */
   protected complete(): void {
      this.element.dataset.state = this.isValid() ? FieldStates.Valid : FieldStates.Invalid;
   }

   /**
    * Получает элемент блока
    */
   public getElement(): HTMLElement {
      return this.element;
   }

   /**
    * Получает элемент с шаблонными частями
    */
   public getBody(): HTMLElement {
      return this.body;
   }
}
